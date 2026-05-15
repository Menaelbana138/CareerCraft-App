import 'package:flutter/material.dart';
import 'package:graduationproject/widget/custom_button.dart';

// ... نفس الـ imports اللي عندك

class ResetSuccessDialog extends StatelessWidget {
  const ResetSuccessDialog({super.key});

  @override
  Widget build(BuildContext context) {
    final double screenWidth = MediaQuery.of(context).size.width;
    final double screenHeight = MediaQuery.of(context).size.height;

    return Dialog(
      backgroundColor: Colors.white,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      child: Padding(
        padding: EdgeInsets.all(screenWidth * 0.05), // Padding مرن 5%
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // الدائرة الزرقاء
            Container(
              padding: EdgeInsets.all(screenWidth * 0.05),
              decoration: const BoxDecoration(
                  color: Color(0xFF60ACBF), shape: BoxShape.circle),
              child: Icon(Icons.check,
                  size: screenWidth * 0.15, // حجم الأيقونة 15% من عرض الشاشة
                  color: Colors.white),
            ),

            SizedBox(height: screenHeight * 0.02),

            Text(
              "Password Changed",
              style: TextStyle(
                  fontSize: screenWidth * 0.055, // حجم الخط مرن
                  fontWeight: FontWeight.bold,
                  color: const Color(0xFF60ACBF)),
            ),

            SizedBox(height: screenHeight * 0.01),

            Text(
              "Password Change Successfully",
              textAlign: TextAlign.center,
              style: TextStyle(
                  fontSize: screenWidth * 0.04, color: Colors.black54),
            ),

            SizedBox(height: screenHeight * 0.04),

            CustomButton(
              text: "Ok",
              onPressed: () {
                Navigator.pop(context);
              },
              color: const Color(0xFF60ACBF),
              textColor: Colors.white,
              borderRadius: 15,
              height: screenHeight * 0.06, // ارتفاع الزرار مرن
            ),
          ],
        ),
      ),
    );
  }
}
