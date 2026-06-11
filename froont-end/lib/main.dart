import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'services/notification_service.dart';

// Firebase
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'firebase_options.dart';

// ≈‘⁄«—«  „Õ·Ì…
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

// Easy Localization
import 'package:easy_localization/easy_localization.dart';

// «·‘«‘« 
import 'package:rand/screens/register_screen.dart';
import 'package:rand/screens/forgot_password_screen.dart';
import 'package:rand/screens/login_screen.dart';
import 'package:rand/screens/ads_screen.dart';
import 'package:rand/screens/home_screen.dart';
import 'package:rand/screens/transport_screen.dart';
import 'package:rand/screens/file_screen.dart';
import 'package:rand/screens/finance_screen.dart';
import 'package:rand/screens/exams_screen.dart';
import 'package:rand/screens/contact_us_screen.dart';
import 'package:rand/screens/notification_screen.dart';
import 'package:rand/helpers/locale_direction.dart';

final FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin =
    FlutterLocalNotificationsPlugin();
final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

/// ?? œ«·… „⁄«·Ã… ≈‘⁄«—«  «·Œ·›Ì…
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  if (Firebase.apps.isEmpty) {
    await Firebase.initializeApp(
      options: DefaultFirebaseOptions.currentPlatform,
    );
  }
  debugPrint('? Handling background message: ${message.messageId}');
  await NotificationService.handleMessage(message);
}

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await EasyLocalization.ensureInitialized(); // ?  ÂÌ∆… easy_localization

  runApp(
    EasyLocalization(
      supportedLocales: const [Locale('ar'), Locale('en')],
      path: 'assets/lang',
      fallbackLocale: const Locale('ar'),
      startLocale: const Locale('ar'),
      child: const MyApp(),
    ),
  );
}

class MyApp extends StatefulWidget {
  const MyApp({super.key});

  @override
  State<MyApp> createState() => _MyAppState();
}

class _MyAppState extends State<MyApp> {
  Widget _defaultHome = const CircularProgressIndicator();

  @override
  void initState() {
    super.initState();
    _loadInitialScreen();
    _initializeServices();
  }

  Future<void> _loadInitialScreen() async {
    final storage = const FlutterSecureStorage();
    final userToken = await storage.read(key: 'token');
    final userId = await storage.read(key: 'studentId');

    setState(() {
      _defaultHome = (userToken != null && userId != null)
          ? const HomeScreen()
          : const LoginScreen();
    });
  }

  Future<void> _initializeServices() async {
    try {
      await Firebase.initializeApp(
        options: DefaultFirebaseOptions.currentPlatform,
      );
      await NotificationService.initLocalNotifications();
      await NotificationService.initFCM();
      FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
    } catch (e) {
      debugPrint("? Œÿ√ √À‰«¡ «· ÂÌ∆…: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      navigatorKey: navigatorKey,
      debugShowCheckedModeBanner: false,
      title: 'School Portal',
      theme: ThemeData(primarySwatch: Colors.teal),
      locale: context.locale,
      supportedLocales: context.supportedLocales,
      localizationsDelegates: context.localizationDelegates,
      builder: (context, child) {
        return Directionality(
          textDirection: appTextDirection(context),
          child: child ?? const SizedBox.shrink(),
        );
      },
      home: Scaffold(body: Center(child: _defaultHome)),
      routes: {
        HomeScreen.screenRoute: (context) => const HomeScreen(),
        ForgotPasswordScreen.screenRoute: (context) =>
            const ForgotPasswordScreen(),
        TransportScreen.screenRoute: (context) => const TransportScreen(),
        AdsScreen.screenRoute: (context) => const AdsScreen(),
        MyFilesScreen.screenRoute: (context) => const MyFilesScreen(),
        FinanceScreen.screenRoute: (context) => const FinanceScreen(),
        ExamsScreen.screenRoute: (context) => const ExamsScreen(),
        ContactUsScreen.screenRoute: (context) => const ContactUsScreen(),
        NotificationsScreen.screenRoute: (context) =>
            const NotificationsScreen(),
        LoginScreen.screenRoute: (context) => LoginScreen(
          onLoginSuccess: (String token) async {
            final storage = const FlutterSecureStorage();
            await storage.write(key: 'token', value: token);
            if (!context.mounted) return;
            Navigator.pushReplacementNamed(context, HomeScreen.screenRoute);
          },
        ),
        RegisterScreen.screenRoute: (context) => RegisterScreen(
          onRegisterSuccess: (String token) async {
            final storage = const FlutterSecureStorage();
            await storage.write(key: 'token', value: token);
            await storage.write(key: 'isRegistered', value: 'true');
            if (!context.mounted) return;
            Navigator.pushReplacementNamed(context, HomeScreen.screenRoute);
          },
        ),
      },
    );
  }
}
