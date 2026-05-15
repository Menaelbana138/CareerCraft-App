import 'package:flutter/material.dart';
import 'package:flutter/services.dart'; // 1. لازم تضيفي الـ import ده

// ... الـ imports كما هي

class CustomPinCodeField extends StatelessWidget {
  final List<TextEditingController> controllers;
  final List<FocusNode> focusNodes;

  const CustomPinCodeField({
    super.key,
    required this.controllers,
    required this.focusNodes,
  });

  @override
  Widget build(BuildContext context) {
    final double screenWidth = MediaQuery.of(context).size.width;

    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
      children:
          List.generate(5, (index) => _buildBox(context, index, screenWidth)),
    );
  }

  Widget _buildBox(BuildContext context, int index, double screenWidth) {
    return SizedBox(
      // العرض والارتفاع بقوا يعتمدوا على عرض الشاشة
      width: screenWidth * 0.13,
      height: screenWidth * 0.15,
      child: TextFormField(
        controller: controllers[index],
        focusNode: focusNodes[index],
        textAlign: TextAlign.center,
        keyboardType: TextInputType.number,
        inputFormatters: [
          FilteringTextInputFormatter.digitsOnly,
        ],
        maxLength: 1,
        style: TextStyle(
            fontSize: screenWidth * 0.055, // حجم الرقم جوه البوكس مرن
            fontWeight: FontWeight.bold),
        decoration: InputDecoration(
          counterText: "",
          fillColor: const Color(0xFFF1F8F9),
          filled: true,
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: BorderSide(color: Colors.grey.shade300),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: const BorderSide(color: Color(0xFF60ACBF), width: 2),
          ),
        ),
        onChanged: (value) {
          if (value.isNotEmpty && index < 4) {
            focusNodes[index + 1].requestFocus();
          } else if (value.isEmpty && index > 0) {
            focusNodes[index - 1].requestFocus();
          }
          if (value.isNotEmpty && index == 4) {
            focusNodes[index].unfocus();
          }
        },
      ),
    );
  }
}
