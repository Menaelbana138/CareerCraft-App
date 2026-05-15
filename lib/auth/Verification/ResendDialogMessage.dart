import 'package:flutter/material.dart';
import 'package:graduationproject/widget/custom_button.dart';

class SuccessDialog extends StatelessWidget {
  final String title;
  final String message;

  const SuccessDialog({
    super.key,
    required this.title,
    required this.message,
  });

  @override
  Widget build(BuildContext context) {
    final double screenWidth = MediaQuery.of(context).size.width;
    final double screenHeight = MediaQuery.of(context).size.height;

    return Dialog(
      backgroundColor: Colors.white,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      child: Padding(
        padding: EdgeInsets.all(screenWidth * 0.06), // padding مرن
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              title,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: screenWidth * 0.055, // حجم العنوان مرن
                color: const Color(0xFF60ACBF),
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: screenHeight * 0.02),
            Text(
              message,
              textAlign: TextAlign.center,
              style: TextStyle(
                  fontSize: screenWidth * 0.04, color: Colors.black87),
            ),
            SizedBox(height: screenHeight * 0.03),
            CustomButton(
              text: "Ok",
              onPressed: () => Navigator.pop(context),
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
