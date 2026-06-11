import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:rand/helpers/locale_direction.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ExamsScreen extends StatefulWidget {
  static const String screenRoute = 'exams_screen';
  const ExamsScreen({super.key});

  @override
  State<ExamsScreen> createState() => _ExamsScreenState();
}

class _ExamsScreenState extends State<ExamsScreen> {
  bool loading = true;
  List<dynamic> midterms = [];
  List<dynamic> finals = [];

  String? _token;

  // ---- Load User Data ----
  Future<void> loadUserData() async {
    final prefs = await SharedPreferences.getInstance();
    final secureStorage = const FlutterSecureStorage();

    String? token = await secureStorage.read(key: 'token');
    int? studentId = prefs.getInt('studentId');

    if (token == null || studentId == null) {
      debugPrint("? ·„ Ì „ «·⁄ÀÊ— ⁄·Ï token √Ê studentId");
      setState(() => loading = false);
      return;
    }

    setState(() {
      _token = token;
    });

    // »⁄œ  Õ„Ì· «·»Ì«‰« ° «” œ⁄«¡ fetchExams
    await fetchExams();
  }

  // ---- Fetch Exams ----
  Future<void> fetchExams() async {
    if (_token == null) {
      debugPrint("? ·„ Ì „ «·⁄ÀÊ— ⁄·Ï token ⁄‰œ fetchExams");
      setState(() => loading = false);
      return;
    }

    try {
      final response = await http.get(
        Uri.parse("http://192.168.1.10:8000/api/exams"),
        headers: {
          "Authorization": "Bearer $_token",
          "Accept": "application/json",
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        setState(() {
          midterms = data["data"]["midterms"] ?? [];
          finals = data["data"]["finals"] ?? [];
          loading = false;
        });
      } else {
        debugPrint("?? ›‘·  Õ„Ì· «·«„ Õ«‰« : ${response.statusCode}");
        setState(() => loading = false);
      }
    } catch (e) {
      debugPrint("? Œÿ√ √À‰«¡ Ã·» »Ì«‰«  «·«„ Õ«‰« : $e");
      setState(() => loading = false);
    }
  }

  @override
  void initState() {
    super.initState();
    loadUserData(); // ?  Õ„Ì· token Ê studentId √Ê·«
  }

  // ---- Build Exam Card ----
  Widget buildExamCard(String title, List exams) {
    return Card(
      color: Colors.white.withValues(alpha: 0.15),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
      elevation: 0,
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(
              title,
              textDirection: appTextDirection(context),
              style: const TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 12),
            exams.isEmpty
                ? const Text(
                    "·« ÌÊÃœ »Ì«‰« ",
                    textAlign: TextAlign.center,
                    textDirection: appTextDirection(context),
                    style: TextStyle(color: Colors.white, fontSize: 18),
                  )
                : SizedBox(
                    height: 400,
                    child: SingleChildScrollView(
                      scrollDirection: Axis.vertical,
                      child: SingleChildScrollView(
                        scrollDirection: Axis.horizontal,
                        child: DataTable(
                          columnSpacing: 30,
                          headingRowColor: WidgetStateProperty.all(
                            Colors.black26,
                          ),
                          border: TableBorder.all(color: Colors.white24),
                          columns: const [
                            DataColumn(
                              label: SizedBox(
                                width: 200,
                                child: Center(
                                  child: Text(
                                    "«·„ﬁ——",
                                    textAlign: TextAlign.center,
                                    textDirection: appTextDirection(context),
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                              ),
                            ),
                            DataColumn(
                              label: SizedBox(
                                width: 140,
                                child: Center(
                                  child: Text(
                                    "«·„œ…",
                                    textAlign: TextAlign.center,
                                    textDirection: appTextDirection(context),
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                              ),
                            ),
                            DataColumn(
                              label: Center(
                                child: Text(
                                  "«· «—ÌŒ",
                                  textAlign: TextAlign.center,
                                  textDirection: appTextDirection(context),
                                  style: TextStyle(
                                    color: Colors.white,
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                            ),
                            DataColumn(
                              label: Center(
                                child: Text(
                                  "«·ÌÊ„",
                                  textAlign: TextAlign.center,
                                  textDirection: appTextDirection(context),
                                  style: TextStyle(
                                    color: Colors.white,
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                            ),
                            DataColumn(
                              label: SizedBox(
                                width: 80,
                                child: Center(
                                  child: Text(
                                    "«·„«œ…",
                                    textAlign: TextAlign.center,
                                    textDirection: appTextDirection(context),
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ],
                          rows: exams.map<DataRow>((exam) {
                            return DataRow(
                              cells: [
                                DataCell(
                                  SizedBox(
                                    width: 200,
                                    child: Center(
                                      child: Text(
                                        "${exam['syllabus'] ?? '-'}",
                                        textAlign: TextAlign.center,
                                        textDirection: appTextDirection(context),
                                        style: const TextStyle(
                                          color: Colors.white,
                                          fontSize: 16,
                                        ),
                                      ),
                                    ),
                                  ),
                                ),
                                DataCell(
                                  SizedBox(
                                    width: 140,
                                    child: Center(
                                      child: Text(
                                        exam['duration'] != null
                                            ? (() {
                                                var parts = exam['duration']
                                                    .split('?');
                                                if (parts.length == 2) {
                                                  var start = parts[0]
                                                      .trim()
                                                      .substring(0, 5);
                                                  var end = parts[1]
                                                      .trim()
                                                      .substring(0, 5);
                                                  return "$end ? $start";
                                                }
                                                return exam['duration'];
                                              })()
                                            : '-',
                                        textAlign: TextAlign.center,
                                        textDirection: appTextDirection(context),
                                        style: const TextStyle(
                                          color: Colors.white,
                                          fontSize: 16,
                                        ),
                                      ),
                                    ),
                                  ),
                                ),
                                DataCell(
                                  Center(
                                    child: Text(
                                      "${exam['date'] ?? '-'}",
                                      textAlign: TextAlign.center,
                                      textDirection: appTextDirection(context),
                                      style: const TextStyle(
                                        color: Colors.white,
                                        fontSize: 16,
                                      ),
                                    ),
                                  ),
                                ),
                                DataCell(
                                  Center(
                                    child: Text(
                                      "${exam['day'] ?? '-'}",
                                      textAlign: TextAlign.center,
                                      textDirection: appTextDirection(context),
                                      style: const TextStyle(
                                        color: Colors.white,
                                        fontSize: 16,
                                      ),
                                    ),
                                  ),
                                ),
                                DataCell(
                                  SizedBox(
                                    width: 80,
                                    child: Center(
                                      child: Text(
                                        "${exam['subject'] ?? '-'}",
                                        textAlign: TextAlign.center,
                                        textDirection: appTextDirection(context),
                                        style: const TextStyle(
                                          color: Colors.white,
                                          fontSize: 16,
                                        ),
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            );
                          }).toList(),
                        ),
                      ),
                    ),
                  ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    const purpleColor = Color(0xFF0F766E);
    const greenColor = Color(0xFF2563EB);

    return Scaffold(
      appBar: AppBar(
        title: const Text("ÃœÊ· «·«„ Õ«‰« "),
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
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [purpleColor, greenColor],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: Stack(
          children: [
            Positioned.fill(
              child: Opacity(
                opacity: 0.05,
                child: const Icon( Icons.school_outlined, size: 220, color: Colors.white ),
              ),
            ),
            loading
                ? const Center(child: CircularProgressIndicator())
                : ListView(
                    padding: const EdgeInsets.all(16),
                    children: [
                      buildExamCard("«·«„ Õ«‰ «·‰’›Ì", midterms),
                      const SizedBox(height: 20),
                      buildExamCard("«·«„ Õ«‰ «·‰Â«∆Ì", finals),
                    ],
                  ),
          ],
        ),
      ),
    );
  }
}
