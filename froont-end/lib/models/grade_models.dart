class ExamScore {
  final String subject;
  final String component;
  final double marksObtained;
  final double maxMarks;
  final double minMarks;
  final bool passed;

  ExamScore({
    required this.subject,
    required this.component,
    required this.marksObtained,
    required this.maxMarks,
    required this.minMarks,
    required this.passed,
  });

  factory ExamScore.fromJson(Map<String, dynamic> json) {
    return ExamScore(
      subject: json['subject_name'] ?? '',
      component: json['component_name'] ?? '',
      marksObtained: _toDouble(json['marks_obtained']),
      maxMarks: _toDouble(json['max_marks']),
      minMarks: _toDouble(json['min_marks']),
      passed: _toBool(json['passed']),
    );
  }

  static double _toDouble(dynamic value) {
    if (value is num) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0;

    return 0;
  }

  static bool _toBool(dynamic value) {
    if (value is bool) return value;
    if (value is num) return value != 0;
    if (value is String) return value == '1' || value.toLowerCase() == 'true';

    return false;
  }
}
