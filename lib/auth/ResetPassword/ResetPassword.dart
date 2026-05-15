import 'package:flutter/material.dart';
import 'package:graduationproject/auth/Customs/AuthHeader.dart';
import 'package:graduationproject/auth/Customs/CustomAuthAppBar.dart';
import 'package:graduationproject/auth/ResetPassword/ResetDialogMessage.dart';

import 'package:graduationproject/widget/Validator.dart';
import 'package:graduationproject/widget/custom_button.dart';

class ResetPasswordScreen extends StatefulWidget {
  const ResetPasswordScreen({super.key});

  @override
  State<ResetPasswordScreen> createState() => _ResetPasswordScreenState();
}

class _ResetPasswordScreenState extends State<ResetPasswordScreen> {
  final TextEditingController newPasswordController = TextEditingController();
  final TextEditingController confirmPasswordController =
      TextEditingController();
  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  bool isNewPasswordObscure = true;
  bool isConfirmPasswordObscure = true;
  bool _isLoading = false; // جاهز لحالة الـ API

  // ميثود إظهار Dialog نجاح تغيير الباسورد
  void _showSuccessDialog() {
    showDialog(
      context: context,
      barrierDismissible: false, // مبيتقفلش غير لما يضغط OK
      builder: (context) => const ResetSuccessDialog(),
    );
  }

  // ميثود الربط مع الـ API
  Future<void> handleResetPassword() async {
    if (_formKey.currentState!.validate()) {
      setState(() => _isLoading = true);

      try {
        print("Resetting password to: ${newPasswordController.text}");
        // هنا بيتم استدعاء الـ API Service الخاص بتغيير الباسورد
        // await AuthService().resetPassword(newPassword: newPasswordController.text);

        await Future.delayed(const Duration(seconds: 2)); // محاكاة وقت الطلب

        _showSuccessDialog(); // إظهار الـ Dialog الموجود في الصورة image_fc4361.png
      } catch (e) {
        print("Error: $e");
      } finally {
        setState(() => _isLoading = false);
      }
    }
  }

  @override
  void dispose() {
    newPasswordController.dispose();
    confirmPasswordController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // الحصول على أبعاد الشاشة
    final double screenHeight = MediaQuery.of(context).size.height;
    final double screenWidth = MediaQuery.of(context).size.width;

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: const CustomAuthAppBar(title: "Reset Password"),
      body: SafeArea(
        child: SingleChildScrollView(
          child: Padding(
            padding: EdgeInsets.symmetric(
                horizontal: screenWidth * 0.07), // مرن بدل 25
            child: Form(
              key: _formKey,
              child: Column(
                children: [
                  const AuthHeader(imagePath: 'assets/images/authImage.png'),

                  // مسافة متجاوبة (تقريباً 7% من طول الشاشة)
                  SizedBox(height: screenHeight * 0.07),

                  TextFormField(
                    controller: newPasswordController,
                    obscureText: isNewPasswordObscure,
                    decoration: InputDecoration(
                      hintText: "New Password",
                      suffixIcon: IconButton(
                        icon: Icon(
                            isNewPasswordObscure
                                ? Icons.visibility_off_outlined
                                : Icons.visibility_outlined,
                            color: Colors.grey),
                        onPressed: () => setState(
                            () => isNewPasswordObscure = !isNewPasswordObscure),
                      ),
                      border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(15)),
                    ),
                    validator: (value) =>
                        ValidationHelper.validatePassword(value),
                  ),

                  SizedBox(height: screenHeight * 0.025), // مسافة مرنة

                  TextFormField(
                    controller: confirmPasswordController,
                    obscureText: isConfirmPasswordObscure,
                    decoration: InputDecoration(
                      hintText: "Confirm Password",
                      suffixIcon: IconButton(
                        icon: Icon(
                            isConfirmPasswordObscure
                                ? Icons.visibility_off_outlined
                                : Icons.visibility_outlined,
                            color: Colors.grey),
                        onPressed: () => setState(() =>
                            isConfirmPasswordObscure =
                                !isConfirmPasswordObscure),
                      ),
                      border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(15)),
                    ),
                    validator: (value) {
                      if (value != newPasswordController.text) {
                        return "Passwords do not match";
                      }
                      return ValidationHelper.validatePassword(value);
                    },
                  ),

                  SizedBox(
                      height: screenHeight * 0.06), // مسافة مرنة قبل الزرار

                  _isLoading
                      ? const CircularProgressIndicator(
                          color: Color(0xFF60ACBF))
                      : CustomButton(
                          text: "Change Password",
                          onPressed: handleResetPassword,
                          color: const Color(0xFF60ACBF),
                          textColor: Colors.white,
                          borderRadius: 15,
                          height: screenHeight * 0.065, // ارتفاع الزرار متجاوب
                        ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
