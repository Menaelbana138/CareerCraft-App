import 'dart:async';
import 'package:flutter/material.dart';
import 'package:graduationproject/features/welcomePage/welcompage_boy.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key}); // أضفت الـ key ليكون الكود Standard أكتر

  @override
  _SplashScreenState createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();

    // ملاحظة: 12 ثانية وقت طويل جداً للـ Splash، غالباً بتكون من 2 لـ 4 ثواني
    Timer(const Duration(seconds: 12), () {
      if (mounted) {
        // للتأكد إن الصفحة لسه موجودة قبل الانتقال
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => const WelcompageBoy()),
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    // الحصول على أبعاد الشاشة
    final double screenWidth = MediaQuery.of(context).size.width;
    final double screenHeight = MediaQuery.of(context).size.height;

    return Scaffold(
      backgroundColor: const Color(0xFF60ACBF),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // جعل عرض الصورة يمثل 40% من عرض الشاشة لضمان التناسق
            Image.asset(
              'assets/images/splash_center.png',
              width: screenWidth * 0.4,
              fit: BoxFit.contain,
            ),

            // في حالة حبيتي ترجعي الـ CircularProgressIndicator مستقبلاً
            // SizedBox(height: screenHeight * 0.03),
            // const CircularProgressIndicator(color: Colors.white),
          ],
        ),
      ),
    );
  }
}
