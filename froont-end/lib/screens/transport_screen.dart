import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:rand/helpers/locale_direction.dart';

class TransportScreen extends StatefulWidget {
  static const String screenRoute = 'transport_screen';

  const TransportScreen({super.key});

  @override
  TransportScreenState createState() => TransportScreenState();
}

class TransportScreenState extends State<TransportScreen> {
  int? _selectedRouteId;
  bool _loading = false;

  final List<Map<String, String>> routes = [
    {"name": "«· Ã«—…", "code": "R001"},
    {"name": "«·⁄»«”ÌÌ‰", "code": "R002"},
    {"name": "«·»—«„ﬂ…", "code": "R003"},
    {"name": "«·’‰«⁄…", "code": "R004"},
    {"name": "«·“«Â—…", "code": "R005"},
    {"name": "«·„Ìœ«‰", "code": "R006"},
    {"name": "œÊÌ·⁄…", "code": "R007"},
    {"name": "’Õ‰«Ì«", "code": "R008"},
    {"name": "Ã—„«‰«", "code": "R009"},
    {"name": "„·ÌÕ…", "code": "R010"},
    {"name": "€“·«‰Ì…", "code": "R011"},
    {"name": "«·Õ”Ì‰Ì…", "code": "R012"},
    {"name": "«·ÂÌÃ«‰…", "code": "R013"},
    {"name": "„“… « ” —«œ", "code": "R014"},
    {"name": "ﬂ›— ”Ê”…", "code": "R015"},
    {"name": "‘«—⁄ »€œ«œ", "code": "R016"},
    {"name": "»«» «·Ã«»Ì…", "code": "R017"},
    {"name": "‰Â— ⁄Ì‘…", "code": "R018"},
    {"name": "œÌ— «·⁄’«›Ì—", "code": "R019"},
    {"name": "«·ﬁ“«“", "code": "R020"},
    {"name": "Ì·œ«", "code": "R021"},
    {"name": "»»Ì·«", "code": "R022"},
    {"name": "»Ì  ”Õ„", "code": "R023"},
    {"name": "ﬁ—Õ «", "code": "R024"},
    {"name": "Õ Ì … «· —ﬂ„«‰", "code": "R025"},
    {"name": "«·”Ìœ… “Ì‰»", "code": "R026"},
    {"name": "Õ„Ê—Ì«", "code": "R027"},
    {"name": "⁄Ì‰  —„«", "code": "R028"},
  ];

  Future<void> subscribeToRoute({required int routeId}) async {
    await Future.delayed(const Duration(seconds: 1)); // „Õ«ﬂ«…  √ŒÌ—
  }

  Future<void> cancelSubscription({required int routeId}) async {
    await Future.delayed(const Duration(seconds: 1)); // „Õ«ﬂ«…  √ŒÌ—
  }

  /// ? œ«·… „ÊÕœ… ·≈ŸÂ«— «·—”«∆·
  void _showMessage(String message, {bool success = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Directionality(
          textDirection: appTextDirection(context),
          child: Text(
            message,
            style: const TextStyle(fontSize: 16, color: Colors.white),
          ),
        ),
        backgroundColor: success ? Colors.blue : Colors.red,
        behavior: SnackBarBehavior.floating,
        duration: const Duration(seconds: 3),
      ),
    );
  }

  void _handleSubscribe() async {
    if (_selectedRouteId == null) {
      _showMessage("«Œ — Œÿ« √Ê·«"); // ? Œÿ√
      return;
    }
    setState(() => _loading = true);
    try {
      await subscribeToRoute(routeId: _selectedRouteId!);
      _showMessage(" „ «·«‘ —«ﬂ »‰Ã«Õ ?", success: true); // ? ‰Ã«Õ
    } catch (e) {
      _showMessage(e.toString()); // ? Œÿ√
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _handleCancel() async {
    if (_selectedRouteId == null) {
      _showMessage("«Œ — Œÿ« ·≈·€«¡ «·«‘ —«ﬂ"); // ? Œÿ√
      return;
    }
    setState(() => _loading = true);
    try {
      await cancelSubscription(routeId: _selectedRouteId!);
      _showMessage(" „ ≈·€«¡ «·«‘ —«ﬂ »‰Ã«Õ ?", success: true); // ? ‰Ã«Õ
    } catch (e) {
      _showMessage(e.toString()); // ? Œÿ√
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    const purpleColor = Color(0xFF0F766E);
    const greenColor = Color(0xFF2563EB);

    return Scaffold(
      appBar: AppBar(
        title: const Text("«·‰ﬁ·", textAlign: TextAlign.center),
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
          // ·ÊÃÊ Œ«›  »«·Œ·›Ì…
          Positioned.fill(
            child: Opacity(
              opacity: 0.05,
              child: const Icon( Icons.school_outlined, size: 220, color: Colors.white ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: [
                    ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.blue,
                        foregroundColor: Colors.white,
                      ),
                      onPressed: _loading ? null : _handleSubscribe,
                      icon: _loading
                          ? const CircularProgressIndicator(
                              color: Colors.white,
                              strokeWidth: 2,
                            )
                          : const Icon(Icons.check),
                      label: const Text("«‘ —«ﬂ"),
                    ),
                    ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.red,
                        foregroundColor: Colors.white,
                      ),
                      onPressed: _loading ? null : _handleCancel,
                      icon: _loading
                          ? const CircularProgressIndicator(
                              color: Colors.white,
                              strokeWidth: 2,
                            )
                          : const Icon(Icons.close),
                      label: const Text("≈·€«¡ «·«‘ —«ﬂ"),
                    ),
                  ],
                ),
                const SizedBox(height: 20),

                // »ÿ«ﬁ«  «·ŒÿÊÿ
                Expanded(
                  child: GridView.builder(
                    itemCount: routes.length,
                    gridDelegate:
                        const SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 2,
                          crossAxisSpacing: 16,
                          mainAxisSpacing: 16,
                        ),
                    itemBuilder: (context, index) {
                      return _buildGlassCard(
                        routes[index]["name"]!,
                        routes[index]["code"]!,
                        index,
                      );
                    },
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildGlassCard(String title, String code, int index) {
    bool isSelected = _selectedRouteId == index;

    return GestureDetector(
      onTap: () {
        setState(() {
          _selectedRouteId = isSelected ? null : index;
        });
      },
      child: ClipRRect(
        borderRadius: BorderRadius.circular(16),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 15, sigmaY: 15),
          child: Container(
            decoration: BoxDecoration(
              color: isSelected
                  ? Colors.white.withValues(alpha: 0.3)
                  : Colors.white.withValues(alpha: 0.15),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.white.withValues(alpha: 0.2)),
            ),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                  ),
                ),
                const SizedBox(height: 5),
                Text(
                  code,
                  style: const TextStyle(fontSize: 18, color: Colors.white),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
