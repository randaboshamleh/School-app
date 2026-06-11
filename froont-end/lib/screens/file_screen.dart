import 'package:flutter/material.dart';
import 'package:rand/helpers/locale_direction.dart';
import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:easy_localization/easy_localization.dart';

class MyFilesScreen extends StatefulWidget {
  static const String screenRoute = 'file_screen';
  const MyFilesScreen({super.key});

  @override
  State<MyFilesScreen> createState() => _MyFilesScreenState();
}

class _MyFilesScreenState extends State<MyFilesScreen> {
  final Dio dio = Dio();
  Map<String, dynamic>? studentData;
  bool isLoading = true;

  String? _token;
  int? _studentId;

  Future<void> loadUserData() async {
    final prefs = await SharedPreferences.getInstance();
    final secureStorage = const FlutterSecureStorage();

    String? token = await secureStorage.read(key: 'token');
    int? studentId = prefs.getInt('studentId');

    if (token == null || studentId == null) {
      debugPrint("? ·„ Ì „ «·⁄ÀÊ— ⁄·Ï token √Ê studentId");
      setState(() => isLoading = false);
      return;
    }

    setState(() {
      _token = token;
      _studentId = studentId;
    });

    await fetchStudentData();
  }

  String translateGender(String gender) {
    switch (gender) {
      case "male":
        return "–ﬂ—";
      case "female":
        return "√‰ÀÏ";
      default:
        return gender;
    }
  }

  String translateStatus(String status) {
    switch (status) {
      case "active":
        return "„”Ã·";
      case "graduated":
        return "„ Œ—Ã";
      case "suspended":
        return "„ÊﬁÊ›";
      case "withdrawn":
        return "„‰”Õ»";
      default:
        return status;
    }
  }

  Future<void> fetchStudentData() async {
    if (_token == null || _studentId == null) {
      debugPrint("? ·« Ì„ﬂ‰ Ã·» «·»Ì«‰«  »œÊ‰ token √Ê studentId");
      setState(() => isLoading = false);
      return;
    }

    try {
      final response = await dio.get(
        "http://192.168.1.10:8000/api/students/$_studentId",
        options: Options(
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $_token',
          },
        ),
      );

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = Map<String, dynamic>.from(
          response.data,
        );
        if (data.containsKey('student')) {
          setState(() {
            studentData = Map<String, dynamic>.from(data['student']);
            isLoading = false;
          });
        } else {
          debugPrint("?? API ·„  —Ã⁄ «·„› «Õ 'student'");
          setState(() => isLoading = false);
        }
      }
    } catch (e) {
      debugPrint("? Error fetching student data: $e");
      setState(() => isLoading = false);
    }
  }

  @override
  void initState() {
    super.initState();
    loadUserData();
  }

  @override
  Widget build(BuildContext context) {
    const purpleColor = Color(0xFF0F766E);
    const greenColor = Color(0xFF2563EB);

    //  ÕœÌœ «··€… Õ«·Ì«
    bool isArabic = isArabicLocale(context);

    return Scaffold(
      appBar: AppBar(
        title: Text("„·›« Ì", textAlign: TextAlign.center),
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
        leading: IconButton(
          icon: Icon(backIconForLocale(context), color: Colors.black),
          onPressed: () => Navigator.pop(context),
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
          Positioned.fill(
            child: Opacity(
              opacity: 0.05,
              child: const Icon( Icons.school_outlined, size: 220, color: Colors.white ),
            ),
          ),
          isLoading
              ? const Center(
                  child: CircularProgressIndicator(color: Colors.white),
                )
              : studentData == null
              ? Center(
                  child: Text(
                    "·„ Ì „ «·⁄ÀÊ— ⁄·Ï »Ì«‰«  «·ÿ«·»",
                    style: const TextStyle(color: Colors.white, fontSize: 18),
                    textAlign: isArabic ? TextAlign.right : TextAlign.left,
                  ),
                )
              : ListView(
                  padding: const EdgeInsets.all(16),
                  children: [
                    Card(
                      color: Colors.white.withValues(alpha: 0.15),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                      margin: const EdgeInsets.symmetric(vertical: 8),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            infoRow(
                              "«·«”„ «·ﬂ«„·",
                              studentData!["name"] ?? "€Ì— „ Ê›—",
                              Icons.person,
                              true,
                              isArabic,
                            ),
                            infoRow(
                              "«·’› Ê«·‘⁄»…",
                              "${(studentData!["class"] ?? "-").toString().trim()}-${(studentData!["section"] ?? "-").toString().trim()}",
                              Icons.class_,
                              true,
                              isArabic,
                            ),

                            infoRow(
                              "«·—ﬁ„ «·„œ—”Ì",
                              (studentData!["student_id"]?.toString() ??
                                      "€Ì— „ Ê›—")
                                  .trim(),
                              Icons.badge,
                              true,
                              isArabic,
                            ),

                            infoRow(
                              "«·„⁄œ· «·›’·Ì",
                              "${(studentData!["gpa"]?.toString() ?? "0").trim()}%",
                              Icons.school,
                              true,
                              isArabic,
                            ),
                            infoRow(
                              "«·⁄‰Ê«‰",
                              studentData!["address"] ?? "€Ì— „ Ê›—",
                              Icons.home,
                              true,
                              isArabic,
                            ),
                            infoRow(
                              "«·Ã‰”Ì…",
                              studentData!["nationality"] ?? "€Ì— „ Ê›—",
                              Icons.flag,
                              true,
                              isArabic,
                            ),
                            infoRow(
                              "«·Ã‰”",
                              translateGender(studentData!["gender"] ?? ""),
                              Icons.wc,
                              true,
                              isArabic,
                            ),
                            infoRow(
                              "«·Õ«·…",
                              translateStatus(studentData!["status"] ?? ""),
                              Icons.check_circle,
                              false,
                              isArabic,
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
        ],
      ),
    );
  }

  Widget infoRow(
    String title,
    String value,
    IconData icon,
    bool hasDivider,
    bool isArabic,
  ) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: Colors.white, size: 24),
            const SizedBox(width: 8), // „”«›… »”Ìÿ… ›ﬁÿ
            Flexible(
              child: Column(
                crossAxisAlignment:
                    CrossAxisAlignment.start, // «·‰’Ê’ „»«‘—… »⁄œ «·√ÌﬁÊ‰…
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      color: Colors.white70,
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    value.trim(),
                    style: const TextStyle(color: Colors.white, fontSize: 18),
                  ),
                ],
              ),
            ),
          ],
        ),
        if (hasDivider)
          const Divider(color: Colors.white24, thickness: 1, height: 16),
      ],
    );
  }
}
