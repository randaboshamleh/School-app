import 'package:flutter/material.dart';
import 'package:rand/helpers/locale_direction.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import 'dart:async';
import '../models/notification_models.dart';
import '../services/notification_service.dart' as service;

const Color purpleColor = Color(0xFF0F766E);
const Color greenColor = Color(0xFF2563EB);

class NotificationsScreen extends StatefulWidget {
  static const String screenRoute = "/notifications";
  const NotificationsScreen({super.key});

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  List<NotificationItem> notifications = [];
  bool loading = true;
  StreamSubscription? _subscription;

  @override
  void initState() {
    super.initState();
    loadCachedNotifications();

    _subscription = service.NotificationService.stream.listen((message) {
      loadCachedNotifications();
    });

    WidgetsBinding.instance.addPostFrameCallback((_) async {
      await service.NotificationService.markAllAsRead();
      await loadCachedNotifications();
    });
  }

  Future<void> loadCachedNotifications() async {
    setState(() => loading = true);
    try {
      final prefs = await SharedPreferences.getInstance();
      final cached = prefs.getString("cached_notifications");
      List<NotificationItem> list = [];

      if (cached != null) {
        final data = jsonDecode(cached);
        list = (data as List).map((n) => NotificationItem.fromJson(n)).toList();
      }

      // ≈“«·… «· ﬂ—«— Õ”» id
      final Map<int, NotificationItem> map = {for (var n in list) n.id: n};

      final unique = map.values.toList();

      //  — Ì» «·√ÕœÀ √Ê·«
      unique.sort((a, b) => b.createdAt.compareTo(a.createdAt));

      setState(() => notifications = unique);
    } catch (e) {
      debugPrint("?? Œÿ√ √À‰«¡  Õ„Ì· «·≈‘⁄«—« : $e");
    } finally {
      setState(() => loading = false);
    }
  }

  int get unreadCount => notifications.where((n) => !n.isRead).length;

  @override
  void dispose() {
    _subscription?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("«·≈‘⁄«—« ", style: TextStyle(color: Colors.black)),
        centerTitle: true,
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [purpleColor, greenColor],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
        ),
      ),
      body: Stack(
        children: [
          Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [purpleColor, greenColor],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
            ),
          ),
          // ‘⁄«— ‘›«› »«·Œ·›Ì…
          Positioned.fill(
            child: Opacity(
              opacity: 0.05,
              child: const Icon( Icons.school_outlined, size: 220, color: Colors.white ),
            ),
          ),
          SafeArea(
            child: Column(
              children: [
                const SizedBox(height: 20),
                Expanded(
                  child: loading
                      ? const Center(child: CircularProgressIndicator())
                      : notifications.isEmpty
                      ? const Center(
                          child: Text(
                            "·« ÌÊÃœ ≈‘⁄«—« ",
                            style: TextStyle(color: Colors.white, fontSize: 18),
                          ),
                        )
                      : RefreshIndicator(
                          onRefresh: loadCachedNotifications,
                          child: ListView.builder(
                            itemCount: notifications.length,
                            itemBuilder: (context, index) {
                              final n = notifications[index];
                              return Directionality(
                                textDirection: appTextDirection(context),
                                child: Card(
                                  color: Colors.white.withValues(alpha: 0.5),
                                  margin: const EdgeInsets.symmetric(
                                    horizontal: 16,
                                    vertical: 8,
                                  ),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: ListTile(
                                    title: Text(
                                      n.title,
                                      style: const TextStyle(
                                        fontWeight: FontWeight.bold,
                                        color: Colors.black87,
                                      ),
                                    ),
                                    subtitle: Text(
                                      n.body,
                                      style: const TextStyle(
                                        fontSize: 16,
                                        color: Colors.black54,
                                      ),
                                    ),
                                    trailing: Text(
                                      "${n.createdAt.hour}:${n.createdAt.minute.toString().padLeft(2, '0')}",
                                      style: const TextStyle(
                                        fontSize: 12,
                                        color: Colors.black54,
                                      ),
                                    ),
                                  ),
                                ),
                              );
                            },
                          ),
                        ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
