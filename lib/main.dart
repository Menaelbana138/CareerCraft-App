import 'package:device_preview/device_preview.dart';
import 'package:flutter/material.dart';
import 'package:graduationproject/auth/ForgetPassword/forgetpasswordPage.dart';
import 'package:graduationproject/auth/ResetPassword/ResetPassword.dart';
import 'package:graduationproject/auth/Sign%20up/SignUpPage.dart';
import 'package:graduationproject/auth/Verification/VerificationPage.dart';
import 'package:graduationproject/auth/login/login.dart';
import 'package:graduationproject/features/welcomePage/welcomepage_robot.dart';
import 'package:graduationproject/features/welcomePage/welcompage_boy.dart';
import 'package:graduationproject/splash_screen.dart';

void main() {
  runApp(
    DevicePreview(
      enabled: true,
      builder: (context) => MyApp(),
    ),
  );
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
        useInheritedMediaQuery: true,
        locale: DevicePreview.locale(context),
        builder: DevicePreview.appBuilder,
        debugShowCheckedModeBanner: false,
        title: 'Flutter Demo',
        home: const SplashScreen());
  }
}
