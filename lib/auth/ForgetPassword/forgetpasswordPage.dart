import 'package:flutter/material.dart';
import 'package:graduationproject/auth/Customs/AuthHeader.dart';
import 'package:graduationproject/auth/Customs/CustomAuthAppBar.dart';
import 'package:graduationproject/auth/Verification/VerificationPage.dart';
import 'package:graduationproject/widget/Validator.dart';
import 'package:graduationproject/widget/custom_button.dart';

class ForgetPasswordScreen extends StatefulWidget {
  const ForgetPasswordScreen({super.key});

  @override
  State<ForgetPasswordScreen> createState() => _ForgetPasswordScreenState();
}

class _ForgetPasswordScreenState extends State<ForgetPasswordScreen> {
  final TextEditingController emailController = TextEditingController();
  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  Future<void> handleSendRequest() async {
    if (_formKey.currentState!.validate()) {
      print("Requesting OTP for: ${emailController.text}");

      Navigator.push(
        context,
        MaterialPageRoute(builder: (context) => const VerificationScreen()),
      );
    }
  }

  @override
  void dispose() {
    emailController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // تعريف الأبعاد بناءً على حجم الشاشة
    final double screenWidth = MediaQuery.of(context).size.width;
    final double screenHeight = MediaQuery.of(context).size.height;

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: const CustomAuthAppBar(title: "Forget Password"),
      body: SafeArea(
        child: SingleChildScrollView(
          physics: const BouncingScrollPhysics(),
          child: Padding(
            padding: EdgeInsets.symmetric(
                horizontal: screenWidth * 0.07), // 7% من العرض
            child: Form(
              key: _formKey,
              child: Column(
                children: [
                  const AuthHeader(imagePath: 'assets/images/authImage.png'),

                  // مسافة مرنة بدل 60
                  SizedBox(height: screenHeight * 0.05),

                  Text(
                    "Enter your email and we will send you a verification code, check your mails",
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize:
                          screenWidth * 0.04, // حجم خط متجاوب (تقريباً 16)
                      color: const Color(0xFF62686A),
                      height: 1.5,
                    ),
                  ),

                  // مسافة مرنة بدل 40
                  SizedBox(height: screenHeight * 0.04),

                  TextFormField(
                    controller: emailController,
                    keyboardType: TextInputType.emailAddress,
                    decoration: InputDecoration(
                      hintText: "Email",
                      suffixIcon: Padding(
                        padding: const EdgeInsets.all(12.0),
                        child: ImageIcon(
                          const AssetImage('assets/images/email Icon.png'),
                          color: Colors.grey,
                        ),
                      ),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(15),
                      ),
                    ),
                    validator: (value) => ValidationHelper.validateEmail(value),
                  ),

                  // مسافة مرنة قبل الزرار بدل 50
                  SizedBox(height: screenHeight * 0.06),

                  CustomButton(
                    text: "Send",
                    onPressed: handleSendRequest,
                    color: const Color(0xFF60ACBF),
                    textColor: Colors.white,
                    borderRadius: 15,
                    height: screenHeight * 0.065, // ارتفاع الزرار متجاوب
                  ),

                  // مسافة أمان إضافية تحت الزرار
                  SizedBox(height: screenHeight * 0.02),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
