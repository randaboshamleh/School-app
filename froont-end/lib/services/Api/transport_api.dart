// transport_api.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'api_config.dart';
import 'package:rand/models/transport_models.dart';

Future<List<TransportRoute>> fetchRoutes() async {
  final token = await getAuthToken();
  final res = await http.get(
    Uri.parse("$kBaseUrl/transport/routes"),
    headers: {
      "Accept": "application/json",
      "Authorization": "Bearer $token",
    },
  );
  if (res.statusCode == 200) {
    final data = jsonDecode(res.body);
    final list = (data['data'] as List)
        .map((e) => TransportRoute.fromJson(e))
        .toList();
    return list;
  }
  throw Exception("فشل جلب الخطوط: ${res.statusCode}");
}

Future<void> subscribeToRoute({required int routeId}) async {
  final token = await getAuthToken();
  final res = await http.post(
    Uri.parse("$kBaseUrl/transport/subscribe"),
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json",
      "Authorization": "Bearer $token",
    },
    body: jsonEncode({"route_id": routeId}),
  );
  if (res.statusCode != 200) {
    final msg = _extractError(res.body);
    throw Exception("فشل الاشتراك: $msg");
  }
}

Future<void> cancelSubscription({required int routeId}) async {
  final token = await getAuthToken();
  final res = await http.delete(
    Uri.parse("$kBaseUrl/transport/subscribe/$routeId"),
    headers: {
      "Accept": "application/json",
      "Authorization": "Bearer $token",
    },
  );
  if (res.statusCode != 200) {
    final msg = _extractError(res.body);
    throw Exception("فشل إلغاء الاشتراك: $msg");
  }
}

String _extractError(String body) {
  try {
    final m = jsonDecode(body);
    return (m['message'] ?? m['error'] ?? "خطأ غير معروف").toString();
  } catch (_) {
    return "خطأ غير معروف";
  }
}
