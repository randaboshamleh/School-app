import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:rand/screens/home_screen.dart';
import 'package:rand/screens/login_screen.dart';
import 'package:firebase_messaging/firebase_messaging.dart';

// ...import statements ﬂ„« ÂÌ

class RegisterScreen extends StatefulWidget {
  final Future<void> Function(String token) onRegisterSuccess;
  const RegisterScreen({super.key, required this.onRegisterSuccess});
  static const String screenRoute = '/register';

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _nameController = TextEditingController();
  final _studentIdController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  bool _isPasswordVisible = false;
  bool _isConfirmPasswordVisible = false;

  String? _nameError;
  String? _studentIdError;
  String? _passwordError;
  String? _confirmPasswordError;
  String? _successMessage;

  Future<void> sendTokenToServer(String token, int userId) async {
    try {
      final dio = Dio(BaseOptions(baseUrl: "http://192.168.1.10:8000/api"));
      await dio.post(
        "/save-fcm-token",
        data: {"user_id": userId, "fcm_token": token},
      );
    } catch (e) {
      debugPrint("? Œÿ√ »≈—”«· FCM Token ··”Ì—›—: $e");
    }
  }

  Future<void> _register() async {
    setState(() {
      _nameError = null;
      _studentIdError = null;
      _passwordError = null;
      _confirmPasswordError = null;
      _successMessage = null;
    });

    String name = _nameController.text.trim();
    String studentId = _studentIdController.text.trim();
    String password = _passwordController.text.trim();
    String confirmPassword = _confirmPasswordController.text.trim();

    bool hasError = false;
    if (name.isEmpty || name.length < 5) {
      _nameError = "Ì—ÃÏ ≈œŒ«· «”„ ’ÕÌÕ (5 √Õ—› ⁄·Ï «·√ﬁ·)";
      hasError = true;
    }
    if (studentId.isEmpty) {
      _studentIdError = "—ﬁ„ «·ÿ«·» „ÿ·Ê»";
      hasError = true;
    }
    if (password.length < 6) {
      _passwordError = "ﬂ·„… «·„—Ê— ÌÃ» √‰  ﬂÊ‰ 6 √Õ—› ⁄·Ï «·√ﬁ·";
      hasError = true;
    }
    if (password != confirmPassword) {
      _confirmPasswordError = "ﬂ·„… «·„—Ê— €Ì— „ ÿ«»ﬁ…";
      hasError = true;
    }
    if (hasError) {
      setState(() {});
      return;
    }

    try {
      var dio = Dio();
      var response = await dio.post(
        'http://10.0.2.2:8000/api/register',
        data: {
          'name': name,
          'student_id': studentId,
          'password': password,
          'password_confirmation': confirmPassword,
        },
        options: Options(headers: {'Accept': 'application/json'}),
      );

      if (response.statusCode == 201) {
        final token = response.data['token'];
        int userId = response.data['student']['id'];

        // ? Õ›Ÿ «· Êﬂ‰ Ê «·‹ studentId ›Ì FlutterSecureStorage
        await _storage.write(key: 'token', value: token);
        await _storage.write(key: 'studentId', value: userId.toString());

        // ? Õ›Ÿ «· Êﬂ‰ Ê «·‹ studentId ›Ì SharedPreferences
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', token);
        await prefs.setInt('studentId', userId);

        // ?? ≈—”«· FCM Token ··”Ì—›—
        String? fcmToken = await FirebaseMessaging.instance.getToken();
        if (fcmToken != null) {
          await sendTokenToServer(fcmToken, userId);
        }

        setState(() => _successMessage = "?  „ ≈‰‘«¡ «·Õ”«» »‰Ã«Õ!");
        Future.delayed(const Duration(seconds: 1), () {
          if (!mounted) return;
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(builder: (context) => const HomeScreen()),
          );
        });
      }
    } on DioException catch (e) {
      if (e.response != null && e.response?.data != null) {
        var data = e.response!.data;
        switch (data['code']) {
          case 'NAME_NOT_MATCH':
            setState(() => _nameError = data['message']);
            break;
          case 'STUDENT_ID_NOT_MATCH':
            setState(() => _studentIdError = data['message']);
            break;
          case 'INVALID':
            setState(() {
              _nameError = data['message'];
              _studentIdError = data['message'];
            });
            break;
          case 'ALREADY_REGISTERED':
            setState(() => _successMessage = data['message']);
            break;
          default:
            setState(
              () => _successMessage = "ÕœÀ Œÿ√ €Ì— „ Êﬁ⁄° Ì—ÃÏ «·„Õ«Ê·… ·«Õﬁ«",
            );
        }
      }
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
              opacity: 0.05,
              child: const Icon( Icons.school_outlined, size: 220, color: Colors.white ),
            ),
          ),
          SafeArea(
            child: Center(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(24.0),
                child: Column(
                  children: [
                    const Text(
                      "»Ê«»… «·„œ—”…",
                      style: TextStyle(
                        fontSize: 33,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 15),
                    TextField(
                      controller: _nameController,
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: Colors.white,
                        labelText: "«·«”„ «·ﬂ«„·",
                        prefixIcon: const Icon(
                          Icons.person,
                          color: purpleColor,
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      onChanged: (_) => setState(() => _nameError = null),
                    ),
                    if (_nameError != null)
                      Padding(
                        padding: const EdgeInsets.only(top: 4),
                        child: Text(
                          _nameError!,
                          style: const TextStyle(
                            color: Colors.red,
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                          ),
                        ),
                      ),
                    const SizedBox(height: 15),
                    TextField(
                      controller: _studentIdController,
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: Colors.white,
                        labelText: "—ﬁ„ «·ÿ«·»",
                        prefixIcon: const Icon(
                          Icons.confirmation_number,
                          color: purpleColor,
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      onChanged: (_) => setState(() => _studentIdError = null),
                    ),
                    if (_studentIdError != null)
                      Padding(
                        padding: const EdgeInsets.only(top: 4),
                        child: Text(
                          _studentIdError!,
                          style: const TextStyle(
                            color: Colors.red,
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
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
                          onPressed: () => setState(
                            () => _isPasswordVisible = !_isPasswordVisible,
                          ),
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      onChanged: (_) => setState(() => _passwordError = null),
                    ),
                    if (_passwordError != null)
                      Padding(
                        padding: const EdgeInsets.only(top: 4),
                        child: Text(
                          _passwordError!,
                          style: const TextStyle(
                            color: Colors.red,
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                          ),
                        ),
                      ),
                    const SizedBox(height: 15),
                    TextField(
                      controller: _confirmPasswordController,
                      obscureText: !_isConfirmPasswordVisible,
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: Colors.white,
                        labelText: " √ﬂÌœ ﬂ·„… «·„—Ê—",
                        prefixIcon: const Icon(Icons.lock, color: purpleColor),
                        suffixIcon: IconButton(
                          icon: Icon(
                            _isConfirmPasswordVisible
                                ? Icons.visibility
                                : Icons.visibility_off,
                          ),
                          onPressed: () => setState(
                            () => _isConfirmPasswordVisible =
                                !_isConfirmPasswordVisible,
                          ),
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      onChanged: (_) =>
                          setState(() => _confirmPasswordError = null),
                    ),
                    if (_confirmPasswordError != null)
                      Padding(
                        padding: const EdgeInsets.only(top: 4),
                        child: Text(
                          _confirmPasswordError!,
                          style: const TextStyle(
                            color: Colors.red,
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                          ),
                        ),
                      ),
                    const SizedBox(height: 20),
                    if (_successMessage != null)
                      Padding(
                        padding: const EdgeInsets.symmetric(vertical: 8),
                        child: Text(
                          _successMessage!,
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            color: _successMessage!.contains("?")
                                ? Colors.blue
                                : Colors.red,
                            fontWeight: FontWeight.bold,
                            fontSize: 16,
                          ),
                        ),
                      ),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _register,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: purpleColor,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        child: const Text(
                          '≈‰‘«¡ Õ”«»',
                          style: TextStyle(fontSize: 18),
                        ),
                      ),
                    ),
                    TextButton(
                      onPressed: () {
                        Navigator.pushReplacement(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const LoginScreen(),
                          ),
                        );
                      },
                      child: const Text(
                        "·œÌﬂ Õ”«»ø  ”ÃÌ· «·œŒÊ·",
                        style: TextStyle(color: Colors.white),
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
