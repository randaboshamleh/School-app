import 'package:flutter/foundation.dart';
import 'dart:async';
import 'dart:convert';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:dio/dio.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import '../models/notification_models.dart';
import '../main.dart';

class NotificationService {
  // ?? Stream · ÕœÌÀ «·‘«‘… ⁄‰œ Ê’Ê· ≈‘⁄«—«  ÃœÌœ…
  static final StreamController<NotificationItem> _controller =
      StreamController<NotificationItem>.broadcast();

  static Stream<NotificationItem> get stream => _controller.stream;

  static final _storage = const FlutterSecureStorage();

  // ?? Plugin ··≈‘⁄«—«  «·„Õ·Ì…
  static final FlutterLocalNotificationsPlugin _localNotifications =
      FlutterLocalNotificationsPlugin();

  /// ??  ÂÌ∆… «·≈‘⁄«—«  «·„Õ·Ì…
  static Future<void> initLocalNotifications() async {
    const androidInit = AndroidInitializationSettings('@mipmap/ic_launcher');
    const iosInit = DarwinInitializationSettings();
    const initSettings = InitializationSettings(
      android: androidInit,
      iOS: iosInit,
    );

    await _localNotifications.initialize(initSettings);
  }

  /// ?? ⁄—÷ ≈‘⁄«— „Õ·Ì ⁄·Ï «·ÃÂ«“
  static Future<void> showLocalNotification(String title, String body) async {
    const androidDetails = AndroidNotificationDetails(
      'channel_id',
      'General Notifications',
      importance: Importance.max,
      priority: Priority.high,
    );
    const notifDetails = NotificationDetails(android: androidDetails);

    await _localNotifications.show(
      DateTime.now().millisecondsSinceEpoch ~/ 1000,
      title,
      body,
      notifDetails,
    );
  }

  /// ?? Õ›Ÿ FCM Token ›Ì «·”Ì—›— («Œ Ì«—Ì)
  static Future<void> registerFCMToken(
    String userId,
    String authToken,
    String fcmToken,
  ) async {
    final dio = Dio(BaseOptions(baseUrl: "http://192.168.1.10:8000/api"));
    try {
      await dio.post(
        "/save-fcm-token",
        data: {"user_id": userId, "token": fcmToken},
        options: Options(headers: {"Authorization": "Bearer $authToken"}),
      );
      debugPrint("?  „ Õ›Ÿ «·‹ FCM Token ⁄·Ï «·”Ì—›—");
    } catch (e) {
      debugPrint("? ›‘· Õ›Ÿ FCM Token: $e");
    }
  }

