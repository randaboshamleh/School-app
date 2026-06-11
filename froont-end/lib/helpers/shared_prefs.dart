import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/grades_service.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

// ÍİÙ studentId İí SharedPreferences
Future<void> saveStudentId(int studentId) async {
  final prefs = await SharedPreferences.getInstance();
  await prefs.setInt('studentId', studentId);
}

Future<void> saveToken(String token) async {
  SharedPreferences prefs = await SharedPreferences.getInstance();
  await prefs.setString('token', token);
}

// ÏÇáÉ áÇÓÊÑÌÇÚ ÇáÜ token áÇÍŞğÇ
Future<String?> getToken() async {
  SharedPreferences prefs = await SharedPreferences.getInstance();
  return prefs.getString('token');
}

// ÌáÈ studentId ãä SharedPreferences
Future<int?> getStudentId() async {
  final prefs = await SharedPreferences.getInstance();
  return prefs.getInt('studentId');
}

Future<void> loadGrades() async {
  try {
    final grades = await GradesService.fetchGrades();
    debugPrint("? ÚÏÏ ÇáãæÇÏ: ${grades.length}");
  } catch (e) {
    debugPrint("? ÎØÃ ÃËäÇÁ ÌáÈ ÇáÏÑÌÇÊ: $e");
  }
}

const String kBaseUrl = "http://192.168.1.10:8000/api";

Future<String?> getAuthToken() async {
  const storage = FlutterSecureStorage();
  return await storage.read(key: 'token');
}
