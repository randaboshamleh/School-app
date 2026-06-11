import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:rand/screens/home_screen.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import '../helpers/shared_prefs.dart';
import 'package:shared_preferences/shared_preferences.dart';

Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await FlutterLocalNotificationsPlugin().show(
    message.hashCode,
    message.notification?.title,
    message.notification?.body,
    const NotificationDetails(
      android: AndroidNotificationDetails(
        'channel_id',
        'channel_name',
        importance: Importance.max,
        priority: Priority.high,
      ),
    ),
  );
}

class ForgotPasswordScreen extends StatefulWidget {
  static const String screenRoute = 'forgot_password_screen';
  const ForgotPasswordScreen({super.key});

  @override
  State<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen> {
  final _studentIdController = TextEditingController();
  final Dio _dio = Dio(BaseOptions(baseUrl: "http://192.168.1.10:8000/api"));
  String? _statusMessage;
  bool _loading = false;
  bool _fcmInitialized = false;

  final FlutterLocalNotificationsPlugin _flutterLocalNotificationsPlugin =
      FlutterLocalNotificationsPlugin();

  @override
  void initState() {
    super.initState();
    _setupFCM();
    _loadOtpSentStatus();
  }

  bool _otpSent = false;

  Future<void> _loadOtpSentStatus() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _otpSent = prefs.getBool('otp_sent') ?? false;
    });
  }

  Future<void> _setOtpSent(bool value) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool('otp_sent', value);
    setState(() {
      _otpSent = value;
    });
  }

  void _setupFCM() async {
    if (_fcmInitialized) return;

    const androidSettings = AndroidInitializationSettings(
      '@mipmap/ic_launcher',
    );
    const initSettings = InitializationSettings(android: androidSettings);

    await _flutterLocalNotificationsPlugin.initialize(initSettings);
    FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);

    // «” „⁄ ›ﬁÿ „—… Ê«Õœ… ›Ì  ÿ»Ìﬁ ﬂ«„·
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      if (!_otpSent && message.notification != null) {
        _flutterLocalNotificationsPlugin.show(
          message.hashCode,
          message.notification!.title,
          message.notification!.body,
          const NotificationDetails(
            android: AndroidNotificationDetails(
              'channel_id',
              'channel_name',
              importance: Importance.max,
              priority: Priority.high,
            ),
          ),
        );
        _setOtpSent(true);
      }
    });

    _fcmInitialized = true;
  }

  Future<void> _saveFcmToken(String token, String studentId) async {
    try {
      await _dio.post(
        '/save-fcm-token',
        data: {'student_id': studentId, 'fcm_token': token},
      );
      debugPrint('?  „ Õ›Ÿ FCM token »‰Ã«Õ');
    } catch (e) {
      debugPrint('?? Œÿ√ √À‰«¡ Õ›Ÿ FCM token: $e');
    }
  }

  Future<void> _sendResetRequest() async {
    if (_otpSent) return; // Ì„‰⁄ «·≈—”«· «·„ﬂ——

    String studentId = _studentIdController.text.trim();
    if (studentId.isEmpty) {
      setState(() => _statusMessage = "—ﬁ„ «·ÿ«·» „ÿ·Ê»");
      return;
    }

    setState(() {
      _loading = true;
      _otpSent = true; // ⁄·„ √‰Â  „ «·≈—”«·
    });

    try {
      String? fcmToken = await FirebaseMessaging.instance.getToken();

      if (fcmToken != null && fcmToken.isNotEmpty) {
        await _saveFcmToken(fcmToken, studentId);
      }

      final response = await _dio.post(
        '/forgot-password',
        data: {'student_id': studentId, 'fcm_token': fcmToken},
      );

      if (response.statusCode == 200) {
        setState(() => _statusMessage = "?  „ ≈—”«· —„“ ≈⁄«œ… «· ⁄ÌÌ‰ »‰Ã«Õ");

        Future.delayed(const Duration(seconds: 1), () async {
          if (response.data['token'] != null) {
            String newToken = response.data['token'];
            await saveToken(newToken);
          }

          if (context.mounted) {
            await Future.delayed(const Duration(milliseconds: 300));
            if (!context.mounted) return;
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => ResetPasswordScreen(studentId: studentId),
              ),
            );
          }
        });
      } else {
        setState(
          () => _statusMessage = "?? ·„ Ì „ ≈—”«· «·—„“°  Õﬁﬁ „‰ «·»Ì«‰« ",
        );
      }
    } catch (e) {
      debugPrint("? Œÿ√ √À‰«¡ «·≈—”«·: $e");
      setState(() => _statusMessage = "?? ›‘· «·« ’«· »«·”Ì—›—");
    } finally {
      setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    const purpleColor = Color(0xFF0F766E);
    const greenColor = Color(0xFF2563EB);

    return Scaffold(
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
              opacity: 0.07,
              child: const Icon( Icons.school_outlined, size: 220, color: Colors.white ),
            ),
          ),
          SafeArea(
            child: Center(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(24.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    const Text(
                      "»Ê«»… «·„œ—”…",
                      style: TextStyle(
                        fontSize: 33,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 25),
                    TextField(
                      controller: _studentIdController,
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: Colors.white,
                        labelText: "—ﬁ„ «·ÿ«·»",
                        prefixIcon: const Icon(
                          Icons.person,
                          color: purpleColor,
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        focusedBorder: const OutlineInputBorder(
                          borderSide: BorderSide(color: purpleColor, width: 2),
                        ),
                      ),
                    ),
                    const SizedBox(height: 20),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _loading ? null : _sendResetRequest,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: purpleColor,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        child: _loading
                            ? const CircularProgressIndicator(
                                color: Colors.white,
                              )
                            : const Text(
                                '≈—”«· «·—„“',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                      ),
                    ),
                    if (_statusMessage != null) ...[
                      const SizedBox(height: 15),
                      Text(
                        _statusMessage!,
                        style: const TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ],
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class ResetPasswordScreen extends StatefulWidget {
  final String studentId;
  const ResetPasswordScreen({super.key, required this.studentId});

  @override
  State<ResetPasswordScreen> createState() => _ResetPasswordScreenState();
}

class _ResetPasswordScreenState extends State<ResetPasswordScreen> {
  final _tokenController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmController = TextEditingController();
  final Dio _dio = Dio(BaseOptions(baseUrl: "http://192.168.1.10:8000/api"));
  bool _isPasswordVisible = false;
  bool _isConfirmVisible = false;
  bool _loading = false;
  String? _statusMessage;

  Future<void> _resetPassword() async {
    if (_passwordController.text != _confirmController.text) {
      setState(() => _statusMessage = "ﬂ·„… «·„—Ê— €Ì— „ ÿ«»ﬁ…");
      return;
    }

    setState(() => _loading = true);

    try {
      final response = await _dio.post(
        '/reset-password',
        data: {
          'student_id': widget.studentId,
          'otp': _tokenController.text,
          'password': _passwordController.text,
          'password_confirmation': _confirmController.text,
        },
        options: Options(
          contentType: Headers.jsonContentType,
          headers: {'Accept': 'application/json'},
        ),
      );

      debugPrint("? Status Code: ${response.statusCode}");
      debugPrint("? Response Data: ${response.data}");

      if (response.statusCode == 200 && response.data is Map<String, dynamic>) {
        setState(
          () => _statusMessage =
              "? ${response.data['message'] ?? " „  €ÌÌ— ﬂ·„… «·„—Ê— »‰Ã«Õ"}",
        );

        if (mounted) {
          // «· ⁄·Ìﬁ „ƒﬁ « ·Õ· „‘ﬂ·… «·‹ token
          // if (response.data['token'] != null) {
          //   await saveToken(response.data['token']);
          // }

          // «·«‰ ﬁ«· ··’›Õ… «·—∆Ì”Ì… »⁄œ 1 À«‰Ì…
          Future.delayed(const Duration(seconds: 1), () {
            if (!mounted) return;
            Navigator.pushAndRemoveUntil(
                context,
                MaterialPageRoute(builder: (_) => HomeScreen()),
                (route) => false,
              );
          });
        }
      } else {
        setState(
          () => _statusMessage =
              "? ÕœÀ Œÿ√:  Õﬁﬁ „‰ «·»Ì«‰«  √Ê «” Ã«»… «·”Ì—›— €Ì— ’ÕÌÕ…",
        );
      }
    } on DioException catch (e) {
      debugPrint("? DioException: $e");
      if (e.response != null) {
        debugPrint("? Dio Response Data: ${e.response!.data}");
        debugPrint("? Dio Status Code: ${e.response!.statusCode}");
      }
      setState(() => _statusMessage = "? ›‘· «·« ’«· »«·”Ì—›—");
    } finally {
      setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    const purpleColor = Color(0xFF0F766E);
    const greenColor = Color(0xFF2563EB);

    return Scaffold(
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
              opacity: 0.07,
              child: const Icon( Icons.school_outlined, size: 220, color: Colors.white ),
            ),
          ),
          SafeArea(
            child: Center(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(24.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    const Text(
                      " ⁄ÌÌ‰ ﬂ·„… „—Ê— ÃœÌœ…",
                      style: TextStyle(
                        fontSize: 28,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 25),
                    TextField(
                      controller: _tokenController,
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: Colors.white,
                        labelText: "«·—„“ «·„—”· (OTP)",
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        focusedBorder: const OutlineInputBorder(
                          borderSide: BorderSide(color: purpleColor, width: 2),
                        ),
                      ),
                    ),
                    const SizedBox(height: 15),
                    TextField(
                      controller: _passwordController,
                      obscureText: !_isPasswordVisible,
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: Colors.white,
                        labelText: "ﬂ·„… «·„—Ê— «·ÃœÌœ…",
                        prefixIcon: const Icon(Icons.lock, color: purpleColor),
                        suffixIcon: IconButton(
                          icon: Icon(
                            _isPasswordVisible
                                ? Icons.visibility
                                : Icons.visibility_off,
                          ),
                          onPressed: () => setState(() {
                            _isPasswordVisible = !_isPasswordVisible;
                          }),
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        focusedBorder: const OutlineInputBorder(
                          borderSide: BorderSide(color: purpleColor, width: 2),
                        ),
                      ),
                    ),
                    const SizedBox(height: 15),
                    TextField(
                      controller: _confirmController,
                      obscureText: !_isConfirmVisible,
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: Colors.white,
                        labelText: " √ﬂÌœ ﬂ·„… «·„—Ê—",
                        prefixIcon: const Icon(Icons.lock, color: purpleColor),
                        suffixIcon: IconButton(
                          icon: Icon(
                            _isConfirmVisible
                                ? Icons.visibility
                                : Icons.visibility_off,
                          ),
                          onPressed: () => setState(() {
                            _isConfirmVisible = !_isConfirmVisible;
                          }),
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        focusedBorder: const OutlineInputBorder(
                          borderSide: BorderSide(color: purpleColor, width: 2),
                        ),
                      ),
                    ),
                    const SizedBox(height: 20),
                    if (_statusMessage != null) ...[
                      Text(
                        _statusMessage!,
                        style: TextStyle(
                          color: _statusMessage!.contains("?")
                              ? Colors.blue
                              : Colors.red,
                          fontWeight: FontWeight.bold,
                        ),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 15),
                    ],
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _loading ? null : _resetPassword,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: purpleColor,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        child: _loading
                            ? const CircularProgressIndicator(
                                color: Colors.white,
                              )
                            : const Text(
                                ' €ÌÌ— ﬂ·„… «·„—Ê—',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
