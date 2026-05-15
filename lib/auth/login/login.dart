import 'package:flutter/material.dart';
import 'package:graduationproject/auth/Customs/AuthFooter.dart';
import 'package:graduationproject/auth/Customs/AuthHeader.dart';
import 'package:graduationproject/auth/Customs/CustomAuthAppBar.dart';
import 'package:graduationproject/auth/ForgetPassword/forgetpasswordPage.dart';
import 'package:graduationproject/auth/Sign%20up/SignUpPage.dart';
import 'package:graduationproject/widget/Validator.dart';
import 'package:graduationproject/widget/custom_button.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final TextEditingController emailController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  bool isObscure = true;

  @override
  void dispose() {
    emailController.dispose();
    passwordController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // الحصول على أبعاد الشاشة
    final double screenWidth = MediaQuery.of(context).size.width;
    final double screenHeight = MediaQuery.of(context).size.height;

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: CustomAuthAppBar(title: "Login"),
      body: SafeArea(
        child: SingleChildScrollView(
          // الـ BouncingScrollPhysics بيخلي التجربة أحسن في السكرول
          physics: const BouncingScrollPhysics(),
          child: Padding(
            padding: EdgeInsets.symmetric(
                horizontal: screenWidth * 0.07), // 7% من عرض الشاشة
            child: Form(
              key: _formKey,
              child: Column(
                children: [
                  const AuthHeader(
                    imagePath: 'assets/images/authImage.png',
                  ),

                  SizedBox(height: screenHeight * 0.05),

                  // حقل الإيميل
                  TextFormField(
                    controller: emailController,
                    keyboardType: TextInputType.emailAddress,
                    decoration: InputDecoration(
                      hintText: "Email",
                      suffixIcon: Padding(
                        padding: const EdgeInsets.all(12.0),
                        child: ImageIcon(
                          AssetImage('assets/images/email Icon.png'),
                          color: Colors.grey,
                        ),
                      ),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(15),
                        borderSide: const BorderSide(color: Colors.grey),
                      ),
                    ),
                    validator: (value) => ValidationHelper.validateEmail(value),
                  ),

                  SizedBox(height: screenHeight * 0.02), // مسافة 2% من الطول

                  // حقل الباسورد
                  TextFormField(
                    controller: passwordController,
                    obscureText: isObscure,
                    decoration: InputDecoration(
                      hintText: "Password",
                      suffixIcon: IconButton(
                        icon: Icon(
                          isObscure
                              ? Icons.visibility_off_outlined
                              : Icons.visibility_outlined,
                          color: Colors.grey,
                        ),
                        onPressed: () => setState(() => isObscure = !isObscure),
                      ),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(15),
                        borderSide: const BorderSide(color: Colors.grey),
                      ),
                    ),
                    validator: (value) =>
                        ValidationHelper.validatePassword(value),
                  ),

                  // زر نسيت كلمة المرور
                  Align(
                    alignment: Alignment.centerRight,
                    child: TextButton(
                      onPressed: () {
                        Navigator.push(
                            context,
                            MaterialPageRoute(
                                builder: (context) =>
                                    const ForgetPasswordScreen()));
                      },
                      child: Text(
                        "Forget password?",
                        style: TextStyle(
                          color: const Color(0xFF071012),
                          fontSize: screenWidth * 0.035, // خط متجاوب
                        ),
                      ),
                    ),
                  ),

                  SizedBox(height: screenHeight * 0.01),

                  // زر تسجيل الدخول
                  CustomButton(
                    text: "Login",
                    onPressed: () {
                      if (_formKey.currentState!.validate()) {
                        print("Email: ${emailController.text}");
                      }
                    },
                    color: const Color(0xFF60ACBF),
                    textColor: Colors.white,
                    borderRadius: 15,
                    height: screenHeight * 0.065, // ارتفاع الزر بالنسبة للشاشة
                    fontWeight: FontWeight.bold,
                  ),

                  SizedBox(height: screenHeight * 0.03),

                  // الفوتر (Social Login & Sign Up)
                  AuthSocialFooter(
                    promptText: "Don't Have an account? ",
                    actionText: "Sign Up",
                    onActionTap: () {
                      Navigator.push(
                          context,
                          MaterialPageRoute(
                              builder: (context) => const SignUpScreen()));
                    },
                    onGoogleTap: () => print("Google Login"),
                    onFacebookTap: () => print("Facebook Login"),
                  ),

                  // مسافة إضافية في الأسفل عشان السكرول
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
