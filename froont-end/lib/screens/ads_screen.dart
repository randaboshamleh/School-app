import 'package:flutter/material.dart';
import 'package:rand/helpers/locale_direction.dart';
import 'package:dio/dio.dart';

class AdsScreen extends StatefulWidget {
  const AdsScreen({super.key});
  static const String screenRoute = '/ads';

  @override
  State<AdsScreen> createState() => _AdsScreenState();
}

class _AdsScreenState extends State<AdsScreen> {
  final Dio dio = Dio();
  List<dynamic> ads = [];
  bool isLoading = true;

  Future<void> fetchAds() async {
    try {
      final response = await dio.get("http://192.168.1.10:8000/api/ads");

      debugPrint("Response Data: ${response.data}");

      if (response.statusCode == 200) {
        setState(() {
          ads = response.data;
          isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("Error fetching ads: $e");
      setState(() {
        isLoading = false;
      });
    }
  }

  @override
  void initState() {
    super.initState();
    fetchAds();
  }

  @override
  Widget build(BuildContext context) {
    const purpleColor = Color(0xFF0F766E);
    const greenColor = Color(0xFF2563EB);
    return Scaffold(
      appBar: AppBar(
        title: const Text("«·≈⁄·«‰« ", textAlign: TextAlign.center),
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
              ? const Center(child: CircularProgressIndicator())
              : ads.isEmpty
              ? const Center(
                  child: Text(
                    "·« ÌÊÃœ ≈⁄·«‰«  Õ«·Ì«",
                    style: TextStyle(color: Colors.white, fontSize: 18),
                  ),
                )
              : ListView.builder(
                  padding: const EdgeInsets.fromLTRB(16, 10, 16, 16),
                  itemCount: ads.length,
                  itemBuilder: (context, index) {
                    final ad = ads[index];
                    return Card(
                      color: Colors.white.withValues(alpha: 0.3),
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      margin: const EdgeInsets.symmetric(vertical: 12),
                      child: Container(
                        padding: const EdgeInsets.fromLTRB(12, 20, 12, 16),
                        child: Directionality(
                          textDirection: appTextDirection(context),
                          child: Align(
                            alignment: Alignment.centerRight,
                            child: Text(
                              ad["content"] ?? "",
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 18,
                              ),
                            ),
                          ),
                        ),
                      ),
                    );
                  },
                ),
        ],
      ),
    );
  }
}
