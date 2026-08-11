class UserModel {
  final int id;
  final String username;
  final String levelAkses;
  final String? idPks;
  final String? namaPks;

  UserModel({
    required this.id,
    required this.username,
    required this.levelAkses,
    this.idPks,
    this.namaPks,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      username: json['username'] ?? '',
      levelAkses: json['level_akses'] ?? 'unit',
      idPks: json['id_pks']?.toString(),
      namaPks: json['nama_pks']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'username': username,
      'level_akses': levelAkses,
      'id_pks': idPks,
      'nama_pks': namaPks,
    };
  }

  bool get isAdmin => levelAkses.toLowerCase() == 'admin';
  bool get isUnit => levelAkses.toLowerCase() == 'unit';
}
