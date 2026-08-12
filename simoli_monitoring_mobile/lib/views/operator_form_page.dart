import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:geolocator/geolocator.dart';
import 'package:intl/intl.dart';
import '../models/user_model.dart';
import '../models/monitoring_alat_model.dart';
import '../services/api_service.dart';
import 'login_page.dart';

class OperatorFormPage extends StatefulWidget {
  final UserModel user;

  const OperatorFormPage({super.key, required this.user});

  @override
  State<OperatorFormPage> createState() => _OperatorFormPageState();
}

class _OperatorFormPageState extends State<OperatorFormPage> {
  final _formKey = GlobalKey<FormState>();

  int? _selectedAlatId;
  DateTime _selectedDate = DateTime.now();
  final _operatorController = TextEditingController();
  final _kegiatanController = TextEditingController();
  final _lokasiBlokController = TextEditingController();
  final _hmAwalController = TextEditingController();
  final _hmAkhirController = TextEditingController();
  final _bbmController = TextEditingController();
  final _catatanController = TextEditingController();

  String _kondisiAlat = 'Baik';
  double? _latitude;
  double? _longitude;
  bool _isGettingLocation = false;
  bool _isSubmitting = false;

  File? _fotoSebelum;
  File? _fotoSesudah;

  List<AlatBeratMasterModel> _alatList = [];
  bool _isLoadingMaster = true;

  @override
  void initState() {
    super.initState();
    _operatorController.text = widget.user.username;
    _fetchMasterData();
    _getCurrentLocation();
  }

  Future<void> _fetchMasterData() async {
    setState(() => _isLoadingMaster = true);
    final data = await ApiService.getMasterData(idPks: widget.user.idPks);
    if (mounted) {
      setState(() {
        _alatList = data['alat_berat'] ?? [];
        if (_alatList.isNotEmpty) {
          _selectedAlatId = _alatList.first.id;
        }
        _isLoadingMaster = false;
      });
    }
  }

