import 'package:flutter_test/flutter_test.dart';
import 'package:rand/models/grade_models.dart';
import 'package:rand/models/notification_models.dart';
import 'package:rand/models/transport_models.dart';

void main() {
  group('ExamScore', () {
    test('parses numeric API payloads', () {
      final score = ExamScore.fromJson({
        'subject_name': 'Math',
        'component_name': 'Final',
        'marks_obtained': 87,
        'max_marks': '100',
        'min_marks': 50.0,
        'passed': true,
      });

      expect(score.subject, 'Math');
      expect(score.component, 'Final');
      expect(score.marksObtained, 87);
      expect(score.maxMarks, 100);
      expect(score.minMarks, 50);
      expect(score.passed, isTrue);
    });

    test('uses safe defaults for missing payload fields', () {
      final score = ExamScore.fromJson({});

      expect(score.subject, isEmpty);
      expect(score.component, isEmpty);
      expect(score.marksObtained, 0);
      expect(score.maxMarks, 0);
      expect(score.minMarks, 0);
      expect(score.passed, isFalse);
    });
  });

  group('NotificationItem', () {
    test('parses id, read state, and created date', () {
      final item = NotificationItem.fromJson({
        'id': '42',
        'title': 'Notice',
        'body': 'Bring documents',
        'is_read': 1,
        'created_at': '2026-06-11T10:30:00.000Z',
      });

      expect(item.id, 42);
      expect(item.title, 'Notice');
      expect(item.body, 'Bring documents');
      expect(item.isRead, isTrue);
      expect(item.createdAt.toUtc().year, 2026);
    });

    test('compares notifications by id', () {
      final first = NotificationItem.fromJson({'id': 7});
      final second = NotificationItem.fromJson({'id': '7', 'title': 'Updated'});

      expect(first, second);
      expect({first, second}.length, 1);
    });
  });

  group('Transport models', () {
    test('parse route and subscription payloads', () {
      final route = TransportRoute.fromJson({'id': '9', 'name': 'North'});
      final subscription = TransportSubscription.fromJson({
        'id': 3,
        'name': 'Morning',
        'is_active': 1,
      });

      expect(route.id, 9);
      expect(route.name, 'North');
      expect(subscription.id, 3);
      expect(subscription.name, 'Morning');
      expect(subscription.isActive, isTrue);
    });
  });
}
