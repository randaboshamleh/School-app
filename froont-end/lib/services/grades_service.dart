import 'package:flutter/foundation.dart';
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';

class GradesService {
  static Future<Map<String, dynamic>> fetchGrades() async {
    final dio = Dio();
    final secureStorage = const FlutterSecureStorage();

    // ?? ŞÑÇÁÉ ÇáÊæßä
    String? token = await secureStorage.read(key: 'token');
    if (token == null) {
      throw Exception("? áã íÊã ÇáÚËæÑ Úáì ÇáÊæßä İí ÇáÊÎÒíä ÇáÂãä");
    }

    // ?? ŞÑÇÁÉ studentId
    String? studentIdStr = await secureStorage.read(key: 'studentId');
    if (studentIdStr == null) {
      final prefs = await SharedPreferences.getInstance();
      final int? studentIdPref = prefs.getInt('studentId');
      if (studentIdPref == null) {
        throw Exception("? áã íÊã ÇáÚËæÑ Úáì studentId");
      }
      studentIdStr = studentIdPref.toString();
    }

    final int? studentId = int.tryParse(studentIdStr);
    if (studentId == null) {
      throw Exception("? ÕíÛÉ studentId ÛíÑ ÕÍíÍÉ: $studentIdStr");
    }

    // ?? ÅÚÏÇÏ ÇáåíÏÑÒ
    dio.options.headers = {
      "Authorization": "Bearer $token",
      "Accept": "application/json",
    };
    debugPrint("?? Sending headers: ${dio.options.headers}");

    // ?? ÇáØáÈ
    final response = await dio.get(
      "http://192.168.1.10:8000/api/exam-scores?student_id=$studentId",
    );

    final resData = response.data;
    debugPrint("?? Full response: $resData"); // ?? ãåã ááÊÔÎíÕ

    // ? äÊÃßÏ Åäæ ÇáÑÏ Map æİíå student + scores
    if (resData is Map<String, dynamic>) {
      final studentName = resData['student'];
      final scores = resData['scores'] as List<dynamic>? ?? [];

      debugPrint("?? ÇáØÇáÈ: $studentName");
      debugPrint("? ÚÏÏ ÇáÚáÇãÇÊ: ${scores.length}");

      return {
        "student": studentName,
        "scores": scores,
      };
    } else {
      throw Exception("? ÕíÛÉ ÇáÇÓÊÌÇÈÉ ÛíÑ ãÊæŞÚÉ: ${resData.runtimeType}");
    }
  }
}
