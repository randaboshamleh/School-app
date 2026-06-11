import 'package:flutter/material.dart';
import 'package:rand/helpers/locale_direction.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../services/grades_service.dart';
import 'package:shared_preferences/shared_preferences.dart';

class ExamScore {
  final int? studentId;
  final String subject;
  final String component;
  final double score;
  final double max;
  final double min;
  final bool passed;

  ExamScore({
    required this.studentId,
    required this.subject,
    required this.component,
    required this.score,
    required this.max,
    required this.min,
    required this.passed,
  });

  static double _toDouble(dynamic v, [double fallback = 0]) {
    if (v == null) return fallback;
    if (v is double) return v;
    if (v is int) return v.toDouble();
    if (v is String) return double.tryParse(v) ?? fallback;
    return fallback;
  }

  factory ExamScore.fromJson(Map<String, dynamic> json) {
    final examComponent = json['exam_component'] ?? {};
    final subject = examComponent['subject'] ?? {};

    return ExamScore(
      studentId: json['student_id'] is int
          ? json['student_id'] as int
          : (json['student_id'] is String
                ? int.tryParse(json['student_id'])
                : null),
      subject: (subject['name'] ?? '').toString(),
      component: (examComponent['component_name'] ?? '').toString(),
      score: _toDouble(json['marks_obtained'], 0),
      max: _toDouble(examComponent['max_marks'], 100),
      min: _toDouble(examComponent['min_marks'], 0),
      passed: json['passed'] == null
          ? false
          : (json['passed'] is bool
                ? json['passed'] as bool
                : (json['passed'].toString() == '1' ||
                      json['passed'].toString().toLowerCase() == 'true')),
    );
  }
}

Map<String, Map<String, ExamScore>> groupBySubject(List<ExamScore> scores) {
  final Map<String, Map<String, ExamScore>> grouped = {};
  for (var score in scores) {
    final subject = score.subject;
    final componentKey = score.component.trim();
    grouped.putIfAbsent(subject, () => {});
    grouped[subject]![componentKey] = score;
  }
  return grouped;
}

class GradesScreen extends StatefulWidget {
  const GradesScreen({super.key});

  @override
  State<GradesScreen> createState() => _GradesScreenState();
}

class _GradesScreenState extends State<GradesScreen> {
  bool loading = true;
  Map<String, dynamic>? _gradesData;

  @override
  void initState() {
    super.initState();
    loadUserData();
  }

  Future<void> loadUserData() async {
    setState(() => loading = true);

    try {
      final prefs = await SharedPreferences.getInstance();
      final secureStorage = const FlutterSecureStorage();

      String? token = await secureStorage.read(key: 'token');
      debugPrint("?? Token „‰ FlutterSecureStorage: $token");

      int? studentId = prefs.getInt('studentId');
      debugPrint("?? StudentId „‰ SharedPreferences: $studentId");

      if (token == null || studentId == null) {
        setState(() => loading = false);
        return;
      }

      await fetchExams();
    } catch (e, stack) {
      debugPrint("? «” À‰«¡ √À‰«¡ loadUserData: $e");
      debugPrint('$stack');
      setState(() => loading = false);
    }
  }

  Future<void> fetchExams() async {
    try {
      final data = await GradesService.fetchGrades();
      setState(() {
        _gradesData = data;
        loading = false;
      });
    } catch (e) {
      debugPrint("? Œÿ√ »Ã·» «·⁄·«„« : $e");
      setState(() => loading = false);
    }
  }

  String formatScore(double? score) {
    if (score == null) return "";
    return score.toInt().toString();
  }

