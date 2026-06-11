import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:rand/helpers/locale_direction.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class FinanceScreen extends StatefulWidget {
  static const String screenRoute = 'finance_screen';
  const FinanceScreen({super.key});

  @override
  State<FinanceScreen> createState() => _FinanceScreenState();
}

class _FinanceScreenState extends State<FinanceScreen> {
  bool loading = true;
  Map<String, dynamic>? financeData;

  final _storage = const FlutterSecureStorage(); // · Œ“Ì‰/ﬁ—«¡… «· Êﬂ‰

  double _asDouble(dynamic v) {
    if (v == null) return 0;
    if (v is num) return v.toDouble();
    return double.tryParse(v.toString()) ?? 0;
  }

  int _asInt(dynamic v) {
    if (v == null) return 0;
    if (v is int) return v;
    if (v is num) return v.toInt();
    return int.tryParse(v.toString()) ?? 0;
  }

  Future<void> fetchPayments() async {
    try {
      // «ﬁ—√ «· Êﬂ‰ „‰ «· Œ“Ì‰
      String? authToken = await _storage.read(key: 'token');

      // Õ÷¯— «·ÂÌœ—“
      final headers = {"Accept": "application/json"};
      if (authToken != null && authToken.isNotEmpty) {
        headers["Authorization"] = "Bearer $authToken";
      }

      final response = await http.get(
        Uri.parse("http://192.168.1.10:8000/api/payments"),
        headers: headers,
      );

      debugPrint("?? Response Status: ${response.statusCode}");
      debugPrint("?? Response Body: ${response.body}");

      if (response.statusCode == 200) {
        final data = json.decode(response.body);

        final paymentsData = data["data"];
        List<Map<String, dynamic>> paymentsList;

        if (paymentsData is List) {
          paymentsList = List<Map<String, dynamic>>.from(paymentsData);
        } else if (paymentsData is Map) {
          paymentsList = [Map<String, dynamic>.from(paymentsData)];
        } else {
          paymentsList = [];
        }

        setState(() {
          financeData = {
            "payments": paymentsList,
            "balance": 0, // Õ«·Ì« À«» ° „„ﬂ‰ ·«Õﬁ«  Õ”» „‰ API
          };
          loading = false;
        });
      } else {
        setState(() {
          loading = false;
          financeData = null;
        });
      }
    } catch (e) {
      debugPrint("? Error fetching payments: $e");
      setState(() {
        loading = false;
        financeData = null;
      });
    }
  }

  @override
  void initState() {
    super.initState();
    fetchPayments();
  }