  Future<void> _getCurrentLocation() async {
    setState(() => _isGettingLocation = true);
    try {
      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }

      if (permission == LocationPermission.whileInUse || permission == LocationPermission.always) {
        Position position = await Geolocator.getCurrentPosition();
        if (mounted) {
          setState(() {
            _latitude = position.latitude;
            _longitude = position.longitude;
          });
        }
      }
    } catch (e) {
      print('Gagal mengambil GPS: $e');
    } finally {
      if (mounted) setState(() => _isGettingLocation = false);
    }
  }

  Future<void> _pickImage(bool isSebelum, ImageSource source) async {
    final picker = ImagePicker();
    final picked = await picker.pickImage(source: source, imageQuality: 75);
    if (picked != null) {
      setState(() {
        if (isSebelum) {
          _fotoSebelum = File(picked.path);
        } else {
          _fotoSesudah = File(picked.path);
        }
      });
    }
  }

  Future<void> _selectTime(TextEditingController controller) async {
    final TimeOfDay? picked = await showTimePicker(
      context: context,
      initialTime: TimeOfDay.now(),
    );
    if (picked != null) {
      final formatted = '${picked.hour.toString().padLeft(2, '0')}:${picked.minute.toString().padLeft(2, '0')}';
      controller.text = formatted;
    }
  }

  void _resetForm() {
    setState(() {
      _selectedDate = DateTime.now();
      _kegiatanController.clear();
      _lokasiBlokController.clear();
      _hmAwalController.clear();
      _hmAkhirController.clear();
      _bbmController.clear();
      _catatanController.clear();
      _kondisiAlat = 'Baik';
      _fotoSebelum = null;
      _fotoSesudah = null;
    });
    _getCurrentLocation();
  }

  Future<void> _submitForm() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedAlatId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Silakan pilih alat berat terlebih dahulu.')),
      );
      return;
    }

    setState(() => _isSubmitting = true);

    final res = await ApiService.postMonitoringAlatBerat(
      alatBeratId: _selectedAlatId!,
      tanggal: DateFormat('Y-MM-dd').format(_selectedDate),
      operator: _operatorController.text.trim(),
      kegiatan: _kegiatanController.text.trim(),
      lokasiBlok: _lokasiBlokController.text.trim(),
      kondisiAlat: _kondisiAlat,
      hmAwal: _hmAwalController.text.trim().isNotEmpty ? _hmAwalController.text.trim() : null,
      hmAkhir: _hmAkhirController.text.trim().isNotEmpty ? _hmAkhirController.text.trim() : null,
      bbmLiter: double.tryParse(_bbmController.text.trim()),
      latitude: _latitude,
      longitude: _longitude,
      catatan: _catatanController.text.trim().isNotEmpty ? _catatanController.text.trim() : null,
      fotoSebelum: _fotoSebelum,
      fotoSesudah: _fotoSesudah,
    );

    if (!mounted) return;
    setState(() => _isSubmitting = false);

    if (res['success'] == true) {
      showDialog(
        context: context,
        builder: (ctx) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: const Row(
            children: [
              Icon(Icons.check_circle, color: Color(0xFF15803D), size: 28),
              SizedBox(width: 10),
              Text('Berhasil Dikirim!', style: TextStyle(fontWeight: FontWeight.bold)),
            ],
          ),
          content: const Text(
            'Laporan monitoring alat berat telah berhasil tersimpan ke sistem PKS.',
            style: TextStyle(fontSize: 14),
          ),
          actions: [
            ElevatedButton(
              onPressed: () {
                Navigator.pop(ctx);
                _resetForm();
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF15803D),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
              ),
              child: const Text('OK / Buat Laporan Baru', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Gagal mengirim laporan monitoring.'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _handleLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Konfirmasi Out'),
        content: const Text('Keluar dari akun Operator Lapangan?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Keluar', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );

    if (confirm == true) {
      await ApiService.clearToken();
      if (!mounted) return;
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const LoginPage()),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final pksName = widget.user.namaPks ?? 'UNIT PKS #${widget.user.idPks ?? "1"}';

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0F172A),
        elevation: 0,
        automaticallyImplyLeading: false,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'FORM MONITORING LAPANGAN',
              style: TextStyle(color: Colors.white, fontSize: 15, fontWeight: FontWeight.bold, letterSpacing: 0.5),
            ),
            Text(
              '$pksName • Operator: ${widget.user.username}',
              style: const TextStyle(color: Color(0xFF22C55E), fontSize: 12, fontWeight: FontWeight.w500),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.power_settings_new_rounded, color: Colors.redAccent),
            onPressed: _handleLogout,
            tooltip: 'Logout Operator',
          ),
        ],
      ),
      body: _isLoadingMaster
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Banner Info Operator PKS
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFF15803D), Color(0xFF065F46)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.precision_manufacturing, color: Colors.white, size: 28),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  pksName.toUpperCase(),
                                  style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14),
                                ),
                                const Text(
                                  'Input Log Operasional Alat Berat Unit',
                                  style: TextStyle(color: Colors.white70, fontSize: 12),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Card 1: Data Alat & Operasional
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(14),
                        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 10)],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('1. DATA ALAT BERAT & LOKASI', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF64748B))),
                          const SizedBox(height: 14),

                          // Alat Berat Dropdown
                          const Text('Pilih Alat Berat', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                          const SizedBox(height: 6),
                          DropdownButtonFormField<int>(
                            initialValue: _selectedAlatId,
                            isExpanded: true,
                            decoration: const InputDecoration(
                              border: OutlineInputBorder(),
                              contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                            ),
                            items: _alatList.map((alat) {
                              return DropdownMenuItem<int>(
                                value: alat.id,
                                child: Text('${alat.kodeAlat} - ${alat.jenisAlat} ${alat.merkModel ?? ""}'),
                              );
                            }).toList(),
                            onChanged: (val) {
                              setState(() => _selectedAlatId = val);
                            },
                          ),
                          const SizedBox(height: 14),

                          // Tanggal
                          const Text('Tanggal Operasional', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                          const SizedBox(height: 6),
                          InkWell(
                            onTap: () async {
                              final picked = await showDatePicker(
                                context: context,
                                initialDate: _selectedDate,
                                firstDate: DateTime(2020),
                                lastDate: DateTime(2030),
                              );
                              if (picked != null) setState(() => _selectedDate = picked);
                            },
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
                              decoration: BoxDecoration(
                                border: Border.all(color: Colors.grey.shade400),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(DateFormat('dd MMMM yyyy').format(_selectedDate)),
                                  const Icon(Icons.calendar_today, size: 18, color: Colors.grey),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(height: 14),

                          // Operator & Lokasi
                          TextFormField(
                            controller: _operatorController,
                            decoration: const InputDecoration(labelText: 'Nama Operator', border: OutlineInputBorder()),
                            validator: (v) => v == null || v.isEmpty ? 'Nama operator wajib diisi' : null,
                          ),
                          const SizedBox(height: 14),

                          TextFormField(
                            controller: _kegiatanController,
                            decoration: const InputDecoration(labelText: 'Kegiatan Operasional', hintText: 'Contoh: Clearing Bed / Maintenance Pipa', border: OutlineInputBorder()),
                            validator: (v) => v == null || v.isEmpty ? 'Kegiatan operasional wajib diisi' : null,
                          ),
                          const SizedBox(height: 14),

                          TextFormField(
                            controller: _lokasiBlokController,
                            decoration: const InputDecoration(labelText: 'Lokasi Blok Operasi', hintText: 'Contoh: Blok B12', border: OutlineInputBorder()),
                            validator: (v) => v == null || v.isEmpty ? 'Lokasi blok wajib diisi' : null,
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Card 2: Hour Meter (HM) & BBM & Kondisi
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(14),
                        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 10)],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('2. JAM KERJA (HM), BBM & KONDISI', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF64748B))),
                          const SizedBox(height: 14),

                          Row(
                            children: [
                              Expanded(
                                child: TextFormField(
                                  controller: _hmAwalController,
                                  readOnly: true,
                                  onTap: () => _selectTime(_hmAwalController),
                                  decoration: const InputDecoration(
                                    labelText: 'HM Awal (HH:MM)',
                                    suffixIcon: Icon(Icons.access_time),
                                    border: OutlineInputBorder(),
                                  ),
                                ),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: TextFormField(
                                  controller: _hmAkhirController,
                                  readOnly: true,
                                  onTap: () => _selectTime(_hmAkhirController),
                                  decoration: const InputDecoration(
                                    labelText: 'HM Akhir (HH:MM)',
                                    suffixIcon: Icon(Icons.access_time),
                                    border: OutlineInputBorder(),
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 14),

                          TextFormField(
                            controller: _bbmController,
                            keyboardType: TextInputType.number,
                            decoration: const InputDecoration(
                              labelText: 'Konsumsi BBM (Liter)',
                              suffixText: 'Liter',
                              border: OutlineInputBorder(),
                            ),
                          ),
                          const SizedBox(height: 14),

                          // Kondisi Alat
                          const Text('Kondisi Alat Berat', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                          const SizedBox(height: 6),
                          DropdownButtonFormField<String>(
                            initialValue: _kondisiAlat,
                            decoration: const InputDecoration(border: OutlineInputBorder()),
                            items: const [
                              DropdownMenuItem(value: 'Baik', child: Text('Baik (Siap Operasi)', style: TextStyle(color: Colors.green))),
                              DropdownMenuItem(value: 'Perbaikan', child: Text('Perbaikan (Maintenance)', style: TextStyle(color: Colors.orange))),
                              DropdownMenuItem(value: 'Rusak', child: Text('Rusak (Breakdown)', style: TextStyle(color: Colors.red))),
                            ],
                            onChanged: (val) {
                              if (val != null) setState(() => _kondisiAlat = val);
                            },
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Card 3: GPS Geolocation & Camera Photos
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(14),
                        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 10)],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('3. LOKASI GPS & DOKUMENTASI FOTO', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF64748B))),
                          const SizedBox(height: 14),

                          // GPS Info Box
                          Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: const Color(0xFFF1F5F9),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Row(
                              children: [
                                const Icon(Icons.my_location, color: Color(0xFF2563EB)),
                                const SizedBox(width: 10),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      const Text('Koordinat GPS Lapangan', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                                      Text(
                                        _latitude != null ? 'Lat: $_latitude, Long: $_longitude' : 'Deteksi koordinat GPS...',
                                        style: TextStyle(color: Colors.grey.shade700, fontSize: 12),
                                      ),
                                    ],
                                  ),
                                ),
                                IconButton(
                                  icon: _isGettingLocation
                                      ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2))
                                      : const Icon(Icons.refresh, color: Colors.blue),
                                  onPressed: _getCurrentLocation,
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 16),

                          // Photos Picker
                          Row(
                            children: [
                              Expanded(
                                child: _buildImagePickerCard(
                                  title: 'Foto Sebelum',
                                  file: _fotoSebelum,
                                  onPick: (source) => _pickImage(true, source),
                                ),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: _buildImagePickerCard(
                                  title: 'Foto Sesudah',
                                  file: _fotoSesudah,
                                  onPick: (source) => _pickImage(false, source),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 14),

                          TextFormField(
                            controller: _catatanController,
                            maxLines: 2,
                            decoration: const InputDecoration(
                              labelText: 'Catatan Tambahan Operator (Opsional)',
                              border: OutlineInputBorder(),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 24),

                    // Submit Button
                    SizedBox(
                      height: 54,
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: _isSubmitting ? null : _submitForm,
                        icon: _isSubmitting
                            ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                            : const Icon(Icons.send_rounded, color: Colors.white),
                        label: Text(
                          _isSubmitting ? 'MENGIRIM LAPORAN...' : 'KIRIM LAPORAN MONITORING',
                          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16, letterSpacing: 0.5),
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF15803D),
                          elevation: 3,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                      ),
                    ),
                    const SizedBox(height: 30),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildImagePickerCard({
    required String title,
    required File? file,
    required Function(ImageSource) onPick,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
        const SizedBox(height: 6),
        InkWell(
          onTap: () {
            showModalBottomSheet(
              context: context,
              builder: (ctx) => SafeArea(
                child: Wrap(
                  children: [
                    ListTile(
                      leading: const Icon(Icons.camera_alt, color: Color(0xFF15803D)),
                      title: const Text('Ambil Foto Kamera'),
                      onTap: () {
                        Navigator.pop(ctx);
                        onPick(ImageSource.camera);
                      },
                    ),
                    ListTile(
                      leading: const Icon(Icons.photo_library, color: Colors.blue),
                      title: const Text('Pilih dari Galeri'),
                      onTap: () {
                        Navigator.pop(ctx);
                        onPick(ImageSource.gallery);
                      },
                    ),
                  ],
                ),
              ),
            );
          },
          child: Container(
            height: 120,
            decoration: BoxDecoration(
              color: Colors.grey.shade100,
              borderRadius: BorderRadius.circular(10),
              border: Border.all(color: Colors.grey.shade400, style: BorderStyle.solid),
            ),
            child: file != null
                ? ClipRRect(
                    borderRadius: BorderRadius.circular(10),
                    child: Image.file(file, fit: BoxFit.cover, width: double.infinity),
                  )
                : const Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.add_a_photo, color: Colors.grey, size: 28),
                      SizedBox(height: 6),
                      Text('Ambil Foto', style: TextStyle(color: Colors.grey, fontSize: 11)),
                    ],
                  ),
          ),
        ),
      ],
    );
  }
}
