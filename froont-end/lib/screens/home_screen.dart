import 'dart:async';
import 'dart:convert';
import 'package:flutter/services.dart';
import 'package:flutter/material.dart';
import 'package:rand/screens/schedule_screen.dart';
import 'package:rand/screens/ads_screen.dart';
import 'package:rand/screens/file_screen.dart';
import 'package:rand/screens/transport_screen.dart';
import 'package:rand/screens/grades_screen.dart';
import 'package:rand/screens/notification_screen.dart';
import '../services/notification_service.dart';
import 'package:rand/helpers/shared_prefs.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:dio/dio.dart';
import 'package:http/http.dart' as http;
import 'package:easy_localization/easy_localization.dart';
import 'package:rand/helpers/locale_direction.dart';

class HomeScreen extends StatefulWidget {
  static const String screenRoute = 'home_screen';

  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> with TickerProviderStateMixin {
  late AnimationController _animationController;
  int unreadCount = 0;
  StreamSubscription? _notifSubscription;

  Future<int?> getStudentId() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getInt('studentId');
  }

  Future<void> checkTransportStatus(BuildContext context) async {
    final token = await getAuthToken();
    final res = await http.get(
      Uri.parse("$kBaseUrl/transport/status"),
      headers: {"Accept": "application/json", "Authorization": "Bearer $token"},
    );

    if (!context.mounted) return;

    if (res.statusCode == 200) {
      final data = jsonDecode(res.body);
      final bool status = data["status"] as bool;

      if (!status) {
        Navigator.pushReplacementNamed(context, TransportScreen.screenRoute);
      }
    } else {
      debugPrint("Failed to fetch transport status: ${res.body}");
    }
  }

  Future<void> _loadUnreadCount() async {
    final prefs = await SharedPreferences.getInstance();
    final cached = prefs.getString("cached_notifications");
    if (cached != null) {
      final data = jsonDecode(cached) as List;
      final count = data.where((n) {
        final map = n as Map<String, dynamic>;
        return map["is_read"] == false || map["is_read"] == 0;
      }).length;
      if (mounted) setState(() => unreadCount = count);
    } else {
      if (mounted) setState(() => unreadCount = 0);
    }
  }

  @override
  void initState() {
    super.initState();
    _loadUnreadCount();
    _notifSubscription = NotificationService.stream.listen((msg) {
      _loadUnreadCount();
    });
    WidgetsBinding.instance.addPostFrameCallback((_) {
      checkTransportStatus(context);
    });

    _animationController = AnimationController(
      duration: const Duration(milliseconds: 1500),
      vsync: this,
    );

    _animationController.forward();
  }

