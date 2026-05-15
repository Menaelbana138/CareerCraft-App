import 'package:flutter/material.dart';
import 'package:graduationproject/auth/Customs/AuthHeader.dart';
import 'package:graduationproject/auth/Customs/CustomAuthAppBar.dart';
import 'package:graduationproject/auth/Verification/ResendDialogMessage.dart';
import 'package:graduationproject/auth/Verification/PinCode.dart';
import 'package:graduationproject/widget/custom_button.dart';

class VerificationScreen extends StatefulWidget {
  const VerificationScreen({super.key});

  @override
  State<VerificationScreen> createState() => _VerificationScreenState();
}

class _VerificationScreenState extends State<VerificationScreen> {
  final List<TextEditingController> _controllers =
      List.generate(5, (_) => TextEditingController());
  final List<FocusNode> _focusNodes = List.generate(5, (_) => FocusNode());

  // 1. إضافة متغير حالة التحميل للـ API
  bool _isLoading = false;

  void _showResendDialog() {
    showDialog(
      context: context,
      builder: (context) => const SuccessDialog(
        title: "check your inbox",
        message: "We’ve sent you a new verification code",
      ),
    );
  }

  // 2. ميثود الربط مع الـ API (جاهزة)
  Future<void> _verifyOTP() async {
    String otp = _controllers.map((c) => c.text).join();

    if (otp.length < 5) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Please enter the full code")),
      );
      return;
    }

    setState(() => _isLoading = true);

    try {
      print("Calling API with OTP: $otp");
      // هنا هتحطي سطر الـ await بتاع الـ Service
      // var response = await AuthService().verifyOtp(otp);

      await Future.delayed(const Duration(seconds: 2)); // محاكاة للوقت

      // لو النجاح:
      // Navigator.pushReplacement(context, MaterialPageRoute(builder: (context) => const ResetPasswordScreen()));
    } catch (e) {
      print("Error: $e");
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final double screenWidth = MediaQuery.of(context).size.width;
    final double screenHeight = MediaQuery.of(context).size.height;

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: const CustomAuthAppBar(title: "Verification Code"),
      body: SafeArea(
        child: SingleChildScrollView(
          child: Padding(
            padding:
                EdgeInsets.symmetric(horizontal: screenWidth * 0.07), // مرن
            child: Column(
              children: [
                const AuthHeader(imagePath: 'assets/images/authImage.png'),
                SizedBox(height: screenHeight * 0.05), // مرن
                Text(
                  "We just send your an OTP code to your mail,\nenter the code we sent.",
                  textAlign: TextAlign.center,
                  style: TextStyle(
                      fontSize: screenWidth * 0.038, // حجم خط مرن
                      color: Colors.black54,
                      height: 1.5),
                ),
                SizedBox(height: screenHeight * 0.04),

                CustomPinCodeField(
                    controllers: _controllers, focusNodes: _focusNodes),

                Align(
                  alignment: Alignment.centerRight,
                  child: TextButton(
                    onPressed: _isLoading ? null : _showResendDialog,
                    child: const Text("Resend?",
                        style: TextStyle(color: Color(0xFF60ACBF))),
                  ),
                ),
                SizedBox(height: screenHeight * 0.04),

                _isLoading
                    ? const CircularProgressIndicator(color: Color(0xFF60ACBF))
                    : CustomButton(
                        text: "Done",
                        onPressed: _verifyOTP,
                        color: const Color(0xFF60ACBF),
                        textColor: Colors.white,
                        borderRadius: 15,
                        height: screenHeight * 0.065, // ارتفاع مرن
                      ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