  /// ?? ≈—”«· FCM Token ··»«ﬂ ≈‰œ
  static Future<void> sendTokenToBackend() async {
    String? token = await FirebaseMessaging.instance.getToken();
    String? authToken = await _storage.read(key: 'token');
    debugPrint("?? FCM Token: $token");

    if (token != null && authToken != null) {
      try {
        var response = await http.post(
          Uri.parse('http://192.168.1.10:8000/api/fcm-token'),
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer $authToken',
          },
          body: jsonEncode({'token': token, 'platform': 'android'}),
        );

        if (response.statusCode == 200) {
          debugPrint("?  „ ≈—”«· FCM token ··»«ﬂ");
        } else {
          debugPrint("? ›‘· ≈—”«· FCM token: ${response.body}");
        }
      } catch (e) {
        debugPrint("?? Œÿ√ √À‰«¡ ≈—”«· FCM token: $e");
      }
    }
  }

  /// ?? „⁄«·Ã… «·≈‘⁄«—«  «·ﬁ«œ„… „‰ Firebase
  static Future<void> handleMessage(RemoteMessage message) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final cached = prefs.getString("cached_notifications");
      List data = cached != null ? jsonDecode(cached) : [];

      final rawId =
          message.messageId ??
          "${message.notification?.title}-${message.notification?.body}";
      final messageId = rawId.hashCode.toString();

      final alreadyExists = data.any((n) => n["id"].toString() == messageId);
      if (alreadyExists) {
        debugPrint("?? ≈‘⁄«— „ﬂ——: ${message.notification?.title}");
        return;
      }

      final newItem = NotificationItem(
        id: int.tryParse(messageId) ?? DateTime.now().millisecondsSinceEpoch,
        title:
            message.notification?.title ??
            message.data['title'] ??
            "»œÊ‰ ⁄‰Ê«‰",
        body: message.notification?.body ?? message.data['body'] ?? "",
        createdAt: DateTime.now(),
        isRead: false,
      );

      data.insert(0, newItem.toJson());
      await prefs.setString("cached_notifications", jsonEncode(data));

      await showLocalNotification(newItem.title, newItem.body);
      _controller.add(newItem);

      debugPrint("?? ≈‘⁄«— ÃœÌœ  „ Õ›ŸÂ: ${newItem.title}");
    } catch (e) {
      debugPrint("?? Œÿ√ √À‰«¡ Õ›Ÿ «·≈‘⁄«—: $e");
    }
  }

  /// ?? Ã·» «·≈‘⁄«—«  «·„Œ“‰…
  static Future<List<NotificationItem>> getCachedNotifications() async {
    final prefs = await SharedPreferences.getInstance();
    final cached = prefs.getString("cached_notifications");
    if (cached != null) {
      final data = jsonDecode(cached) as List;
      return data.map((n) => NotificationItem.fromJson(n)).toList();
    }
    return [];
  }

  /// ? Ê÷⁄ ﬂ· «·≈‘⁄«—«  ﬂ„ﬁ—Ê¡…
  static Future<void> markAllAsRead() async {
    final prefs = await SharedPreferences.getInstance();
    final cached = prefs.getString("cached_notifications");
    if (cached == null) return;

    final data = jsonDecode(cached) as List;
    for (var item in data) {
      item["is_read"] = true;
    }

    await prefs.setString("cached_notifications", jsonEncode(data));

    _controller.add(
      NotificationItem(
        id: -1,
        title: "",
        body: "",
        createdAt: DateTime.now(),
        isRead: true,
      ),
    );
  }

  /// ? ≈÷«›… ≈‘⁄«— ÃœÌœ ÌœÊÌ« („À·« „‰ «·”Ì—›—)
  static Future<void> addNotification(NotificationItem item) async {
    final prefs = await SharedPreferences.getInstance();
    final cached = prefs.getString("cached_notifications");
    final data = cached != null ? jsonDecode(cached) as List : [];
    data.insert(0, item.toJson());
    await prefs.setString("cached_notifications", jsonEncode(data));
    _controller.add(item);
  }

  /// ??  ÂÌ∆… Firebase Messaging
  static bool _isFCMInitialized = false;

  static Future<void> initFCM() async {
    if (_isFCMInitialized) return;
    _isFCMInitialized = true;

    await FirebaseMessaging.instance.requestPermission();

    FirebaseMessaging.onMessage.listen((msg) => handleMessage(msg));
    FirebaseMessaging.onMessageOpenedApp.listen((msg) {
      debugPrint("??  „ › Õ «· ÿ»Ìﬁ „‰ ≈‘⁄«—: ${msg.messageId}");
      _handleNotificationClick(msg);
    });
    final initialMessage = await FirebaseMessaging.instance.getInitialMessage();
    if (initialMessage != null) {
      _handleNotificationClick(initialMessage);
    }

    await sendTokenToBackend();
  }

  // ?? › Õ «·’›Õ… «·„‰«”»… ⁄‰œ «·÷€ÿ ⁄·Ï «·≈‘⁄«—
  static void _handleNotificationClick(RemoteMessage message) {
    final route = message.data['route'];
    if (route != null && navigatorKey.currentState != null) {
      navigatorKey.currentState!.pushNamed(route);
    }
  }
}

/// ?? „⁄«·Ã «·≈‘⁄«—«  ›Ì «·Œ·›Ì… (Œ«—Ã «·ﬂ·«”)
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await NotificationService.handleMessage(message);
}