  @override
  Widget build(BuildContext context) {
    const purpleColor = Color(0xFF0F766E);
    const greenColor = Color(0xFF2563EB);

    return Directionality(
      textDirection: appTextDirection(context),
      child: Scaffold(
        appBar: AppBar(
          title: const Text("«·„«·Ì…", textAlign: TextAlign.center),
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
                  : financeData == null
                  ? const Center(
                      child: Text(
                        "·„ Ì „  Õ„Ì· «·»Ì«‰« ",
                        style: TextStyle(color: Colors.white, fontSize: 20),
                      ),
                    )
                  : Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: Directionality(
                        textDirection: appTextDirection(context),
                        child: ListView(
                          children: [
                            // ? ﬂ«—œ «·—’Ìœ «·Õ«·Ì
                            Card(
                              color: Colors.white.withValues(alpha: 0.15),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(15),
                              ),
                              elevation: 0,
                              child: ListTile(
                                leading: const Icon(
                                  Icons.account_balance_wallet,
                                  color: Colors.white,
                                  size: 32,
                                ),
                                title: const Text(
                                  "«·—’Ìœ «·Õ«·Ì",
                                  style: TextStyle(
                                    color: Colors.white,
                                    fontSize: 22,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                subtitle: Text(
                                  "${financeData!["balance"]} ·.”",
                                  style: const TextStyle(
                                    color: Colors.blue,
                                    fontSize: 20,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(height: 20),

                            // ? «·œ›⁄… «·„‰«”»…
                            Builder(
                              builder: (context) {
                                final List<Map<String, dynamic>> payments =
                                    (financeData!["payments"] as List)
                                        .map<Map<String, dynamic>>(
                                          (e) => Map<String, dynamic>.from(
                                            e as Map,
                                          ),
                                        )
                                        .toList();

                                if (payments.isEmpty) {
                                  return const Center(
                                    child: Text(
                                      "·« ÌÊÃœ œ›⁄«  Õ«·Ì«",
                                      style: TextStyle(
                                        color: Colors.white,
                                        fontSize: 20,
                                      ),
                                    ),
                                  );
                                }

                                final Map<String, dynamic> payment = payments
                                    .firstWhere(
                                      (p) => _asDouble(p["final_amount"]) > 0,
                                      orElse: () => payments.first,
                                    );

                                final double finalAmount = _asDouble(
                                  payment["final_amount"],
                                );
                                final double originalAmount = _asDouble(
                                  payment["original_amount"],
                                );
                                final int scholarshipPercentage = _asInt(
                                  payment["scholarship_percentage"],
                                );
                                final String dueDate =
                                    (payment["due_date"] ?? "-").toString();

                                return Column(
                                  children: [
                                    // ? ﬂ«—œ «·„»·€ «·Ê«Ã» œ›⁄Â
                                    Card(
                                      color: Colors.white.withValues(alpha: 0.15),
                                      shape: RoundedRectangleBorder(
                                        borderRadius: BorderRadius.circular(15),
                                      ),
                                      elevation: 0,
                                      child: ListTile(
                                        leading: const Icon(
                                          Icons.payment,
                                          color: Colors.white,
                                          size: 30,
                                        ),
                                        title: const Text(
                                          "«·„»·€ «·Ê«Ã» œ›⁄Â",
                                          style: TextStyle(
                                            color: Colors.white,
                                            fontSize: 20,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        subtitle: Column(
                                          crossAxisAlignment:
                                              CrossAxisAlignment.start,
                                          children: [
                                            const SizedBox(height: 6),
                                            if (scholarshipPercentage > 0) ...[
                                              Text(
                                                "«·„»·€ «·√’·Ì: ${originalAmount.toStringAsFixed(0)} ·.”",
                                                style: const TextStyle(
                                                  color: Colors.white,
                                                  fontSize: 16,
                                                ),
                                              ),
                                              Text(
                                                "‰”»… «·„‰Õ…: $scholarshipPercentage %",
                                                style: const TextStyle(
                                                  color: Colors.white,
                                                  fontSize: 16,
                                                ),
                                              ),
                                              Text(
                                                "«·„»·€ »⁄œ «·„‰Õ…: ${finalAmount.toStringAsFixed(0)} ·.”",
                                                style: const TextStyle(
                                                  color: Colors.blue,
                                                  fontSize: 18,
                                                  fontWeight: FontWeight.bold,
                                                ),
                                              ),
                                            ] else
                                              Text(
                                                "${finalAmount.toStringAsFixed(0)} ·.”",
                                                style: TextStyle(
                                                  color: finalAmount == 0
                                                      ? Colors.blue
                                                      : Colors.red,
                                                  fontSize: 18,
                                                  fontWeight: FontWeight.bold,
                                                ),
                                              ),
                                          ],
                                        ),
                                      ),
                                    ),
                                    const SizedBox(height: 20),

                                    // ? ﬂ«—œ ¬Œ— „Â·… ··œ›⁄
                                    Card(
                                      color: Colors.white.withValues(alpha: 0.15),
                                      shape: RoundedRectangleBorder(
                                        borderRadius: BorderRadius.circular(15),
                                      ),
                                      elevation: 0,
                                      child: ListTile(
                                        leading: const Icon(
                                          Icons.schedule,
                                          color: Colors.white,
                                          size: 30,
                                        ),
                                        title: const Text(
                                          "¬Œ— „Â·… ··œ›⁄",
                                          style: TextStyle(
                                            color: Colors.white,
                                            fontSize: 20,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        subtitle: Text(
                                          dueDate,
                                          style: const TextStyle(
                                            fontSize: 18,
                                            color: Colors.white,
                                          ),
                                        ),
                                      ),
                                    ),
                                  ],
                                );
                              },
                            ),
                          ],
                        ),
                      ),
                    ),
            ],
          ),
        ),
      ),
    );
  }
}
