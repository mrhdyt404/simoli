/**
 * SIMOLI Enterprise Theme Manager
 * Comprehensive Color Theming Engine for PTPN IV Land Application Monitoring System
 */

(function(window, document) {
    'use strict';

    var STORAGE_KEY = 'simoli_custom_theme_v1';

    // 7 Curated Enterprise Color Palettes
    var THEME_PRESETS = {
        emerald: {
            id: 'emerald',
            name: 'Zamrud PTPN',
            badge: 'Standar PTPN IV',
            primary: '#16a34a',
            primaryHover: '#15803d',
            secondary: '#d4a017',
            accent: '#22c55e',
            chartColors: ['#16a34a', '#d97706', '#0284c7', '#8b5cf6'],
            variables: {
                '--theme-primary': '#16a34a',
                '--theme-primary-hover': '#15803d',
                '--theme-primary-subtle': '#f0fdf4',
                '--theme-primary-light': '#dcfce7',
                '--theme-primary-200': '#bbf7d0',
                '--theme-primary-300': '#86efac',
                '--theme-accent': '#4ade80',
                '--theme-accent-500': '#22c55e',
                '--theme-primary-800': '#166534',
                '--theme-primary-dark': '#14532d',
                '--theme-primary-darker': '#052e16',
                '--theme-secondary': '#d4a017',
                '--theme-glow': 'rgba(34, 197, 94, 0.35)',
                '--theme-border': 'rgba(22, 163, 74, 0.18)',
                '--theme-hero-from': '#030d07',
                '--theme-hero-mid': '#0a2317',
                '--theme-hero-to': '#0e3b26',
                '--theme-hero-accent': '#16a34a',
                '--theme-hero-border': 'rgba(34, 197, 94, 0.25)',
                '--theme-sidebar-from': '#030d07',
                '--theme-sidebar-mid': '#0a2317',
                '--theme-sidebar-to': '#0e3b26',
                '--theme-sidebar-border': 'rgba(34, 197, 94, 0.12)'
            }
        },
        sapphire: {
            id: 'sapphire',
            name: 'Biru Samudera',
            badge: 'Executive Sapphire',
            primary: '#2563eb',
            primaryHover: '#1d4ed8',
            secondary: '#0ea5e9',
            accent: '#3b82f6',
            chartColors: ['#2563eb', '#0ea5e9', '#f59e0b', '#10b981'],
            variables: {
                '--theme-primary': '#2563eb',
                '--theme-primary-hover': '#1d4ed8',
                '--theme-primary-subtle': '#eff6ff',
                '--theme-primary-light': '#dbeafe',
                '--theme-primary-200': '#bfdbfe',
                '--theme-primary-300': '#93c5fd',
                '--theme-accent': '#60a5fa',
                '--theme-accent-500': '#3b82f6',
                '--theme-primary-800': '#1e40af',
                '--theme-primary-dark': '#1e3a8a',
                '--theme-primary-darker': '#0f172a',
                '--theme-secondary': '#0ea5e9',
                '--theme-glow': 'rgba(59, 130, 246, 0.35)',
                '--theme-border': 'rgba(37, 99, 235, 0.18)',
                '--theme-hero-from': '#020617',
                '--theme-hero-mid': '#0b192c',
                '--theme-hero-to': '#172554',
                '--theme-hero-accent': '#2563eb',
                '--theme-hero-border': 'rgba(59, 130, 246, 0.25)',
                '--theme-sidebar-from': '#020617',
                '--theme-sidebar-mid': '#0b192c',
                '--theme-sidebar-to': '#172554',
                '--theme-sidebar-border': 'rgba(59, 130, 246, 0.15)'
            }
        },
        amethyst: {
            id: 'amethyst',
            name: 'Ungu Nebula',
            badge: 'Midnight Violet',
            primary: '#7c3aed',
            primaryHover: '#6d28d9',
            secondary: '#ec4899',
            accent: '#8b5cf6',
            chartColors: ['#7c3aed', '#ec4899', '#06b6d4', '#10b981'],
            variables: {
                '--theme-primary': '#7c3aed',
                '--theme-primary-hover': '#6d28d9',
                '--theme-primary-subtle': '#faf5ff',
                '--theme-primary-light': '#f3e8ff',
                '--theme-primary-200': '#e9d5ff',
                '--theme-primary-300': '#d8b4fe',
                '--theme-accent': '#c084fc',
                '--theme-accent-500': '#8b5cf6',
                '--theme-primary-800': '#5b21b6',
                '--theme-primary-dark': '#4c1d95',
                '--theme-primary-darker': '#1e1136',
                '--theme-secondary': '#ec4899',
                '--theme-glow': 'rgba(139, 92, 246, 0.35)',
                '--theme-border': 'rgba(124, 58, 237, 0.18)',
                '--theme-hero-from': '#090414',
                '--theme-hero-mid': '#1e1136',
                '--theme-hero-to': '#2e1065',
                '--theme-hero-accent': '#7c3aed',
                '--theme-hero-border': 'rgba(168, 85, 247, 0.25)',
                '--theme-sidebar-from': '#090414',
                '--theme-sidebar-mid': '#1e1136',
                '--theme-sidebar-to': '#2e1065',
                '--theme-sidebar-border': 'rgba(168, 85, 247, 0.15)'
            }
        },
        amber: {
            id: 'amber',
            name: 'Emas Tembaga',
            badge: 'Sunset Amber',
            primary: '#d97706',
            primaryHover: '#b45309',
            secondary: '#f59e0b',
            accent: '#fbbf24',
            chartColors: ['#d97706', '#10b981', '#3b82f6', '#ef4444'],
            variables: {
                '--theme-primary': '#d97706',
                '--theme-primary-hover': '#b45309',
                '--theme-primary-subtle': '#fffbeb',
                '--theme-primary-light': '#fef3c7',
                '--theme-primary-200': '#fde68a',
                '--theme-primary-300': '#fcd34d',
                '--theme-accent': '#fbbf24',
                '--theme-accent-500': '#f59e0b',
                '--theme-primary-800': '#92400e',
                '--theme-primary-dark': '#78350f',
                '--theme-primary-darker': '#1c0d02',
                '--theme-secondary': '#f59e0b',
                '--theme-glow': 'rgba(245, 158, 11, 0.35)',
                '--theme-border': 'rgba(217, 119, 6, 0.18)',
                '--theme-hero-from': '#140902',
                '--theme-hero-mid': '#291505',
                '--theme-hero-to': '#451a03',
                '--theme-hero-accent': '#d97706',
                '--theme-hero-border': 'rgba(245, 158, 11, 0.25)',
                '--theme-sidebar-from': '#140902',
                '--theme-sidebar-mid': '#291505',
                '--theme-sidebar-to': '#451a03',
                '--theme-sidebar-border': 'rgba(245, 158, 11, 0.15)'
            }
        },
        teal: {
            id: 'teal',
            name: 'Hijau Teal',
            badge: 'Tropical Matrix',
            primary: '#0d9488',
            primaryHover: '#0f766e',
            secondary: '#06b6d4',
            accent: '#14b8a6',
            chartColors: ['#0d9488', '#3b82f6', '#f59e0b', '#8b5cf6'],
            variables: {
                '--theme-primary': '#0d9488',
                '--theme-primary-hover': '#0f766e',
                '--theme-primary-subtle': '#f0fdfa',
                '--theme-primary-light': '#ccfbf1',
                '--theme-primary-200': '#99f6e4',
                '--theme-primary-300': '#5eead4',
                '--theme-accent': '#2dd4bf',
                '--theme-accent-500': '#14b8a6',
                '--theme-primary-800': '#115e59',
                '--theme-primary-dark': '#134e4a',
                '--theme-primary-darker': '#042f2e',
                '--theme-secondary': '#06b6d4',
                '--theme-glow': 'rgba(20, 184, 166, 0.35)',
                '--theme-border': 'rgba(13, 148, 136, 0.18)',
                '--theme-hero-from': '#021a18',
                '--theme-hero-mid': '#042f2e',
                '--theme-hero-to': '#0f4945',
                '--theme-hero-accent': '#0d9488',
                '--theme-hero-border': 'rgba(20, 184, 166, 0.25)',
                '--theme-sidebar-from': '#021a18',
                '--theme-sidebar-mid': '#042f2e',
                '--theme-sidebar-to': '#0f4945',
                '--theme-sidebar-border': 'rgba(20, 184, 166, 0.15)'
            }
        },
        slate: {
            id: 'slate',
            name: 'Obsidian Slate',
            badge: 'Modern Graphite',
            primary: '#475569',
            primaryHover: '#334155',
            secondary: '#0284c7',
            accent: '#64748b',
            chartColors: ['#475569', '#0284c7', '#10b981', '#f59e0b'],
            variables: {
                '--theme-primary': '#475569',
                '--theme-primary-hover': '#334155',
                '--theme-primary-subtle': '#f8fafc',
                '--theme-primary-light': '#e2e8f0',
                '--theme-primary-200': '#cbd5e1',
                '--theme-primary-300': '#94a3b8',
                '--theme-accent': '#64748b',
                '--theme-accent-500': '#475569',
                '--theme-primary-800': '#1e293b',
                '--theme-primary-dark': '#0f172a',
                '--theme-primary-darker': '#020617',
                '--theme-secondary': '#0284c7',
                '--theme-glow': 'rgba(100, 116, 139, 0.35)',
                '--theme-border': 'rgba(71, 85, 105, 0.18)',
                '--theme-hero-from': '#020617',
                '--theme-hero-mid': '#0f172a',
                '--theme-hero-to': '#1e293b',
                '--theme-hero-accent': '#475569',
                '--theme-hero-border': 'rgba(100, 116, 139, 0.25)',
                '--theme-sidebar-from': '#020617',
                '--theme-sidebar-mid': '#0f172a',
                '--theme-sidebar-to': '#1e293b',
                '--theme-sidebar-border': 'rgba(100, 116, 139, 0.15)'
            }
        },
        rose: {
            id: 'rose',
            name: 'Rose Ruby',
            badge: 'Crimson Elite',
            primary: '#e11d48',
            primaryHover: '#be123c',
            secondary: '#f43f5e',
            accent: '#f43f5e',
            chartColors: ['#e11d48', '#8b5cf6', '#f59e0b', '#06b6d4'],
            variables: {
                '--theme-primary': '#e11d48',
                '--theme-primary-hover': '#be123c',
                '--theme-primary-subtle': '#fff1f2',
                '--theme-primary-light': '#ffe4e6',
                '--theme-primary-200': '#fecdd3',
                '--theme-primary-300': '#fda4af',
                '--theme-accent': '#fb7185',
                '--theme-accent-500': '#f43f5e',
                '--theme-primary-800': '#9f1239',
                '--theme-primary-dark': '#881337',
                '--theme-primary-darker': '#26020c',
                '--theme-secondary': '#f43f5e',
                '--theme-glow': 'rgba(244, 63, 94, 0.35)',
                '--theme-border': 'rgba(225, 29, 72, 0.18)',
                '--theme-hero-from': '#130106',
                '--theme-hero-mid': '#26020c',
                '--theme-hero-to': '#4c0519',
                '--theme-hero-accent': '#e11d48',
                '--theme-hero-border': 'rgba(244, 63, 94, 0.25)',
                '--theme-sidebar-from': '#130106',
                '--theme-sidebar-mid': '#26020c',
                '--theme-sidebar-to': '#4c0519',
                '--theme-sidebar-border': 'rgba(244, 63, 94, 0.15)'
            }
        }
    };

    // Color conversion utilities
    function hexToRgb(hex) {
        hex = hex.replace(/^#/, '');
        if (hex.length === 3) {
            hex = hex.split('').map(function(c) { return c + c; }).join('');
        }
        var num = parseInt(hex, 16);
        return {
            r: (num >> 16) & 255,
            g: (num >> 8) & 255,
            b: num & 255
        };
    }

    function rgbToHex(r, g, b) {
        return '#' + [r, g, b].map(function(x) {
            var hex = Math.max(0, Math.min(255, Math.round(x))).toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }).join('');
    }

    function hexToHsl(hex) {
        var rgb = hexToRgb(hex);
        var r = rgb.r / 255, g = rgb.g / 255, b = rgb.b / 255;
        var max = Math.max(r, g, b), min = Math.min(r, g, b);
        var h, s, l = (max + min) / 2;

        if (max === min) {
            h = s = 0;
        } else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        return { h: h * 360, s: s * 100, l: l * 100 };
    }

    function hslToHex(h, s, l) {
        h = (h % 360 + 360) % 360 / 360;
        s = Math.max(0, Math.min(100, s)) / 100;
        l = Math.max(0, Math.min(100, l)) / 100;

        var r, g, b;
        if (s === 0) {
            r = g = b = l;
        } else {
            function hue2rgb(p, q, t) {
                if (t < 0) t += 1;
                if (t > 1) t -= 1;
                if (t < 1/6) return p + (q - p) * 6 * t;
                if (t < 1/2) return q;
                if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                return p;
            }
            var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            var p = 2 * l - q;
            r = hue2rgb(p, q, h + 1/3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1/3);
        }
        return rgbToHex(r * 255, g * 255, b * 255);
    }

    // Generate full palette variables dynamically from single hex
    function generateCustomPalette(hex) {
        var hsl = hexToHsl(hex);
        var h = hsl.h;
        var s = hsl.s;

        var p50  = hslToHex(h, Math.max(20, s * 0.4), 97);
        var p100 = hslToHex(h, Math.max(30, s * 0.6), 92);
        var p200 = hslToHex(h, Math.max(40, s * 0.7), 84);
        var p300 = hslToHex(h, Math.max(50, s * 0.8), 73);
        var p400 = hslToHex(h, s, 60);
        var p500 = hslToHex(h, s, 50);
        var p600 = hex;
        var p700 = hslToHex(h, s, 36);
        var p800 = hslToHex(h, s, 26);
        var p900 = hslToHex(h, s, 18);
        var p950 = hslToHex(h, Math.min(100, s * 1.1), 8);

        var darkFrom = hslToHex(h, Math.min(100, s * 1.2), 4);
        var darkMid  = hslToHex(h, Math.min(100, s * 1.1), 8);
        var darkTo   = hslToHex(h, s, 14);

        var rgb = hexToRgb(hex);

        return {
            id: 'custom',
            name: 'Kustom (' + hex.toUpperCase() + ')',
            badge: 'Warna Kustom',
            primary: hex,
            primaryHover: p700,
            secondary: hslToHex((h + 40) % 360, s, 45),
            accent: p400,
            chartColors: [hex, hslToHex((h + 45) % 360, s, 45), hslToHex((h + 180) % 360, s, 50), '#f59e0b'],
            variables: {
                '--theme-primary': hex,
                '--theme-primary-hover': p700,
                '--theme-primary-subtle': p50,
                '--theme-primary-light': p100,
                '--theme-primary-200': p200,
                '--theme-primary-300': p300,
                '--theme-accent': p400,
                '--theme-accent-500': p500,
                '--theme-primary-800': p800,
                '--theme-primary-dark': p900,
                '--theme-primary-darker': p950,
                '--theme-secondary': hslToHex((h + 40) % 360, s, 45),
                '--theme-glow': 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', 0.35)',
                '--theme-border': 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', 0.18)',
                '--theme-hero-from': darkFrom,
                '--theme-hero-mid': darkMid,
                '--theme-hero-to': darkTo,
                '--theme-hero-accent': hex,
                '--theme-hero-border': 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', 0.25)',
                '--theme-sidebar-from': darkFrom,
                '--theme-sidebar-mid': darkMid,
                '--theme-sidebar-to': darkTo,
                '--theme-sidebar-border': 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', 0.15)'
            }
        };
    }

    // Main Theme Controller
    var SimoliTheme = {
        presets: THEME_PRESETS,
        currentConfig: null,

        // Initialize from localStorage or defaults
        init: function() {
            var raw = localStorage.getItem(STORAGE_KEY);
            var config = null;

            if (raw) {
                try {
                    config = JSON.parse(raw);
                } catch(e) {
                    console.warn('[SimoliTheme] Corrupted settings, resetting:', e);
                }
            }

            if (!config) {
                config = {
                    themeKey: 'emerald',
                    customHex: '#16a34a',
                    mode: 'light' // 'light' | 'dark' | 'auto'
                };
            }

            this.apply(config, false);
            this.bindSystemThemeWatcher();
            this.syncUI();
        },

        // Apply theme settings
        apply: function(config, save) {
            if (!config) return;
            var themeData;

            if (config.themeKey === 'custom' && config.customHex) {
                themeData = generateCustomPalette(config.customHex);
            } else if (THEME_PRESETS[config.themeKey]) {
                themeData = THEME_PRESETS[config.themeKey];
            } else {
                themeData = THEME_PRESETS.emerald;
                config.themeKey = 'emerald';
            }

            var root = document.documentElement;

            // Apply CSS Variables
            if (themeData && themeData.variables) {
                for (var prop in themeData.variables) {
                    root.style.setProperty(prop, themeData.variables[prop]);
                }
            }

            // Apply Theme Attribute
            root.setAttribute('data-simoli-theme', config.themeKey);

            // Apply Dark/Light Skin
            var isDark = false;
            if (config.mode === 'dark') {
                isDark = true;
            } else if (config.mode === 'auto') {
                isDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            }

            if (isDark) {
                root.classList.add('app-skin-dark');
            } else {
                root.classList.remove('app-skin-dark');
            }

            // Update meta theme-color
            var metaTheme = document.querySelector('meta[name="theme-color"]');
            if (metaTheme) {
                metaTheme.setAttribute('content', isDark ? (themeData.variables['--theme-hero-from'] || '#030d07') : themeData.primary);
            }

            this.currentConfig = {
                themeKey: config.themeKey,
                customHex: config.customHex || themeData.primary,
                mode: config.mode || 'light',
                themeData: themeData
            };

            if (save !== false) {
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify({
                        themeKey: this.currentConfig.themeKey,
                        customHex: this.currentConfig.customHex,
                        mode: this.currentConfig.mode
                    }));
                } catch(e) {
                    console.warn('[SimoliTheme] Failed to save localStorage:', e);
                }
            }

            // Dispatch global event for ApexCharts and other reactive components
            var evt;
            var eventPayload = {
                theme: this.currentConfig.themeKey,
                mode: this.currentConfig.mode,
                isDark: isDark,
                primary: themeData.primary,
                primaryHover: themeData.primaryHover,
                secondary: themeData.secondary,
                accent: themeData.accent,
                chartColors: themeData.chartColors,
                variables: themeData.variables
            };

            try {
                evt = new CustomEvent('simoli:theme-changed', { detail: eventPayload });
            } catch(e) {
                evt = document.createEvent('CustomEvent');
                evt.initCustomEvent('simoli:theme-changed', true, true, eventPayload);
            }
            window.dispatchEvent(evt);

            this.syncUI();
        },

        // Set preset
        setPreset: function(key) {
            if (!THEME_PRESETS[key]) return;
            var cfg = {
                themeKey: key,
                customHex: THEME_PRESETS[key].primary,
                mode: this.currentConfig ? this.currentConfig.mode : 'light'
            };
            this.apply(cfg, true);
            this.showToast('Tema ' + THEME_PRESETS[key].name + ' berhasil diterapkan!');
        },

        // Set custom hex color
        setCustomHex: function(hex) {
            if (!/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/.test(hex)) return;
            var cfg = {
                themeKey: 'custom',
                customHex: hex,
                mode: this.currentConfig ? this.currentConfig.mode : 'light'
            };
            this.apply(cfg, true);
            this.showToast('Tema Kustom ' + hex.toUpperCase() + ' berhasil diterapkan!');
        },

        // Set mode (light/dark/auto)
        setMode: function(mode) {
            if (!['light', 'dark', 'auto'].includes(mode)) mode = 'light';
            var cfg = {
                themeKey: this.currentConfig ? this.currentConfig.themeKey : 'emerald',
                customHex: this.currentConfig ? this.currentConfig.customHex : '#16a34a',
                mode: mode
            };
            this.apply(cfg, true);
            var modeLabels = { light: 'Mode Terang', dark: 'Mode Gelap', auto: 'Mode Otomatis' };
            this.showToast(modeLabels[mode] + ' diaktifkan');
        },

        // Reset to PTPN Standard
        resetToDefault: function() {
            var cfg = {
                themeKey: 'emerald',
                customHex: '#16a34a',
                mode: 'light'
            };
            this.apply(cfg, true);
            this.showToast('Tema direset ke Standar PTPN IV (Zamrud Palm)');
        },

        // Open offcanvas drawer
        openCustomizer: function() {
            var el = document.getElementById('simoliThemeDrawer');
            var overlay = document.getElementById('simoliThemeOverlay');
            if (el) el.classList.add('open');
            if (overlay) overlay.classList.add('open');
            this.syncUI();
        },

        // Close offcanvas drawer
        closeCustomizer: function() {
            var el = document.getElementById('simoliThemeDrawer');
            var overlay = document.getElementById('simoliThemeOverlay');
            if (el) el.classList.remove('open');
            if (overlay) overlay.classList.remove('open');
        },

        // Sync drawer UI states
        syncUI: function() {
            if (!this.currentConfig) return;
            var cur = this.currentConfig;

            // Sync preset swatches
            document.querySelectorAll('.theme-swatch-item').forEach(function(btn) {
                var key = btn.getAttribute('data-theme-key');
                if (key === cur.themeKey) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Sync color picker inputs
            var colorPicker = document.getElementById('customColorPickerInput');
            var colorText   = document.getElementById('customColorTextInput');
            if (colorPicker) colorPicker.value = cur.customHex || '#16a34a';
            if (colorText)   colorText.value   = (cur.customHex || '#16a34a').toUpperCase();

            // Sync mode buttons
            document.querySelectorAll('.theme-mode-btn').forEach(function(btn) {
                var m = btn.getAttribute('data-mode');
                if (m === cur.mode) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Sync Live Preview Card
            var previewName  = document.getElementById('themePreviewName');
            var previewPill  = document.getElementById('themePreviewBadge');
            var previewHero  = document.getElementById('themePreviewHero');
            var previewBtn   = document.getElementById('themePreviewBtn');

            var curThemeData = cur.themeData || THEME_PRESETS[cur.themeKey] || generateCustomPalette(cur.customHex);
            if (previewName) previewName.textContent = curThemeData.name;
            if (previewPill) previewPill.textContent = curThemeData.badge;
            if (previewHero && curThemeData.variables) {
                previewHero.style.background = 'linear-gradient(135deg, ' +
                    curThemeData.variables['--theme-hero-from'] + ' 0%, ' +
                    curThemeData.variables['--theme-hero-mid'] + ' 50%, ' +
                    curThemeData.variables['--theme-hero-accent'] + ' 100%)';
            }
            if (previewBtn && curThemeData.variables) {
                previewBtn.style.background = 'linear-gradient(135deg, ' +
                    curThemeData.variables['--theme-primary-hover'] + ' 0%, ' +
                    curThemeData.variables['--theme-primary'] + ' 100%)';
            }
        },

        // Watch OS theme change if in 'auto' mode
        bindSystemThemeWatcher: function() {
            var self = this;
            if (window.matchMedia) {
                var query = window.matchMedia('(prefers-color-scheme: dark)');
                query.addEventListener('change', function(e) {
                    if (self.currentConfig && self.currentConfig.mode === 'auto') {
                        if (e.matches) {
                            document.documentElement.classList.add('app-skin-dark');
                        } else {
                            document.documentElement.classList.remove('app-skin-dark');
                        }
                        self.apply(self.currentConfig, false);
                    }
                });
            }
        },

        // Toast feedback
        showToast: function(msg) {
            if (window.Swal) {
                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2400,
                    timerProgressBar: true,
                    background: document.documentElement.classList.contains('app-skin-dark') ? '#0a2317' : '#ffffff',
                    color: document.documentElement.classList.contains('app-skin-dark') ? '#d1fae5' : '#14532d'
                });
                Toast.fire({
                    icon: 'success',
                    title: msg
                });
            }
        }
    };

    window.SimoliTheme = SimoliTheme;

    // Auto-init on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { SimoliTheme.init(); });
    } else {
        SimoliTheme.init();
    }

})(window, document);
