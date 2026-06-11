import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:rand/services/auth_service.dart';
import 'package:dio/dio.dart';
import 'package:rand/screens/forgot_password_screen.dart';
import 'package:rand/screens/home_screen.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import '../dashboard_page.dart';

class LoginScreen extends StatefulWidget {
  final Future<void> Function(String token)? onLoginSuccess;
  const LoginScreen({super.key, this.onLoginSuccess});
  static const String screenRoute = '/login';

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _studentIdController = TextEditingController();
  final _passwordController = TextEditingController();
  final AuthService _authService = AuthService();
  final FlutterSecureStorage _secureStorage = const FlutterSecureStorage();
  bool _isPasswordVisible = false;
  bool _loading = false;

  // ? Õ›Ÿ studentId ›Ì SharedPreferences
  Future<void> saveStudentId(int studentId) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt('studentId', studentId);
  }

  // ? ≈—”«· FCM token ··”Ì—›—
  Future<void> sendTokenToServer(String token, int userId) async {
    try {
      final dio = Dio(
        BaseOptions(
          baseUrl: "http://192.168.1.10:8000/api",
          headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
          },
          connectTimeout: const Duration(seconds: 10),
          receiveTimeout: const Duration(seconds: 10),
        ),
      );
      await dio.post(
        "/save-fcm-token",
        data: {"user_id": userId, "fcm_token": token},
      );
      debugPrint("?  „ ≈—”«· FCM Token ··”Ì—›— »‰Ã«Õ");
    } catch (e) {
      debugPrint("? Œÿ√ √À‰«¡ ≈—”«· FCM Token ··”Ì—›—: $e");
    }
  }

  // ? Ê«ÃÂ… "‰”Ì  ﬂ·„… «·„—Ê—"
  void _forgotPassword() {
    Navigator.pushNamed(context, ForgotPasswordScreen.screenRoute);
  }

  // ? ⁄—÷ —”«·… Õ«·… ⁄«„…
  void _showStatusMessage(String message, {bool isError = false}) {
    final scaffoldMessenger = ScaffoldMessenger.of(context);
    scaffoldMessenger.hideCurrentSnackBar();
    scaffoldMessenger.showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: isError ? Colors.red : Colors.blue,
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.all(10),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        duration: const Duration(seconds: 3),
      ),
    );
  }

  // ?  ”ÃÌ· «·œŒÊ·
  Future<void> _login() async {
    final studentId = _studentIdController.text.trim();
    final password = _passwordController.text.trim();

    if (studentId.isEmpty) {
      _showStatusMessage('—ﬁ„ «·ÿ«·» „ÿ·Ê»', isError: true);
      return;
    }
    if (password.isEmpty || password.length < 6) {
      _showStatusMessage(
        'ﬂ·„… «·„—Ê— ÌÃ» √‰  ﬂÊ‰ 6 √Õ—› ⁄·Ï «·√ﬁ·',
        isError: true,
      );
      return;
    }

    setState(() => _loading = true);

    try {
      final data = await _authService.login(studentId, password);
      if (!mounted) return;
      debugPrint("?? login response: $data");

      // ? «· Õﬁﬁ „‰ «·√Œÿ«¡ «·ﬁ«œ„… „‰ «·”Ì—›—
      if (data['status'] == 'error') {
        String code = data['code'] ?? '';
        String message = data['message'] ?? 'ÕœÀ Œÿ√';

        if (code == 'STUDENT_ID_NOT_FOUND' || code == 'PASSWORD_INCORRECT') {
          _showStatusMessage(message, isError: true);
        } else if (code == 'NOT_REGISTERED') {
          showDialog(
            context: context,
            builder: (context) => AlertDialog(
              title: const Text(" ‰»ÌÂ"),
              content: Text(message, style: const TextStyle(fontSize: 16)),
              actions: [
                TextButton(
                  onPressed: () {
                    Navigator.pop(context);
                    Navigator.pushReplacementNamed(context, '/register');
                  },
                  child: const Text(
                    "«·–Â«» ·≈‰‘«¡ Õ”«»",
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                ),
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text("≈·€«¡"),
                ),
              ],
            ),
          );
        } else {
          _showStatusMessage(message, isError: true);
        }
        return;
      }

      // ? «” Œ—«Ã «·‹ token
      final token = data['token'] ?? data['access_token'];
      if (token == null) {
        _showStatusMessage(
          '›‘·  ”ÃÌ· «·œŒÊ·: ·« ÌÊÃœ  Êﬂ‰ ›Ì «·—œ',
          isError: true,
        );
        return;
      }

      // ? «” Œ—«Ã userId / studentId
      int? userId;
      if (data['student'] != null && data['student']['student_id'] != null) {
        userId = int.tryParse(data['student']['student_id'].toString());
      } else if (data['user'] != null && data['user']['id'] != null) {
        userId = data['user']['id'];
      }

      if (userId == null) {
        _showStatusMessage(
          '?? ·„ Ì „ «·⁄ÀÊ— ⁄·Ï —ﬁ„ «·ÿ«·» ›Ì «·—œ',
          isError: true,
        );
        return;
      }

      // ? Õ›Ÿ «·»Ì«‰«  „Õ·Ì«
      await _secureStorage.write(key: 'token', value: token);
      await _secureStorage.write(key: 'studentId', value: userId.toString());

      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('token', token);
      await prefs.setInt('studentId', userId);

      // ? Õ›Ÿ FCM Token Ê≈—”«·Â ··”Ì—›—
      String? fcmToken = await FirebaseMessaging.instance.getToken();
      if (fcmToken != null && fcmToken.isNotEmpty) {
        await sendTokenToServer(fcmToken, userId);
      } else {
        debugPrint("?? ·„ Ì „ «” ·«„ FCM Token „‰ «·ÃÂ«“!");
      }

      // ? «·«‰ ﬁ«· ≈·Ï «·‘«‘… «·—∆Ì”Ì…
      if (!mounted) return;
      String userRole = 'student';

      if (data['user'] != null && data['user']['role'] != null) {
        userRole = data['user']['role'].toString();
      } else if (data['student'] != null && data['student']['role'] != null) {
        userRole = data['student']['role'].toString();
      }

      await prefs.setString('role', userRole);
      if (!mounted) return;

      if (userRole == 'admin' || userRole.contains('supervisor')) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (_) => DashboardPage(token: token, role: userRole),
          ),
        );
      } else {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => const HomeScreen()),
        );
      }
      _showStatusMessage('?  „  ”ÃÌ· «·œŒÊ· »‰Ã«Õ');
    } catch (e, st) {
      debugPrint("?? login error: $e\n$st");
      _showStatusMessage(
        ' ⁄–— «·« ’«· »«·”Ì—›— √Ê »Ì«‰«  Œ«ÿ∆…',
        isError: true,
      );
    } finally {
      if (mounted) {
        setState(() => _loading = false);
      }
    }
  }

  // ? Ê«ÃÂ… «·„” Œœ„
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
                        labelText: '—ﬁ„ «·ÿ«·»',
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
                    const SizedBox(height: 15),
                    TextField(
                      controller: _passwordController,
                      obscureText: !_isPasswordVisible,
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: Colors.white,
                        labelText: "ﬂ·„… «·„—Ê—",
                        prefixIcon: const Icon(Icons.lock, color: purpleColor),
                        suffixIcon: IconButton(
                          icon: Icon(
                            _isPasswordVisible
                                ? Icons.visibility
                                : Icons.visibility_off,
                          ),
                          onPressed: () {
                            setState(() {
                              _isPasswordVisible = !_isPasswordVisible;
                            });
                          },
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
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _loading ? null : _login,
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
                                ' ”ÃÌ· «·œŒÊ·',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                      ),
                    ),
                    const SizedBox(height: 10),
                    Center(
                      child: TextButton(
                        onPressed: _forgotPassword,
                        child: const Text(
                          "‰”Ì  ﬂ·„… «·„—Ê—ø",
                          style: TextStyle(color: Colors.white),
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
