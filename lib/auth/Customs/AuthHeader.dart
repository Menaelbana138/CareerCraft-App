import 'package:flutter/material.dart';

class AuthHeader extends StatelessWidget {
  final String imagePath;

  const AuthHeader({
    super.key,
    required this.imagePath,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        const SizedBox(height: 40),
        Image.asset(
          imagePath,
          height: 100,
        ),
      ],
    );
  }
}
