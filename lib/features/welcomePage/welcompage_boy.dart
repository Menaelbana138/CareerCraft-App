import 'package:flutter/material.dart';
import 'package:graduationproject/features/welcomePage/welcomeBase.dart';

class WelcompageBoy extends StatelessWidget {
  const WelcompageBoy({super.key});

  @override
  Widget build(BuildContext context) {
    return Welcomebase(
      backgroundColor: Color(0xFFFAFCFC),
      imageUrl: 'assets/images/BoyWelcomePage.png',
      button1Color: Colors.white,
      button1TextColor: Color(0xFF60ACBF),
      button2Color: Color(0xFF60ACBF),
      button2TextColor: Colors.white,
      guestTextColor: Color(0xFF60ACBF),
      dividerColor: const Color.fromARGB(255, 73, 72, 72)!,
      orTextColor: const Color.fromARGB(255, 73, 72, 72)!,
      continueAsAColor: const Color.fromARGB(255, 73, 72, 72)!,
      underlineColor: const Color.fromARGB(255, 73, 72, 72)!,
    );
  }
}
// import 'package:flutter/material.dart';
// import 'package:graduationproject/widget/custom_button.dart';

// class WelcompageBoy extends StatelessWidget {
//   const WelcompageBoy({super.key});

//   @override
//   Widget build(BuildContext context) {
//     return Scaffold(
//         backgroundColor: Color(0xFFFAFCFC), // لون الخلفية الأزرق
//         body: Column(
//             //   mainAxisAlignment: MainAxisAlignment.spaceAround,
//             crossAxisAlignment: CrossAxisAlignment.center,
//             children: [
//               SizedBox(height: 120),
//               Image.asset('assets/images/BoyWelcomePage.png',
//                   width: 220, height: 250, fit: BoxFit.cover),
//               SizedBox(height: 50),
//               Padding(
//                 padding: const EdgeInsets.symmetric(horizontal: 25.0),
//                 child: Center(
//                     child: CustomButton(
//                   text: "Login", onPressed: () {},
//                   color: Colors.white,
//                   textColor: Color(0xFF60ACBF),
//                   borderColor: Color(0xFF60ACBF),
//                   // isFullWidth: false,
//                   borderRadius: 16,
//                   // بدل 300 ثابت
//                   fontWeight: FontWeight.normal,

//                   /*Navigator.push(
//                   context,
//                   MaterialPageRoute(builder: (context) => Loginpage()),*/
//                 )),
//               ),
//               SizedBox(height: 25),
//               Padding(
//                 padding: const EdgeInsets.symmetric(horizontal: 25.0),
//                 child: Center(
//                   child: CustomButton(
//                     text: "Sign up", onPressed: () {},
//                     color: Color(0xFF60ACBF),
//                     textColor: Colors.white,
//                     borderColor: Color(0xFF60ACBF),
//                     // isFullWidth: false,
//                     borderRadius: 16,
//                     // بدل 300 ثابت
//                     fontWeight: FontWeight.normal,

//                     /*Navigator.push(
//                   context,
//                   MaterialPageRoute(builder: (context) => Loginpage()),*/
//                   ),
//                 ),
//               ),
//               const SizedBox(height: 30),

//               // 4. خط الـ "or"
//               Padding(
//                 padding: const EdgeInsets.symmetric(horizontal: 20),
//                 child: Row(
//                   children: [
//                     Expanded(
//                         child: Divider(thickness: 1, color: Colors.grey[400])),
//                     Padding(
//                       padding: const EdgeInsets.symmetric(horizontal: 10),
//                       child: Text("or",
//                           style:
//                               TextStyle(color: Colors.grey[600], fontSize: 16)),
//                     ),
//                     Expanded(
//                         child: Divider(thickness: 1, color: Colors.grey[400])),
//                   ],
//                 ),
//               ),
//               const SizedBox(height: 30),

//               // 5. نص الدخول كزائر
//               Row(
//                 mainAxisAlignment: MainAxisAlignment.center,
//                 children: [
//                   Text(
//                     "Continue As A ",
//                     style: TextStyle(color: Colors.grey[700], fontSize: 16),
//                   ),
//                   GestureDetector(
//                     onTap: () {},
//                     child: const Text(
//                       "Guest",
//                       style: TextStyle(
//                           color: Color(0xFF60ACBF),
//                           fontSize: 16,
//                           fontWeight: FontWeight.bold,
//                           decoration: TextDecoration.underline,
//                           decorationColor: Colors.grey),
//                     ),
//                   ),
//                 ],
//               ),
//               const Spacer(flex: 1),
//             ]));
//   }
// }