  @override
  Widget build(BuildContext context) {
    const purpleColor = Color(0xFF0F766E);
    const greenColor = Color(0xFF2563EB);

    return Scaffold(
      appBar: AppBar(
        title: const Text(
          "⁄·«„«  «·ÿ«·»",
          style: TextStyle(color: Colors.black),
        ),
        centerTitle: true,
        leading: Directionality(
          textDirection: appTextDirection(context),
          child: IconButton(
            icon: Icon(backIconForLocale(context)),
            onPressed: () => Navigator.of(context).pop(),
          ),
        ),
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
      body: Directionality(
        textDirection: appTextDirection(context),
        child: Stack(
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
              child: _gradesData == null && loading
                  ? const Center(
                      child: CircularProgressIndicator(color: Colors.white),
                    )
                  : _gradesData == null
                  ? const Center(
                      child: Text(
                        '·«  ÊÃœ »Ì«‰« ',
                        style: TextStyle(color: Colors.white),
                      ),
                    )
                  : _buildGradesTable(),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildGradesTable() {
    final data = _gradesData!;
    final rawList = data['scores'] as List? ?? [];

    final Map<int, String> paymentStatusMap = {};
    for (var item in rawList) {
      try {
        final sid = item['student_id'];
        final st = item['status'];
        if (sid != null &&
            (sid is int || (sid is String && int.tryParse(sid) != null)) &&
            st != null) {
          final int sidInt = sid is int ? sid : int.parse(sid.toString());
          paymentStatusMap[sidInt] = st.toString();
        }
      } catch (_) {}
    }

    final scores = rawList
        .where((e) {
          final examComponent = e['exam_component'] ?? {};
          final subject = examComponent['subject'] ?? {};
          return (subject['name'] != null &&
                  subject['name'].toString().trim().isNotEmpty) ||
              (examComponent['component_name'] != null &&
                  examComponent['component_name'].toString().trim().isNotEmpty);
        })
        .map((e) => ExamScore.fromJson(Map<String, dynamic>.from(e)))
        .toList();

    if (scores.isEmpty) {
      return const Center(
        child: Text(
          '·«  ÊÃœ ⁄·«„«  Õ«·Ì«',
          style: TextStyle(color: Colors.white, fontSize: 16),
        ),
      );
    }

    final grouped = groupBySubject(scores);

    double examTotal = 0;
    double examMax = 0;
    for (var subject in grouped.keys) {
      final exam = grouped[subject]?['«·«„ Õ«‰'];
      if (exam != null) {
        examTotal += exam.score;
        examMax += exam.max;
      }
    }
    double percentage = examMax > 0 ? (examTotal / examMax * 100) : 0;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        children: [
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: DataTable(
              headingRowColor: WidgetStateProperty.all(Colors.black12),
              border: TableBorder.all(color: Colors.white),
              columns: const [
                DataColumn(
                  label: Text(
                    "«·„«œ…",
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                ),
                DataColumn(
                  label: Text(
                    "«·„–«ﬂ—…",
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                ),
                DataColumn(
                  label: Text(
                    "«·«„ Õ«‰",
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                ),
              ],
              rows: grouped.keys.map((subject) {
                final quiz = grouped[subject]?['«·„–«ﬂ—…'];
                final exam = grouped[subject]?['«·«„ Õ«‰'];

                final int? sid = quiz?.studentId ?? exam?.studentId;
                final currentPaymentStatus = sid != null
                    ? (paymentStatusMap[sid] ?? 'pending')
                    : 'pending';
                final bool isBlocked =
                    (currentPaymentStatus.toLowerCase() == 'overdue' ||
                    currentPaymentStatus.toLowerCase() == 'unpaid');

                final String quizText = isBlocked
                    ? "„ÕÃÊ»… - Ì—ÃÏ  ”œÌœ «·—”Ê„"
                    : (quiz == null || quiz.score == 0
                          ? "_ „‰ ${formatScore(quiz?.max ?? 100)}"
                          : "${formatScore(quiz.score)} „‰ ${formatScore(quiz.max)}");

                final String examText = isBlocked
                    ? "„ÕÃÊ»… - Ì—ÃÏ  ”œÌœ «·—”Ê„"
                    : (exam == null || exam.score == 0
                          ? "_ „‰ ${formatScore(exam?.max ?? 100)}"
                          : "${formatScore(exam.score)} „‰ ${formatScore(exam.max)}");

                // ? «· €ÌÌ—: «··Ê‰ «·√Õ„— ÌŸÂ— ›ﬁÿ ≈–« ﬂ«‰  «·⁄·«„… √ﬁ· „‰ min
                final bool quizFail = quiz != null && quiz.score < quiz.min;
                final bool examFail = exam != null && exam.score < exam.min;

                return DataRow(
                  cells: [
                    DataCell(Text(subject.toString())),
                    DataCell(
                      Container(
                        padding: const EdgeInsets.all(4),
                        color: quizFail ? Colors.red.withValues(alpha: 0.3) : null,
                        child: Text(
                          quizText,
                          overflow: TextOverflow.ellipsis,
                          softWrap: false,
                        ),
                      ),
                    ),
                    DataCell(
                      Container(
                        padding: const EdgeInsets.all(3),
                        color: examFail ? Colors.red.withValues(alpha: 0.3) : null,
                        child: Text(
                          examText,
                          overflow: TextOverflow.ellipsis,
                          softWrap: false,
                        ),
                      ),
                    ),
                  ],
                );
              }).toList(),
            ),
          ),
          const SizedBox(height: 20),
          Text(
            "«·„Ã„Ê⁄ : ${examTotal.toInt()} „‰ ${examMax.toInt()}   -   «·‰”»…: ${percentage.toStringAsFixed(2)}%",
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: Colors.white,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }
}
