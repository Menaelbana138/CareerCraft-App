import 'package:flutter/material.dart';
import 'package:graduationproject/auth/Sign%20up/SignUpPage.dart';
import 'package:graduationproject/auth/login/login.dart';
import 'package:graduationproject/widget/custom_button.dart';

class Welcomebase extends StatelessWidget {
  final Color backgroundColor;
  final String imageUrl;
  final double imageWidthFactor; // نستخدم معامل نسبة بدل رقم ثابت
  final double imageHeightFactor;

  final Color button1Color;
  final Color button1TextColor;
  final double button1BorderThickness;
  final Color button2Color;
  final Color button2TextColor;

  final Color dividerColor;
  final Color orTextColor;

  final Color continueAsAColor;
  final Color guestTextColor;
  final Color underlineColor;

  const Welcomebase({
    super.key,
    required this.backgroundColor,
    required this.imageUrl,
    this.imageWidthFactor = 0.6, // 60% من عرض الشاشة افتراضياً
    this.imageHeightFactor = 0.3, // 30% من طول الشاشة افتراضياً
    required this.button1Color,
    required this.button1TextColor,
    this.button1BorderThickness = 2.0,
    required this.button2Color,
    required this.button2TextColor,
    required this.dividerColor,
    required this.orTextColor,
    required this.continueAsAColor,
    required this.guestTextColor,
    required this.underlineColor,
  });

  @override
  Widget build(BuildContext context) {
    final double screenHeight = MediaQuery.of(context).size.height;
    final double screenWidth = MediaQuery.of(context).size.width;

    return Scaffold(
      backgroundColor: backgroundColor,
      body: SafeArea(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // مسافة علوية مرنة
            SizedBox(height: screenHeight * 0.1),

            Image.asset(
              imageUrl,
              width: screenWidth * imageWidthFactor,
              height: screenHeight * imageHeightFactor,
              fit: BoxFit.contain,
            ),

            // مسافة مرنة بين الصورة والأزرار
            SizedBox(height: screenHeight * 0.05),

            Padding(
              padding: EdgeInsets.symmetric(horizontal: screenWidth * 0.07),
              child: CustomButton(
                text: "Login",
                onPressed: () {
                  Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) => const LoginScreen()));
                },
                color: button1Color,
                textColor: button1TextColor,
                borderColor: button1TextColor,
                borderWidth: button1BorderThickness,
                borderRadius: 16,
                height: screenHeight * 0.065, // ارتفاع متجاوب للزر
              ),
            ),

            SizedBox(height: screenHeight * 0.025),

            Padding(
              padding: EdgeInsets.symmetric(horizontal: screenWidth * 0.07),
              child: CustomButton(
                text: "Sign up",
                onPressed: () {
                  Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) => const SignUpScreen()));
                },
                color: button2Color,
                textColor: button2TextColor,
                borderColor: button2Color,
                borderRadius: 16,
                height: screenHeight * 0.065, // ارتفاع متجاوب للزر
              ),
            ),

            SizedBox(height: screenHeight * 0.03),

            // الـ Divider (or)
            Padding(
              padding: EdgeInsets.symmetric(horizontal: screenWidth * 0.08),
              child: Row(
                children: [
                  Expanded(child: Divider(thickness: 1, color: dividerColor)),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 10),
                    child: Text("or",
                        style: TextStyle(
                            color: orTextColor, fontSize: screenWidth * 0.04)),
                  ),
                  Expanded(child: Divider(thickness: 1, color: dividerColor)),
                ],
              ),
            ),

            SizedBox(height: screenHeight * 0.03),

            // نص الـ Guest
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  "Continue As A ",
                  style: TextStyle(
                      color: continueAsAColor, fontSize: screenWidth * 0.04),
                ),
                GestureDetector(
                  onTap: () {},
                  child: Text(
                    "Guest",
                    style: TextStyle(
                        color: guestTextColor,
                        fontSize: screenWidth * 0.04,
                        decoration: TextDecoration.underline,
                        decorationColor: underlineColor),
                  ),
                ),
              ],
            ),

            // Spacer عشان نضمن إن العناصر مكدوسة فوق بعضها لو الشاشة كبيرة
            const Spacer(),
          ],
        ),
      ),
    );
  }
}
