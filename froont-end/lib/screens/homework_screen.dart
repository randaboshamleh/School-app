import 'package:flutter/material.dart';
import 'package:rand/helpers/locale_direction.dart';
import 'package:dio/dio.dart';

class HomeworkScreen extends StatefulWidget {
  const HomeworkScreen({super.key});

  @override
  State<HomeworkScreen> createState() => _HomeworkScreenState();
}

class _HomeworkScreenState extends State<HomeworkScreen> {
  final Dio dio = Dio();
  List<dynamic> homeworkList = [];
  bool isLoading = true;

  Future<void> fetchHomework() async {
    try {
      final response = await dio.get("http://10.0.2.2:8000/api/homework"); 
      if (response.statusCode == 200) {
        setState(() {
          homeworkList = response.data;
          isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("? Error fetching homework: $e");
      setState(() => isLoading = false);
    }
  }

  Future<void> deleteHomework(int id) async {
    try {
      await dio.delete("http://10.0.2.2:8000/api/homework/$id");
      fetchHomework(); // ≈⁄«œ…  Õ„Ì· »⁄œ «·Õ–›
    } catch (e) {
      debugPrint("? Error deleting homework: $e");
    }
  }

  @override
  void initState() {
    super.initState();
    fetchHomework();
  }

  @override
  Widget build(BuildContext context) {
    const purpleColor = Color(0xFF0F766E);

    return Scaffold(
      appBar: AppBar(
        title: const Text("«·Ê«Ã»« ", textAlign: TextAlign.center),
        centerTitle: true,
        backgroundColor: purpleColor,
      ),
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [purpleColor, Colors.blue],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: isLoading
            ? const Center(child: CircularProgressIndicator())
            : homeworkList.isEmpty
                ? const Center(
                    child: Text(
                      "·« ÌÊÃœ Ê«Ã»«  Õ«·Ì«",
                      style: TextStyle(color: Colors.white, fontSize: 18),
                    ),
                  )
                : ListView.builder(
                    padding: const EdgeInsets.all(12),
                    itemCount: homeworkList.length,
                    itemBuilder: (context, index) {
                      final hw = homeworkList[index];
                      return Card(
                        color: Colors.white.withValues(alpha: 0.2),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        margin: const EdgeInsets.symmetric(vertical: 8),
                        child: Directionality(
                          textDirection: appTextDirection(context),
                          child: ListTile(
                            contentPadding: const EdgeInsets.symmetric(
                                horizontal: 12, vertical: 8),
                            leading: Icon(
                              hw["done"] == true
                                  ? Icons.check_circle
                                  : Icons.radio_button_unchecked,
                              color: hw["done"] == true
                                  ? Colors.blue
                                  : Colors.white70,
                            ),
                            title: Text(
                              hw["subject"] ?? "€Ì— „Õœœ",
                              textAlign: TextAlign.right,
                              style: const TextStyle(
                                fontWeight: FontWeight.bold,
                                color: Colors.white,
                              ),
                            ),
                            subtitle: Text(
                              "${hw["task"] ?? ""}\n «—ÌŒ «· ”·Ì„: ${hw["dueDate"] ?? "-"}",
                              textAlign: TextAlign.right,
                              style: const TextStyle(color: Colors.white70),
                            ),
                            isThreeLine: true,
                            trailing: IconButton(
                              icon: const Icon(Icons.delete, color: Colors.red),
                              onPressed: () => deleteHomework(hw["id"]),
                            ),
                          ),
                        ),
                      );
                    },
                  ),
      ),
      floatingActionButton: FloatingActionButton(
        backgroundColor: purpleColor,
        child: const Icon(Icons.add),
        onPressed: () {
          // ?? Â‰« „„ﬂ‰  › Õ Dialog ·≈÷«›… Ê«Ã» ÃœÌœ
        },
      ),
    );
  }
}
