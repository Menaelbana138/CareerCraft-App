import 'package:flutter/material.dart';
import 'package:graduationproject/auth/login/login.dart';
import 'package:graduationproject/widget/custom_button.dart';

class SignUpSuccessDialog extends StatelessWidget {
  final String imagePath;

  const SignUpSuccessDialog({super.key, required this.imagePath});

  @override
  Widget build(BuildContext context) {
    final double screenWidth = MediaQuery.of(context).size.width;
    final double screenHeight = MediaQuery.of(context).size.height;

    return Dialog(
      backgroundColor: const Color(0xFF60ACBF),
      shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(screenWidth * 0.08)),
      // الـ child محمي بـ SingleChildScrollView احتياطاً لو الموبايل صغير جداً
      child: SingleChildScrollView(
        child: Padding(
          padding: EdgeInsets.symmetric(
              vertical: screenHeight * 0.04, horizontal: screenWidth * 0.05),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Image.asset(
                imagePath,
                height: screenHeight * 0.15, // طول الصورة 15% من الشاشة
                fit: BoxFit.contain,
              ),
              SizedBox(height: screenHeight * 0.03),
              Text(
                "Sign in to continue",
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: screenWidth * 0.06, // خط متجاوب
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
              SizedBox(height: screenHeight * 0.02),
              Text(
                "Sign in to save your progress and\nunlock all features.",
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: screenWidth * 0.04,
                  color: Colors.white,
                  height: 1.4,
                ),
              ),
              SizedBox(height: screenHeight * 0.04),
              CustomButton(
                text: "Sign in",
                onPressed: () {
                  Navigator.pushAndRemoveUntil(
                    context,
                    MaterialPageRoute(
                        builder: (context) => const LoginScreen()),
                    (route) => false, // يفضل في النجاح نقفل كل اللي فات
                  );
                },
                color: Colors.white,
                textColor: const Color(0xFF60ACBF),
                borderRadius: 25,
                height: screenHeight * 0.06,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
