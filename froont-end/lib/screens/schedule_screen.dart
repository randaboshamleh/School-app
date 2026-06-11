import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import 'package:collection/collection.dart'; // ·· Ã„Ì⁄ Õ”» «·ÌÊ„

class ScheduleScreen extends StatefulWidget {
  const ScheduleScreen({super.key});

  @override
  State<ScheduleScreen> createState() => _ScheduleScreenState();
}

class _ScheduleScreenState extends State<ScheduleScreen> {
  final Dio dio = Dio();
  List<dynamic> weeklySchedule = [];
  bool isLoading = true;

  Future<void> fetchSchedule() async {
    try {
      final response = await dio.get("http://192.168.1.10:8000/api/timetable");

      debugPrint("Response Data: ${response.data}");

      if (response.statusCode == 200) {
        setState(() {
          weeklySchedule = response.data as List;
          isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("? Error fetching schedule: $e");
      setState(() {
        isLoading = false;
      });
    }
  }

  @override
  void initState() {
    super.initState();
    fetchSchedule();
  }

  String lessonOrderInArabic(int index) {
    switch (index) {
      case 1:
        return "«·√Ê·Ï";
      case 2:
        return "«·À«‰Ì…";
      case 3:
        return "«·À«·À…";
      case 4:
        return "«·—«»⁄…";
      case 5:
        return "«·Œ«„”…";
      case 6:
        return "«·”«œ”…";
      default:
        return "$index";
    }
  }

  @override
  Widget build(BuildContext context) {
    const purpleColor = Color(0xFF0F766E);
    const greenColor = Color(0xFF2563EB);

    final daysOrder = ["«·√Õœ", "«·≈À‰Ì‰", "«·À·«À«¡", "«·√—»⁄«¡", "«·Œ„Ì”"];

    final groupedByDay = groupBy(
      weeklySchedule,
      (lesson) =>
          (lesson["day"] ?? "€Ì— „Õœœ").toString().trim().replaceAll(" ", ""),
    );

    final sortedEntries = groupedByDay.entries.toList()
      ..sort(
        (a, b) => daysOrder.indexOf(a.key).compareTo(daysOrder.indexOf(b.key)),
      );

    return Scaffold(
      appBar: AppBar(
        title: const Text("«·ÃœÊ· «·√”»Ê⁄Ì", textAlign: TextAlign.center),
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
          // Œ·›Ì… „ œ—Ã…
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

          // «·„Õ ÊÏ «·—∆Ì”Ì
          isLoading
              ? const Center(child: CircularProgressIndicator())
              : weeklySchedule.isEmpty
              ? const Center(
                  child: Text(
                    "·« ÌÊÃœ ÃœÊ· Õ«·Ì«",
                    style: TextStyle(color: Colors.white, fontSize: 18),
                  ),
                )
              : ListView(
                  padding: const EdgeInsets.all(16),
                  children: sortedEntries.map((entry) {
                    final day = entry.key;
                    final lessons = entry.value;

                    return Card(
                      color: Colors.white.withValues(alpha: 0.2),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                      margin: const EdgeInsets.only(bottom: 16),
                      child: Padding(
                        padding: const EdgeInsets.all(12),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Center(
                              child: Text(
                                day,
                                style: const TextStyle(
                                  fontSize: 22,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.white,
                                ),
                              ),
                            ),
                            const SizedBox(height: 8),
                            const Divider(color: Colors.white70),
                            ...lessons.asMap().entries.map((entry) {
                              final index = entry.key;
                              final lesson = entry.value;
                              final subject = lesson["subject"] ?? "€Ì— „Õœœ";

                              return Padding(
                                padding: const EdgeInsets.symmetric(
                                  vertical: 4,
                                ),
                                child: Row(
                                  textDirection: TextDirection
                                      .rtl, // Â–« ÌÃ⁄· „Õ ÊÌ«  «·‹ Row „‰ «·Ì„Ì‰ ··Ì”«—
                                  children: [
                                    Flexible(
                                      child: Text(
                                        "«·Õ’… ${lessonOrderInArabic(index + 1)} : $subject",
                                        style: const TextStyle(
                                          color: Colors.white,
                                          fontSize: 16,
                                        ),
                                        textAlign: TextAlign.right,
                                      ),
                                    ),
                                  ],
                                ),
                              );
                            }),
                          ],
                        ),
                      ),
                    );
                  }).toList(),
                ),
        ],
      ),
    );
  }
}
