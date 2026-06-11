import 'package:flutter/material.dart';
import 'package:rand/helpers/locale_direction.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:flutter/gestures.dart';

class ContactUsScreen extends StatelessWidget {
  static const String screenRoute = 'contact_us_screen';

  const ContactUsScreen({super.key});

  // œ«·… ·› Õ «·—Ê«»ÿ
  Future<void> _launchUrl(String url) async {
    final uri = Uri.parse(url);
    if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
      throw '·« Ì„ﬂ‰ › Õ «·—«»ÿ: $url';
    }
  }

  // œ«·… ··« ’«· »—ﬁ„
  Future<void> _makeCall(String phoneNumber) async {
    final uri = Uri.parse("tel:$phoneNumber");
    if (!await launchUrl(uri)) {
      throw '·« Ì„ﬂ‰ «·« ’«· »«·—ﬁ„: $phoneNumber';
    }
  }

  @override
  Widget build(BuildContext context) {
    const purpleColor = Color(0xFF0F766E);
    const greenColor = Color(0xFF2563EB);

    return Scaffold(
      appBar: AppBar(
        title: const Text(" Ê«’· „⁄‰«", style: TextStyle(color: Colors.black)),
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
            // «·Œ·›Ì… «·„ œ—Ã…
            Container(
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [purpleColor, greenColor],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
              ),
            ),
            // «··ÊÃÊ »«·Œ·›Ì…
            Positioned.fill(
              child: Opacity(
                opacity: 0.05,
                child: const Icon( Icons.school_outlined, size: 220, color: Colors.white ),
              ),
            ),
            // «·„Õ ÊÏ «·√”«”Ì
            ListView(
              padding: const EdgeInsets.all(16),
              children: [
                const Text(
                  "„Ê«ﬁ⁄ «· Ê«’·",
                  style: TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: Colors.black,
                  ),
                  textAlign: TextAlign.right,
                ),
                const SizedBox(height: 10),

                // ?? ›Ì”»Êﬂ
                GestureDetector(
                  onTap: () =>
                      _launchUrl("https://www.facebook.com/share/16qFcT71ch/"),
                  child: Container(
                    padding: const EdgeInsets.symmetric(vertical: 8),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.start,
                      textDirection: appTextDirection(context),
                      children: [
                        const Icon(
                          Icons.facebook,
                          color: Colors.blue,
                          size: 28,
                        ),
                        const SizedBox(width: 10),
                        RichText(
                          text: TextSpan(
                            text: "Õ”«» «·„œ—”… ⁄·Ï ›Ì”»Êﬂ\n",
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 18,
                            ),
                            children: [
                              TextSpan(
                                text: "«÷€ÿ Â‰«",
                                style: const TextStyle(
                                  color: Colors.lightBlueAccent,
                                  decoration: TextDecoration.underline,
                                  fontSize: 18,
                                ),
                                recognizer: TapGestureRecognizer()
                                  ..onTap = () {
                                    _launchUrl(
                                      "https://www.facebook.com/share/16qFcT71ch/",
                                    );
                                  },
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),

                // ?? «‰” €—«„
                GestureDetector(
                  onTap: () => _launchUrl(
                    "https://www.instagram.com/al_mukhtar_schools?igsh=MTVuZGxobG9zZTM2bA==",
                  ),
                  child: Container(
                    padding: const EdgeInsets.symmetric(vertical: 8),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.start,
                      textDirection: appTextDirection(context),
                      children: [
                        const Icon(
                          Icons.camera_alt,
                          color: Colors.teal,
                          size: 28,
                        ),
                        const SizedBox(width: 10),
                        RichText(
                          text: TextSpan(
                            text: "Õ”«» «·„œ—”… ⁄·Ï «‰” €—«„\n",
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 18,
                            ),
                            children: [
                              TextSpan(
                                text: "«÷€ÿ Â‰«",
                                style: const TextStyle(
                                  color: Colors.lightBlueAccent,
                                  decoration: TextDecoration.underline,
                                  fontSize: 18,
                                ),
                                recognizer: TapGestureRecognizer()
                                  ..onTap = () {
                                    _launchUrl(
                                      "https://www.instagram.com/al_mukhtar_schools?igsh=MTVuZGxobG9zZTM2bA==",
                                    );
                                  },
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),

                const SizedBox(height: 20),
                const Text(
                  "√—ﬁ«„ «·≈œ«—…",
                  style: TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: Colors.black,
                  ),
                  textAlign: TextAlign.right,
                ),
                const SizedBox(height: 10),

                // √—ﬁ«„ «·ÂÊ« ›
                ListTile(
                  trailing: const Icon(Icons.phone, color: Colors.blue),
                  title: const Text(
                    "«·≈œ«—… - 1",
                    style: TextStyle(color: Colors.white),
                  ),
                  subtitle: const Text(
                    "0933328886",
                    style: TextStyle(color: Colors.white70),
                  ),
                  onTap: () => _makeCall("0933328886"),
                ),
                ListTile(
                  trailing: const Icon(Icons.phone, color: Colors.blue),
                  title: const Text(
                    "«·≈œ«—… - 2",
                    style: TextStyle(color: Colors.white),
                  ),
                  subtitle: const Text(
                    "0992514002",
                    style: TextStyle(color: Colors.white70),
                  ),
                  onTap: () => _makeCall("0992514002"),
                ),
                ListTile(
                  trailing: const Icon(Icons.phone, color: Colors.blue),
                  title: const Text(
                    "«·≈œ«—… - 3",
                    style: TextStyle(color: Colors.white),
                  ),
                  subtitle: const Text(
                    "0944271405",
                    style: TextStyle(color: Colors.white70),
                  ),
                  onTap: () => _makeCall("0944271405"),
                ),
                ListTile(
                  trailing: const Icon(Icons.phone, color: Colors.blue),
                  title: const Text(
                    "«·≈œ«—… - 4",
                    style: TextStyle(color: Colors.white),
                  ),
                  subtitle: const Text(
                    "0960011133",
                    style: TextStyle(color: Colors.white70),
                  ),
                  onTap: () => _makeCall("0960011133"),
                ),
                ListTile(
                  trailing: const Icon(Icons.phone, color: Colors.blue),
                  title: const Text(
                    "«·≈œ«—… - 5",
                    style: TextStyle(color: Colors.white),
                  ),
                  subtitle: const Text(
                    "0935548382",
                    style: TextStyle(color: Colors.white70),
                  ),
                  onTap: () => _makeCall("0935548382"),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
