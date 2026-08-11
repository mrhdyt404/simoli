class MonitoringAlatModel {
  final int id;
  final String? idPks;
  final String? namaPks;
  final int? alatBeratId;
  final String? kodeAlat;
  final String? jenisAlat;
  final String? tanggal;
  final String? operator;
  final String? kegiatan;
  final String? lokasiBlok;
  final double? latitude;
  final double? longitude;
  final String? mapsUrl;
  final String? hmAwal;
  final String? hmAkhir;
  final double? totalHm;
  final String? totalHmFormatted;
  final double? bbmLiter;
  final String? kondisiAlat;
  final String? foto;
  final String? fotoSebelum;
  final String? fotoSesudah;
  final String? catatan;

  MonitoringAlatModel({
    required this.id,
    this.idPks,
    this.namaPks,
    this.alatBeratId,
    this.kodeAlat,
    this.jenisAlat,
    this.tanggal,
    this.operator,
    this.kegiatan,
    this.lokasiBlok,
    this.latitude,
    this.longitude,
    this.mapsUrl,
    this.hmAwal,
    this.hmAkhir,
    this.totalHm,
    this.totalHmFormatted,
    this.bbmLiter,
    this.kondisiAlat,
    this.foto,
    this.fotoSebelum,
    this.fotoSesudah,
    this.catatan,
  });

  factory MonitoringAlatModel.fromJson(Map<String, dynamic> json) {
    return MonitoringAlatModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      idPks: json['id_pks']?.toString(),
      namaPks: json['nama_pks']?.toString(),
      alatBeratId: json['alat_berat_id'] != null ? int.tryParse(json['alat_berat_id'].toString()) : null,
      kodeAlat: json['kode_alat']?.toString(),
      jenisAlat: json['jenis_alat']?.toString(),
      tanggal: json['tanggal']?.toString(),
      operator: json['operator']?.toString(),
      kegiatan: json['kegiatan']?.toString(),
      lokasiBlok: json['lokasi_blok']?.toString(),
      latitude: json['latitude'] != null ? double.tryParse(json['latitude'].toString()) : null,
      longitude: json['longitude'] != null ? double.tryParse(json['longitude'].toString()) : null,
      mapsUrl: json['maps_url']?.toString(),
      hmAwal: json['hm_awal']?.toString(),
      hmAkhir: json['hm_akhir']?.toString(),
      totalHm: json['total_hm'] != null ? double.tryParse(json['total_hm'].toString()) : null,
      totalHmFormatted: json['total_hm_formatted']?.toString(),
      bbmLiter: json['bbm_liter'] != null ? double.tryParse(json['bbm_liter'].toString()) : null,
      kondisiAlat: json['kondisi_alat']?.toString() ?? 'Baik',
      foto: json['foto']?.toString(),
      fotoSebelum: json['foto_sebelum']?.toString(),
      fotoSesudah: json['foto_sesudah']?.toString(),
      catatan: json['catatan']?.toString(),
    );
  }
}

class AlatBeratMasterModel {
  final int id;
  final String? idPks;
  final String kodeAlat;
  final String jenisAlat;
  final String? merkModel;

  AlatBeratMasterModel({
    required this.id,
    this.idPks,
    required this.kodeAlat,
    required this.jenisAlat,
    this.merkModel,
  });

  factory AlatBeratMasterModel.fromJson(Map<String, dynamic> json) {
    String jenis = json['jenis_alat']?.toString() ?? json['nama_alat']?.toString() ?? 'Alat Berat';
    String kode = json['kode_alat']?.toString() ?? 'ALAT-${json['id']}';
    return AlatBeratMasterModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      idPks: json['id_pks']?.toString(),
      kodeAlat: kode,
      jenisAlat: jenis,
      merkModel: json['merk_model']?.toString() ?? json['merk_tipe']?.toString(),
    );
  }
}

class PksMasterModel {
  final String idPks;
  final String nama;

  PksMasterModel({
    required this.idPks,
    required this.nama,
  });

  factory PksMasterModel.fromJson(Map<String, dynamic> json) {
    return PksMasterModel(
      idPks: json['id_pks']?.toString() ?? '',
      nama: json['nama']?.toString() ?? '',
    );
  }
}