  @override
  void dispose() {
    _animationController.dispose();
    _notifSubscription?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    const purpleColor = Color(0xFF0F766E);
    const greenColor = Color(0xFF2563EB);
    bool isArabic = isArabicLocale(context);
    final String today = MaterialLocalizations.of(
      context,
    ).formatFullDate(DateTime.now());

    Widget buildBigCard(IconData icon, String title, VoidCallback onTap) {
      return GestureDetector(
        onTap: onTap,
        child: Container(
          margin: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
          decoration: BoxDecoration(
            color: Colors.white.withValues(alpha: 0.15),
            borderRadius: BorderRadius.circular(40),
            border: Border.all(
              color: Colors.white.withValues(alpha: 0.3),
              width: 1.5,
            ),
          ),
          padding: const EdgeInsets.symmetric(vertical: 25, horizontal: 20),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(icon, size: 40, color: Colors.white),
              const SizedBox(width: 12),
              Expanded(
                child: Text(
                  title,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Icon(
                isArabic ? Icons.arrow_back_ios : Icons.arrow_forward_ios,
                color: Colors.white70,
                size: 18,
              ),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: Colors.grey[50],
      drawer: isArabic ? null : _buildDrawer(isArabic),
      endDrawer: isArabic ? _buildDrawer(isArabic) : null,
      appBar: AppBar(
        elevation: 0,
        automaticallyImplyLeading: false,
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [purpleColor, greenColor],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
        ),
        leading: Builder(
          builder: (context) {
            return IconButton(
              icon: const Icon(Icons.menu, color: Colors.white),
              onPressed: () {
                if (isArabic) {
                  Scaffold.of(context).openEndDrawer();
                } else {
                  Scaffold.of(context).openDrawer();
                }
              },
            );
          },
        ),
        actions: [
          Stack(
            children: [
              IconButton(
                icon: const Icon(Icons.notifications, color: Colors.white),
                onPressed: () async {
                  await Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => const NotificationsScreen(),
                    ),
                  );
                  await _loadUnreadCount();
                },
              ),
              if (unreadCount > 0)
                Positioned(
                  right: 8,
                  top: 8,
                  child: Container(
                    padding: const EdgeInsets.all(5),
                    decoration: const BoxDecoration(
                      color: Colors.red,
                      shape: BoxShape.circle,
                    ),
                    child: Text(
                      unreadCount.toString(),
                      style: const TextStyle(color: Colors.white, fontSize: 12),
                    ),
                  ),
                ),
            ],
          ),
        ],
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
          Positioned.fill(
            child: Opacity(
              opacity: 0.05,
              child: const Icon( Icons.school_outlined, size: 220, color: Colors.white ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                const SizedBox(height: 10),
                Text(
                  "Welcome".tr(),
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                  ),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 5),
                Align(
                  alignment: isArabic
                      ? Alignment.centerRight
                      : Alignment.centerLeft,
                  child: Text(
                    today,
                    style: const TextStyle(color: Colors.white70, fontSize: 14),
                  ),
                ),
                const SizedBox(height: 20),
                Expanded(
                  child: ListView(
                    padding: const EdgeInsets.symmetric(
                      vertical: 20,
                      horizontal: 8,
                    ),
                    children: [
                      buildBigCard(Icons.grade, "Grades".tr(), () async {
                        final prefs = await SharedPreferences.getInstance();
                        final storage = const FlutterSecureStorage();
                        String? token = await storage.read(key: 'token');
                        int? studentId = prefs.getInt('studentId');

                        if (studentId == null && token != null) {
                          try {
                            final dio = Dio(
                              BaseOptions(
                                baseUrl: 'http://192.168.1.10:8000/api',
                                headers: {
                                  'Accept': 'application/json',
                                  'Authorization': 'Bearer $token',
                                },
                              ),
                            );

                            final res = await dio.get('/user');
                            final user =
                                res.data is Map && res.data['user'] != null
                                ? res.data['user']
                                : res.data;
                            final sidRaw = user['student_id'];
                            final sid = int.tryParse('$sidRaw');
                            if (sid != null) {
                              await prefs.setInt('studentId', sid);
                              studentId = sid;
                            }
                          } catch (e) {
                            debugPrint("? Œÿ√ √À‰«¡ Ã·» student_id „‰ /user: $e");
                          }
                        }

                        if (!context.mounted) return;

                        if (studentId != null) {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => GradesScreen(),
                            ),
                          );
                        } else {
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: Text("student_id_not_found".tr()),
                              backgroundColor: Colors.red,
                            ),
                          );
                        }
                      }),
                      buildBigCard(
                        Icons.calendar_today,
                        "Weekly Schedule".tr(),
                        () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => const ScheduleScreen(),
                            ),
                          );
                        },
                      ),
                      buildBigCard(
                        Icons.announcement,
                        "Advertisements".tr(),
                        () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => const AdsScreen(),
                            ),
                          );
                        },
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDrawer(bool isArabic) {
    return Drawer(
      child: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF0F766E), Color(0xFF2563EB)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: Column(
          crossAxisAlignment: isArabic
              ? CrossAxisAlignment.end
              : CrossAxisAlignment.start,
          children: [
            Container(
              padding: EdgeInsets.only(
                top: 50,
                bottom: 20,
                left: isArabic ? 0 : 20,
                right: isArabic ? 20 : 0,
              ),
              child: Column(
                crossAxisAlignment: isArabic
                    ? CrossAxisAlignment.end
                    : CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: isArabic
                        ? [
                            const CircleAvatar(
                              radius: 15,
                              backgroundColor: Color(0xFF0F766E),
                              child: Icon(Icons.person, size: 20, color: Colors.white),
                            ),
                            IconButton(
                              icon: const Icon(Icons.close, color: Colors.white),
                              onPressed: () => Navigator.pop(context),
                            ),
                          ]
                        : [
                            IconButton(
                              icon: const Icon(Icons.close, color: Colors.white),
                              onPressed: () => Navigator.pop(context),
                            ),
                            const CircleAvatar(
                              radius: 15,
                              backgroundColor: Color(0xFF0F766E),
                              child: Icon(Icons.person, size: 20, color: Colors.white),
                            ),
                          ],
                  ),
                  const SizedBox(height: 20),
                  Align(
                    alignment: isArabic
                        ? Alignment.centerRight
                        : Alignment.centerLeft,
                    child: Text(
                      "school_name".tr(),
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            Expanded(
              child: ListView(
                padding: EdgeInsets.zero,
                children: [
                  _drawerItem(Icons.person, "Personal File".tr(), () {
                    Navigator.pushNamed(context, MyFilesScreen.screenRoute);
                  }, isArabic),
                  _drawerItem(Icons.credit_card, "Financial File".tr(), () {
                    Navigator.pushNamed(context, 'finance_screen');
                  }, isArabic),
                  _drawerItem(Icons.calendar_today, "Exams Schedule".tr(), () {
                    Navigator.pushNamed(context, 'exams_screen');
                  }, isArabic),
                  _drawerItem(Icons.room_service, "Transport Service".tr(), () {
                    Navigator.pushNamed(context, 'transport_screen');
                  }, isArabic),
                  _drawerItem(Icons.phone, "Contact With Us".tr(), () {
                    Navigator.pushNamed(context, 'contact_us_screen');
                  }, isArabic),
                  _drawerItem(Icons.language, "Change_language".tr(), () {
                    final newLocale = isArabic
                        ? const Locale('en')
                        : const Locale('ar');
                    context.setLocale(newLocale);
                    Navigator.pop(context);
                  }, isArabic),
                  _drawerItem(exitIconForLocale(context), "Logout".tr(), () {
                    _showLogoutDialog();
                  }, isArabic),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _drawerItem(
    IconData icon,
    String title,
    VoidCallback onTap,
    bool isArabic,
  ) {
    return ListTile(
      onTap: onTap,
      contentPadding: const EdgeInsets.symmetric(horizontal: 20),
      leading: Icon(icon, color: Colors.white70),
      title: Align(
        alignment: isArabic ? Alignment.centerRight : Alignment.centerLeft,
        child: Text(
          title,
          style: const TextStyle(color: Colors.white, fontSize: 16),
        ),
      ),
    );
  }

  void _showLogoutDialog() {
    showDialog(
      context: context,
      builder: (dialogContext) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
        child: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [Color(0xFF0F766E), Color(0xFF2563EB)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.all(Radius.circular(15)),
          ),
          padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 10),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Text(
                "Â·  —Ìœ  ”ÃÌ· «·Œ—ÊÃ Õﬁ«ø",
                style: TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 18,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 20),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  TextButton(
                    onPressed: () => Navigator.pop(dialogContext),
                    child: const Text(
                      "·«",
                      style: TextStyle(color: Colors.white, fontSize: 16),
                    ),
                  ),
                  TextButton(
                    onPressed: () async {
                      // „”Õ «·»Ì«‰« 
                      final prefs = await SharedPreferences.getInstance();
                      await prefs.clear();

                      const storage = FlutterSecureStorage();
                      await storage.deleteAll();

                      // «€·ﬁ «·‹ Dialog
                      Navigator.pop(dialogContext);

                      // «€·«ﬁ «· ÿ»Ìﬁ „»«‘—…
                      SystemNavigator.pop(); // √Ê exit(0);
                    },
                    child: const Text(
                      "‰⁄„",
                      style: TextStyle(color: Colors.white, fontSize: 16),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
