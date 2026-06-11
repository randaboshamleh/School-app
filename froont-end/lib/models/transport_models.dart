// lib/models/transport_models.dart

class TransportRoute {
  final int id;
  final String name;

  TransportRoute({
    required this.id,
    required this.name,
  });

  factory TransportRoute.fromJson(Map<String, dynamic> json) => TransportRoute(
        id: _toInt(json['id']),
        name: json['name'] ?? '',
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
      };
}

class TransportSubscription {
  final int id;
  final String name;
  final bool isActive;

  TransportSubscription({
    required this.id,
    required this.name,
    required this.isActive,
  });

  factory TransportSubscription.fromJson(Map<String, dynamic> json) =>
      TransportSubscription(
        id: _toInt(json['id']),
        name: json['name'] ?? '',
        isActive: _toBool(json['is_active']),
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'is_active': isActive,
      };
}

int _toInt(dynamic value) {
  if (value is int) return value;
  if (value is num) return value.toInt();
  if (value is String) return int.tryParse(value) ?? 0;

  return 0;
}

bool _toBool(dynamic value) {
  if (value is bool) return value;
  if (value is num) return value != 0;
  if (value is String) return value == '1' || value.toLowerCase() == 'true';

  return false;
}
