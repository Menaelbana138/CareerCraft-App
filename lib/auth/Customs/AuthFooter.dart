import 'package:flutter/material.dart';

class AuthSocialFooter extends StatelessWidget {
  final String
      promptText; // "Don't Have an account?" أو "Already have an account?"
  final String actionText; // "Sign Up" أو "Login"
  final VoidCallback onActionTap; // التنقل بين الصفحات
  final VoidCallback onGoogleTap;
  final VoidCallback onFacebookTap;

  const AuthSocialFooter({
    super.key,
    required this.promptText,
    required this.actionText,
    required this.onActionTap,
    required this.onGoogleTap,
    required this.onFacebookTap,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        const SizedBox(height: 35),
        Row(
          children: [
            Expanded(child: Divider(thickness: 1, color: Colors.grey[300])),
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 10),
              child: Text(
                "or use social media",
                style: TextStyle(color: Color(0xFF62686A), fontSize: 14),
              ),
            ),
            Expanded(child: Divider(thickness: 1, color: Colors.grey[300])),
          ],
        ),
        const SizedBox(height: 25),
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            _buildSocialIcon('assets/images/Gmail.png', onGoogleTap),
            const SizedBox(width: 20),
            _buildSocialIcon('assets/images/Facebook.png', onFacebookTap),
          ],
        ),
        const SizedBox(height: 35),
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(promptText,
                style: const TextStyle(color: Colors.black, fontSize: 14)),
            GestureDetector(
              onTap: onActionTap,
              child: Text(
                actionText,
                style: const TextStyle(
                  color: Color(0xFF60ACBF),
                  fontWeight: FontWeight.bold,
                  decoration: TextDecoration.underline,
                  decorationColor: Color(0xFF60ACBF),
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 20),
      ],
    );
  }

  Widget _buildSocialIcon(String path, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(4),
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: Colors.white,
          border: Border.all(color: Colors.grey[200]!),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 5,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Image.asset(path, height: 40, width: 45),
      ),
    );
  }
}
