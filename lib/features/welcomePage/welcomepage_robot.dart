import 'package:flutter/material.dart';
import 'package:graduationproject/features/welcomePage/welcomeBase.dart';

class WelcomepageRobot extends StatelessWidget {
  const WelcomepageRobot({super.key});

  @override
  Widget build(BuildContext context) {
    return const Welcomebase(
      backgroundColor: Color(0xFF82C5D8),
      imageUrl: 'assets/images/RobotWelcome.png',
      imageWidthFactor: 0.75, // ياخد 75% من عرض الشاشة
      imageHeightFactor: 0.35, // ياخد 35% من طول الشاشة
      button1Color: Color(0xFF82C5D8),
      button1TextColor: Colors.white,
      button2Color: Colors.white,
      button2TextColor: Color(0xFF60ACBF),
      guestTextColor: Colors.white,
      continueAsAColor: Colors.white,
      underlineColor: Colors.white,
      dividerColor: Colors.white,
      orTextColor: Colors.white,
    );
  }
}
