import 'dart:convert';
import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user_model.dart';
import '../models/monitoring_alat_model.dart';

class ApiService {
  // Default Base URL intelligently picked based on platform (10.0.2.2 for Android Emulator, localhost for Windows/Web)
  static String defaultBaseUrl = (!kIsWeb && Platform.isAndroid)
      ? 'http://10.0.2.2/simolii/public/api'
      : 'http://localhost/simolii/public/api';

  static String _currentBaseUrl = defaultBaseUrl;

  static String get baseUrl => _currentBaseUrl;

  static set baseUrl(String newUrl) {
    _currentBaseUrl = newUrl;
    _saveBaseUrl(newUrl);
  }

  static Future<void> initBaseUrl() async {
    final prefs = await SharedPreferences.getInstance();
    final saved = prefs.getString('custom_api_base_url');
    if (saved != null && saved.isNotEmpty) {
      _currentBaseUrl = saved;
    } else {
      _currentBaseUrl = defaultBaseUrl;
    }
  }

  static Future<void> _saveBaseUrl(String url) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('custom_api_base_url', url);
  }

  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  static Future<void> saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }

  static Future<void> clearToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    await prefs.remove('user_data');
  }

  static Future<void> saveUser(UserModel user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('user_data', jsonEncode(user.toJson()));
  }

  static Future<UserModel?> getSavedUser() async {
    final prefs = await SharedPreferences.getInstance();
    final jsonStr = prefs.getString('user_data');
    if (jsonStr != null) {
      try {
        return UserModel.fromJson(jsonDecode(jsonStr));
      } catch (_) {}
    }
    return null;
  }

  static Map<String, String> _headers(String? token) {
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  static Future<Map<String, dynamic>> login(String username, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode({'username': username, 'password': password}),
      ).timeout(const Duration(seconds: 10));

      final data = jsonDecode(response.body);
      if (response.statusCode == 200 && data['success'] == true) {
        final token = data['token'];
        final user = UserModel.fromJson(data['user']);
        await saveToken(token);
        await saveUser(user);
        return {'success': true, 'user': user, 'token': token};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Login gagal'};
      }
    } catch (e) {
      String msg = e.toString();
      if (msg.contains('SocketException') || msg.contains('Connection refused')) {
        return {
          'success': false,
          'message': 'Gagal terhubung ke server SIMOLI ($baseUrl).\n'
              '• Jika memakai Android Emulator: gunakan IP "http://10.0.2.2/simolii/public/api"\n'
              '• Jika memakai HP Fisik: gunakan IP Wi-Fi PC (misal "http://192.168.X.X/simolii/public/api")\n'
              '• Pastikan Apache/XAMPP server sudah berjalan.'
        };
      }
      return {'success': false, 'message': 'Koneksi server gagal: $msg'};
    }
  }

  static Future<Map<String, dynamic>> getMasterData({String? idPks}) async {
    final token = await getToken();
    try {
      Uri uri = Uri.parse('$baseUrl/master-data');
      if (idPks != null && idPks.isNotEmpty) {
        uri = uri.replace(queryParameters: {'id_pks': idPks});
      }
      final res = await http.get(uri, headers: _headers(token)).timeout(const Duration(seconds: 10));
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        final List<PksMasterModel> pksList = (data['pks'] as List? ?? [])
            .map((i) => PksMasterModel.fromJson(i))
            .toList();
        final List<AlatBeratMasterModel> alatList = (data['alat_berat'] as List? ?? [])
            .map((i) => AlatBeratMasterModel.fromJson(i))
            .toList();
        return {'pks': pksList, 'alat_berat': alatList};
      }
    } catch (e) {
      print('Error master data: $e');
    }
    return {'pks': <PksMasterModel>[], 'alat_berat': <AlatBeratMasterModel>[]};
  }

  static Future<Map<String, dynamic>> postMonitoringAlatBerat({
    required int alatBeratId,
    required String tanggal,
    required String operator,
    required String kegiatan,
    required String lokasiBlok,
    required String kondisiAlat,
    String? hmAwal,
    String? hmAkhir,
    double? totalHm,
    double? bbmLiter,
    double? latitude,
    double? longitude,
    String? catatan,
    File? fotoSebelum,
    File? fotoSesudah,
  }) async {
    final token = await getToken();
    try {
      var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/monitoring-alat-berat'));
      if (token != null) {
        request.headers['Authorization'] = 'Bearer $token';
      }

      request.fields['alat_berat_id'] = alatBeratId.toString();
      request.fields['tanggal'] = tanggal;
      request.fields['operator'] = operator;
      request.fields['kegiatan'] = kegiatan;
      request.fields['lokasi_blok'] = lokasiBlok;
      request.fields['kondisi_alat'] = kondisiAlat;
      if (hmAwal != null) request.fields['hm_awal'] = hmAwal;
      if (hmAkhir != null) request.fields['hm_akhir'] = hmAkhir;
      if (totalHm != null) request.fields['total_hm'] = totalHm.toString();
      if (bbmLiter != null) request.fields['bbm_liter'] = bbmLiter.toString();
      if (latitude != null) request.fields['latitude'] = latitude.toString();
      if (longitude != null) request.fields['longitude'] = longitude.toString();
      if (catatan != null) request.fields['catatan'] = catatan;

      if (fotoSebelum != null) {
        request.files.add(await http.MultipartFile.fromPath('foto_sebelum', fotoSebelum.path));
      }
      if (fotoSesudah != null) {
        request.files.add(await http.MultipartFile.fromPath('foto_sesudah', fotoSesudah.path));
      }

      final streamedRes = await request.send().timeout(const Duration(seconds: 25));
      final res = await http.Response.fromStream(streamedRes);

      final data = jsonDecode(res.body);
      if (res.statusCode == 201 || (res.statusCode == 200 && data['success'] == true)) {
        return {'success': true, 'message': data['message'] ?? 'Berhasil disimpan'};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Gagal menyimpan log'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Gagal mengirim data: ${e.toString()}'};
    }
  }
}
