import 'package:flutter/material.dart';
import 'package:graduationproject/auth/Customs/AuthFooter.dart';
import 'package:graduationproject/auth/Customs/AuthHeader.dart';
import 'package:graduationproject/auth/Customs/CustomAuthAppBar.dart';
import 'package:graduationproject/auth/Sign%20up/SignupDialogMessage.dart';
import 'package:graduationproject/auth/login/login.dart';
import 'package:graduationproject/widget/Validator.dart';
import 'package:graduationproject/widget/custom_button.dart';

class SignUpScreen extends StatefulWidget {
  const SignUpScreen({super.key});

  @override
  State<SignUpScreen> createState() => _SignUpScreenState();
}

class _SignUpScreenState extends State<SignUpScreen> {
  // Controllers للربط مع الـ API
  final TextEditingController nameController = TextEditingController();
  final TextEditingController emailController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  final TextEditingController confirmPasswordController =
      TextEditingController();

  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  bool isObscure = true;
  bool isConfirmObscure = true;
  bool _isLoading = false;

  @override
  void dispose() {
    nameController.dispose();
    emailController.dispose();
    passwordController.dispose();
    confirmPasswordController.dispose();
    super.dispose();
  }

  // ميثود منفصلة للـ Sign Up لتقليل الزحمة في الـ Build
  void _handleSignUp() async {
    if (_formKey.currentState!.validate()) {
      setState(() => _isLoading = true);

      try {
        // كود الـ API هينكتب هنا
        print("Registering: ${nameController.text}");

        // محاكاة لطلب الـ API
        await Future.delayed(const Duration(seconds: 2));

        if (mounted) {
          showDialog(
            context: context,
            barrierDismissible: false,
            builder: (context) => const SignUpSuccessDialog(
              imagePath: 'assets/images/signupDialog.png',
            ),
          );
        }
      } catch (e) {
        debugPrint("Error during signup: $e");
      } finally {
        if (mounted) setState(() => _isLoading = false);
      }
    }
  }

  @override
  @override
  Widget build(BuildContext context) {
    // تعريف الأبعاد
    final double screenWidth = MediaQuery.of(context).size.width;
    final double screenHeight = MediaQuery.of(context).size.height;

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: const CustomAuthAppBar(title: "Sign Up"),
      body: SafeArea(
        child: ListView(
          physics: const BouncingScrollPhysics(),
          children: [
            Padding(
              padding: EdgeInsets.symmetric(horizontal: screenWidth * 0.07),
              child: Form(
                key: _formKey,
                child: Column(
                  children: [
                    const AuthHeader(imagePath: 'assets/images/authImage.png'),

                    // مسافة مرنة 4% من طول الشاشة
                    SizedBox(height: screenHeight * 0.04),

                    TextFormField(
                      controller: nameController,
                      decoration:
                          _buildInputDecoration("Name", Icons.person_outline),
                      validator: (value) =>
                          ValidationHelper.validateName(value),
                    ),

                    SizedBox(height: screenHeight * 0.02),

                    TextFormField(
                      controller: emailController,
                      decoration:
                          _buildInputDecoration("Email", Icons.email_outlined),
                      validator: (value) =>
                          ValidationHelper.validateEmail(value),
                    ),

                    SizedBox(height: screenHeight * 0.02),

                    TextFormField(
                      controller: passwordController,
                      obscureText: isObscure,
                      decoration: _buildInputDecoration(
                        "Password",
                        null,
                        isPassword: true,
                        currentlyObscure: isObscure,
                        onToggle: () => setState(() => isObscure = !isObscure),
                      ),
                      validator: (value) =>
                          ValidationHelper.validatePassword(value),
                    ),

                    SizedBox(height: screenHeight * 0.02),

                    TextFormField(
                      controller: confirmPasswordController,
                      obscureText: isConfirmObscure,
                      decoration: _buildInputDecoration(
                        "Confirm Password",
                        null,
                        isPassword: true,
                        currentlyObscure: isConfirmObscure,
                        onToggle: () => setState(
                            () => isConfirmObscure = !isConfirmObscure),
                      ),
                      validator: (value) {
                        if (value != passwordController.text) {
                          return "Passwords do not match";
                        }
                        return null;
                      },
                    ),

                    SizedBox(height: screenHeight * 0.04),

                    _isLoading
                        ? const CircularProgressIndicator(
                            color: Color(0xFF60ACBF))
                        : CustomButton(
                            text: "Sign Up",
                            onPressed: _handleSignUp,
                            color: const Color(0xFF60ACBF),
                            textColor: Colors.white,
                            borderRadius: 15,
                            height: screenHeight * 0.065, // ارتفاع متجاوب
                          ),

                    SizedBox(height: screenHeight * 0.03),

                    AuthSocialFooter(
                      promptText: "Already Have an account? ",
                      actionText: "Login",
                      onActionTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                              builder: (context) => const LoginScreen()),
                        );
                      },
                      onGoogleTap: () => print("Google Sign Up"),
                      onFacebookTap: () => print("Facebook Sign Up"),
                    ),
                    SizedBox(height: screenHeight * 0.03),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  InputDecoration _buildInputDecoration(String label, IconData? icon,
      {bool isPassword = false,
      bool? currentlyObscure,
      VoidCallback? onToggle}) {
    return InputDecoration(
      labelText: label,
      labelStyle: const TextStyle(color: Colors.grey),
      floatingLabelBehavior: FloatingLabelBehavior.always,
      suffixIcon: isPassword
          ? IconButton(
              icon: Icon(
                  currentlyObscure!
                      ? Icons.visibility_off_outlined
                      : Icons.visibility_outlined,
                  color: Colors.grey),
              onPressed: onToggle,
            )
          : Icon(icon, color: Colors.grey),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(15),
        borderSide: const BorderSide(color: Colors.grey),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(15),
        borderSide: const BorderSide(color: Color(0xFF60ACBF)),
      ),
    );
  }
}
