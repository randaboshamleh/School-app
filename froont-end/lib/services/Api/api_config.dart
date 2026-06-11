// lib/services/api/api_config.dart

import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// نستخدم dart-define لتحديد ENV: flutter run --dart-define=ENV=dev
const String _env = String.fromEnvironment('ENV', defaultValue: 'prod');

/// روابط السيرفرات حسب البيئة
const Map<String, String> _baseUrls = {
  'dev': 'http://192.168.1.10:8000', // 🟢 سيرفرك المحلي (Laravel)
  'staging': 'https://staging.yourdomain.com', // (اختياري)
  'prod': 'https://yourdomain.com', // (عند رفع المشروع فعلاً)
};

/// Base URL مع إضافة /api اختياريًا
final String kBaseUrl = '${_baseUrls[_env] ?? _baseUrls['prod']}/api';
const String kApiVersion = 'v1';
final Duration kRequestTimeout = Duration(seconds: 30);

/// استخدام FlutterSecureStorage لتخزين التوكنات بأمان
const _secureStorage = FlutterSecureStorage();

Future<String?> getAuthToken() async {
  return await _secureStorage.read(key: 'access_token');
}

Future<String?> getRefreshToken() async {
  return await _secureStorage.read(key: 'refresh_token');
}

Future<void> saveTokens({
  required String accessToken,
  required String refreshToken,
}) async {
  await _secureStorage.write(key: 'access_token', value: accessToken);
  await _secureStorage.write(key: 'refresh_token', value: refreshToken);
}

Future<void> clearTokens() async {
  await _secureStorage.delete(key: 'access_token');
  await _secureStorage.delete(key: 'refresh_token');
}

/// هيدرز افتراضية (تضيف Authorization إن وجد توكن)
Future<Map<String, String>> defaultHeaders({Map<String, String>? extra}) async {
  final token = await getAuthToken();
  final headers = <String, String>{
    'Accept': 'application/json',
    'Content-Type': 'application/json; charset=utf-8',
    'Accept-Language': 'ar',
    if (token != null) 'Authorization': 'Bearer $token',
  };
  if (extra != null) headers.addAll(extra);
  return headers;
}

/// 🟢 إنشاء كائن Dio عام لتعامل مع الـ API
final Dio dio = Dio(
  BaseOptions(
    baseUrl: kBaseUrl,
    connectTimeout: const Duration(seconds: 10), // ⏱️ مهلة الاتصال
    receiveTimeout: const Duration(seconds: 10), // ⏱️ مهلة استقبال الرد
    sendTimeout: const Duration(seconds: 10), // ⏱️ مهلة الإرسال
    headers: {"Content-Type": "application/json", "Accept": "application/json"},
  ),
);
