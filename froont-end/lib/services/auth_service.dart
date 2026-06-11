import 'package:flutter/foundation.dart';
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';

class AuthService {
  static const _kTokenKey = 'token';
  static const _kStudentIdKey = 'studentId';

  final Dio _dio = Dio(
    BaseOptions(
      baseUrl: 'http://192.168.1.10:8000/api',
      headers: {'Accept': 'application/json'},
    ),
  );

  final FlutterSecureStorage _secureStorage = const FlutterSecureStorage();

  // ---- Helpers ----
  Future<void> _setAuthHeaderFromToken(String token) async {
    _dio.options.headers['Authorization'] = 'Bearer $token';
  }

  Future<int?> _fetchAndCacheStudentId() async {
    try {
      final res = await _dio.get('/user');
      final user = (res.data is Map && res.data['user'] is Map)
          ? res.data['user']
          : res.data; // Ìœ⁄„ «·Õ«· Ì‰: {user:{...}} √Ê {...}

      final sidRaw = user?['student_id'];
      final sid = int.tryParse('$sidRaw');
      if (sid != null) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setInt(_kStudentIdKey, sid);
        debugPrint("? studentId  „  ≈÷«› Â „‰ /user: $sid");
        return sid;
      }
      debugPrint("? /user ·„ Ìı—Ãˆ⁄ student_id");
    } catch (e) {
      debugPrint("? ›‘· Ã·» /user: $e");
    }
    return null;
  }

  // Ê›¯—Â« · ” Œœ„Â« »√Ì „ﬂ«‰ („À· “— «·œ—Ã« )
  Future<int?> getStudentId() async {
    final prefs = await SharedPreferences.getInstance();
    int? sid = prefs.getInt(_kStudentIdKey);
    if (sid == null) {
      // Ã—¯»  ÃÌ»Â „‰ «·”Ì—›— ≈–« „⁄ﬂ  Êﬂ‰
      final token = await _secureStorage.read(key: _kTokenKey);
      if (token != null) {
        await _setAuthHeaderFromToken(token);
        sid = await _fetchAndCacheStudentId();
      }
    }
    return sid;
  }

  // ---- Session check ----
  Future<bool> isLoggedIn() async {
    // 1) ÃÌ» «· Êﬂ‰ „‰ SecureStorage √Ê Â«Ã— „‰ SharedPreferences ≈–« „ÊÃÊœ Â‰«ﬂ
    var token = await _secureStorage.read(key: _kTokenKey);
    final prefs = await SharedPreferences.getInstance();
    token ??= prefs.getString(_kTokenKey); // ÂÃ—…  Êﬂ‰ ﬁœÌ„ ≈‰ ÊÃœ
    if (token != null) {
      await _secureStorage.write(key: _kTokenKey, value: token);
      await _setAuthHeaderFromToken(token);
    }

    // Debug
    final studentId = prefs.getInt(_kStudentIdKey);
    debugPrint("?? token = $token");
    debugPrint("?? studentId = $studentId");
    debugPrint(
      "?? all prefs: ${prefs.getKeys().map((k) => '$k=${prefs.get(k)}').join(', ')}",
    );

    if (token == null) return false;

    // 2) ≈‰ „« ›ÌÂ studentId° ÃÌ»Â „‰ /user ÊŒ“¯‰Â
    if (studentId == null) {
      final fetched = await _fetchAndCacheStudentId();
      return fetched != null;
    }

    return true;
  }

  // ---- Login ----
  Future<Map<String, dynamic>> login(String sidOrEmail, String password) async {
    try {
      debugPrint("?? DIO baseUrl = ${_dio.options.baseUrl}");
      // √—”· «·Õﬁ·Ì‰ „⁄«∫ «·»«ﬂ ≈‰œ ⁄‰œﬂ Ìÿ·» student_id° ÊÂÌﬂ »‰€ÿÌ «·Õ«· Ì‰
      final response = await _dio.post(
        '/login',
        data: {'student_id': sidOrEmail, 'password': password},
      );

      if (response.statusCode == 200) {
        final data = Map<String, dynamic>.from(response.data);
        final token = data['token'] as String?;
        if (token == null) throw Exception('No token in response');

        // Œ“¯‰ «· Êﬂ‰ (›Ì SecureStorage ›ﬁÿ)
        await _secureStorage.write(key: _kTokenKey, value: token);
        await _setAuthHeaderFromToken(token);

        // «” Œ—Ã student_id „‰ «·—œ ≈‰ ÊÃœ° Ê≈·« «ÿ·» /user
        final user = (data['user'] is Map) ? data['user'] as Map : {};
        var sidRaw = user['student_id'];
        sidRaw ??= user['sid']; // «Õ Ì«ÿ
        if (sidRaw == null) {
          await _fetchAndCacheStudentId();
        } else {
          final sid = int.tryParse('$sidRaw');
          if (sid != null) {
            final prefs = await SharedPreferences.getInstance();
            await prefs.setInt(_kStudentIdKey, sid);
            debugPrint("? saved studentId: $sid");
          }
        }

        return data;
      } else {
        throw Exception(response.data['message'] ?? 'Login failed');
      }
    } catch (e) {
      debugPrint("? Œÿ√ √À‰«¡  ”ÃÌ· «·œŒÊ·: $e");
      rethrow;
    }
  }

  // ---- Register (·Ê » ” Œœ„Â „‰ «·„Ê»«Ì·) ----
  Future<Map<String, dynamic>> register({
    required String name,
    required String studentId,
    required String password,
  }) async {
    try {
      final response = await _dio.post(
        '/register',
        data: {
          'name': name,
          'student_id': studentId,
          'password': password,
          'password_confirmation': password,
        },
      );

      if (response.statusCode == 201 || response.statusCode == 200) {
        final data = Map<String, dynamic>.from(response.data);
        final token = data['token'] as String?;
        if (token != null) {
          await _secureStorage.write(key: _kTokenKey, value: token);
          await _setAuthHeaderFromToken(token);
        }

        // Œ“¯‰ student_id „‰ «·—œ √Ê „‰ /user
        final user = (data['user'] is Map) ? data['user'] as Map : {};
        var sidRaw = user['student_id'];
        sidRaw ??= user['sid'];
        if (sidRaw == null) {
          await _fetchAndCacheStudentId();
        } else {
          final sid = int.tryParse('$sidRaw');
          if (sid != null) {
            final prefs = await SharedPreferences.getInstance();
            await prefs.setInt(_kStudentIdKey, sid);
            debugPrint("? saved studentId: $sid");
          }
        }

        return data;
      } else {
        throw Exception(response.data['message'] ?? 'Register failed');
      }
    } catch (e) {
      debugPrint("? Œÿ√ √À‰«¡ «· ”ÃÌ·: $e");
      rethrow;
    }
  }

  // ---- Current user ----
  Future<Map<String, dynamic>> getCurrentUser() async {
    final token = await _secureStorage.read(key: _kTokenKey);
    if (token == null) throw Exception("No token found");
    await _setAuthHeaderFromToken(token);
    final response = await _dio.get('/user');
    return Map<String, dynamic>.from(response.data);
  }

  // ---- Logout ----
  Future<void> logout() async {
    await _secureStorage.delete(key: _kTokenKey);
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_kStudentIdKey);
    debugPrint("??  „  ”ÃÌ· «·Œ—ÊÃ ÊÕ–› «·»Ì«‰« ");
  }
}
