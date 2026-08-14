/**
 * SIMOLI IndexedDB Wrapper for Offline-First Storage
 * Designed for Smartphones (Zero External Dependencies)
 */
const SimoliDB = (() => {
    const DB_NAME = 'SimoliOfflineDB';
    const DB_VERSION = 1;
    let dbInstance = null;

    function openDB() {
        if (dbInstance) return Promise.resolve(dbInstance);

        return new Promise((resolve, reject) => {
            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // 1. Sync Outbox Queue
                if (!db.objectStoreNames.contains('sync_queue')) {
                    const queueStore = db.createObjectStore('sync_queue', { keyPath: 'uuid' });
                    queueStore.createIndex('status', 'status', { unique: false });
                    queueStore.createIndex('created_at', 'created_at', { unique: false });
                }

                // 2. Master Data Alat Berat
                if (!db.objectStoreNames.contains('master_alat_berat')) {
                    const alatStore = db.createObjectStore('master_alat_berat', { keyPath: 'id' });
                    alatStore.createIndex('id_pks', 'id_pks', { unique: false });
                    alatStore.createIndex('kode_alat', 'kode_alat', { unique: false });
                }

                // 3. Master Data PKS
                if (!db.objectStoreNames.contains('master_pks')) {
                    db.createObjectStore('master_pks', { keyPath: 'id_pks' });
                }

                // 4. Cached Recent Reports
                if (!db.objectStoreNames.contains('cached_reports')) {
                    const reportsStore = db.createObjectStore('cached_reports', { keyPath: 'uuid' });
                    reportsStore.createIndex('tanggal', 'tanggal', { unique: false });
                }

                // 5. Config & Offline Session
                if (!db.objectStoreNames.contains('app_config')) {
                    db.createObjectStore('app_config', { keyPath: 'key' });
                }
            };

            request.onsuccess = (event) => {
                dbInstance = event.target.result;
                resolve(dbInstance);
            };

            request.onerror = (event) => {
                console.error('[SimoliDB] Error opening database:', event.target.error);
                reject(event.target.error);
            };
        });
    }

    // Helper: Transaction promise
    async function tx(storeName, mode, callback) {
        const db = await openDB();
        return new Promise((resolve, reject) => {
            const transaction = db.transaction(storeName, mode);
            const store = transaction.objectStore(storeName);
            let result;

            transaction.oncomplete = () => resolve(result);
            transaction.onerror = (e) => reject(e.target.error);

            result = callback(store);
        });
    }

    return {
        async init() {
            return await openDB();
        },

        // --- Config / Device ID ---
        async setConfig(key, value) {
            return tx('app_config', 'readwrite', (store) => {
                store.put({ key, value, updated_at: new Date().toISOString() });
            });
        },

        async getConfig(key) {
            const db = await openDB();
            return new Promise((resolve) => {
                const transaction = db.transaction('app_config', 'readonly');
                const store = transaction.objectStore('app_config');
                const req = store.get(key);
                req.onsuccess = () => resolve(req.result ? req.result.value : null);
                req.onerror = () => resolve(null);
            });
        },

        async getOrCreateDeviceId() {
            let deviceId = await this.getConfig('device_id');
            if (!deviceId) {
                deviceId = 'simoli_dev_' + (crypto.randomUUID ? crypto.randomUUID() : Math.random().toString(36).substring(2) + Date.now());
                await this.setConfig('device_id', deviceId);
            }
            return deviceId;
        },

        // --- Sync Queue (Outbox) ---
        async addToQueue(item) {
            if (!item.uuid) {
                item.uuid = crypto.randomUUID ? crypto.randomUUID() : 'uuid_' + Date.now() + '_' + Math.floor(Math.random() * 10000);
            }
            item.status = 'pending';
            item.created_at = item.created_at || new Date().toISOString();
            item.retry_count = 0;

            await tx('sync_queue', 'readwrite', (store) => {
                store.put(item);
            });

            // Also keep in cached reports for offline listing
            await this.saveCachedReport(item);

            return item;
        },

        async getPendingQueue() {
            const db = await openDB();
            return new Promise((resolve) => {
                const transaction = db.transaction('sync_queue', 'readonly');
                const store = transaction.objectStore('sync_queue');
                const req = store.getAll();
                req.onsuccess = () => {
                    const items = req.result || [];
                    resolve(items.filter(i => i.status === 'pending' || i.status === 'failed'));
                };
                req.onerror = () => resolve([]);
            });
        },

        async getQueueCount() {
            const items = await this.getPendingQueue();
            return items.length;
        },

        async markAsSynced(uuids) {
            const db = await openDB();
            return new Promise((resolve, reject) => {
                const transaction = db.transaction(['sync_queue', 'cached_reports'], 'readwrite');
                const queueStore = transaction.objectStore('sync_queue');
                const reportsStore = transaction.objectStore('cached_reports');

                uuids.forEach((uuid) => {
                    queueStore.delete(uuid);
                    // Update sync flag in cached report
                    const getReq = reportsStore.get(uuid);
                    getReq.onsuccess = () => {
                        if (getReq.result) {
                            const rep = getReq.result;
                            rep.is_synced = true;
                            rep.synced_at = new Date().toISOString();
                            reportsStore.put(rep);
                        }
                    };
                });

                transaction.oncomplete = () => resolve(true);
                transaction.onerror = (e) => reject(e.target.error);
            });
        },

        // --- Master Data ---
        async saveMasterAlatBerat(list) {
            return tx('master_alat_berat', 'readwrite', (store) => {
                store.clear();
                list.forEach(item => store.put(item));
            });
        },

        async getMasterAlatBerat(idPks = null) {
            const db = await openDB();
            return new Promise((resolve) => {
                const transaction = db.transaction('master_alat_berat', 'readonly');
                const store = transaction.objectStore('master_alat_berat');
                const req = store.getAll();
                req.onsuccess = () => {
                    let items = req.result || [];
                    if (idPks) {
                        items = items.filter(a => String(a.id_pks) === String(idPks));
                    }
                    resolve(items);
                };
                req.onerror = () => resolve([]);
            });
        },

        async saveMasterPks(list) {
            return tx('master_pks', 'readwrite', (store) => {
                store.clear();
                list.forEach(item => store.put(item));
            });
        },

        async getMasterPks() {
            const db = await openDB();
            return new Promise((resolve) => {
                const transaction = db.transaction('master_pks', 'readonly');
                const store = transaction.objectStore('master_pks');
                const req = store.getAll();
                req.onsuccess = () => resolve(req.result || []);
                req.onerror = () => resolve([]);
            });
        },

        // --- Cached Reports ---
        async saveCachedReport(report) {
            return tx('cached_reports', 'readwrite', (store) => {
                store.put(report);
            });
        },

        async saveCachedReports(list) {
            return tx('cached_reports', 'readwrite', (store) => {
                list.forEach(item => {
                    const entry = { ...item, uuid: item.uuid || 'server_' + item.id, is_synced: true };
                    store.put(entry);
                });
            });
        },

        async getCachedReports() {
            const db = await openDB();
            return new Promise((resolve) => {
                const transaction = db.transaction('cached_reports', 'readonly');
                const store = transaction.objectStore('cached_reports');
                const req = store.getAll();
                req.onsuccess = () => {
                    const items = req.result || [];
                    // Sort by tanggal / created_at desc
                    items.sort((a, b) => new Date(b.created_at || b.tanggal) - new Date(a.created_at || a.tanggal));
                    resolve(items);
                };
                req.onerror = () => resolve([]);
            });
        }
    };
})();
window.SimoliDB = SimoliDB;
