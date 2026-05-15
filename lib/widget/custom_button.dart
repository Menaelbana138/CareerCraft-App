import 'package:flutter/material.dart';
import 'package:graduationproject/utility/button_style.dart';

enum ButtonType { primary, secondary, outlined, textOnly }

class CustomButton extends StatelessWidget {
  final String text;
  final VoidCallback onPressed;
  final ButtonType type;
  final IconData? icon;
  final double? width;
  final double? height;
  final Color? color;
  final Color? textColor;
  final Color? borderColor;
  final double? borderWidth; // 1. إضافة متغير سمك الحدود
  final double? borderRadius;
  final double? elevation;
  final EdgeInsetsGeometry? padding;
  final double? fontSize;
  final FontWeight? fontWeight;
  final bool isFullWidth;
  final bool isLoading;
  final String? imagePath;
  final double imageSize;
  final bool iconOnRight;

  const CustomButton({
    Key? key,
    required this.text,
    required this.onPressed,
    this.type = ButtonType.primary,
    this.icon,
    this.fontSize,
    this.fontWeight,
    this.color,
    this.textColor,
    this.borderColor,
    this.borderWidth,
    this.borderRadius,
    this.elevation,
    this.padding,
    this.isFullWidth = true,
    this.isLoading = false,
    this.width,
    this.height,
    this.imagePath,
    this.imageSize = 20,
    this.iconOnRight = false,
  }) : super(key: key);

  Color _getBackgroundColor() {
    switch (type) {
      case ButtonType.primary:
        return color ?? ButtonStyles.primaryColor;
      case ButtonType.secondary:
        return color ?? ButtonStyles.secondaryColor;
      case ButtonType.outlined:
      case ButtonType.textOnly:
        return Colors.transparent;
    }
  }

  Color _getTextColor() {
    switch (type) {
      case ButtonType.primary:
      case ButtonType.secondary:
        return textColor ?? ButtonStyles.textColor;
      case ButtonType.outlined:
      case ButtonType.textOnly:
        return textColor ?? ButtonStyles.primaryColor;
    }
  }

  // 3. تعديل الوظيفة لاستخدام السمك الممرر
  BoxBorder? _getBorder() {
    if (type == ButtonType.outlined) {
      return Border.all(
        color: borderColor ?? ButtonStyles.primaryColor,
        width: borderWidth ?? 2.0, // يستخدم القيمة الممررة أو 2 كقيمة افتراضية
      );
    }
    // في حالة النوع العادي ولكن نريد إضافة حدود مخصصة
    if (borderColor != null) {
      return Border.all(
        color: borderColor!,
        width: borderWidth ?? 1.0,
      );
    }
    return null;
  }

  Widget? _buildLeadingWidget() {
    if (imagePath != null) {
      return Image.asset(
        imagePath!,
        width: imageSize,
        height: imageSize,
      );
    } else if (icon != null) {
      return Icon(icon, size: 20, color: _getTextColor());
    }
    return null;
  }

  @override
  Widget build(BuildContext context) {
    final screenWidth = MediaQuery.of(context).size.width;
    final leadingWidget = _buildLeadingWidget();

    final buttonChild = isLoading
        ? SizedBox(
            width: screenWidth * 0.06,
            height: screenWidth * 0.06,
            child: const CircularProgressIndicator(
                strokeWidth: 2, color: Colors.white),
          )
        : Row(
            mainAxisSize: MainAxisSize.min,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              if (!iconOnRight && leadingWidget != null) ...[
                leadingWidget,
                SizedBox(width: screenWidth * 0.02),
              ],
              Text(
                text,
                style: TextStyle(
                  color: _getTextColor(),
                  fontSize: fontSize ?? screenWidth * 0.045,
                  fontWeight: fontWeight ?? ButtonStyles.fontWeight,
                ),
              ),
              if (iconOnRight && leadingWidget != null) ...[
                SizedBox(width: screenWidth * 0.02),
                leadingWidget,
              ],
            ],
          );

    return AnimatedContainer(
      duration: ButtonStyles.animationDuration,
      width: isFullWidth ? double.infinity : width,
      height: height ?? screenWidth * 0.13,
      decoration: BoxDecoration(
        color: _getBackgroundColor(),
        borderRadius: BorderRadius.circular(borderRadius ?? screenWidth * 0.03),
        border: _getBorder(), // سيطبق السمك هنا
        boxShadow: [
          if ((elevation ?? ButtonStyles.elevation) > 0)
            BoxShadow(
              color: Colors.black26,
              blurRadius: elevation ?? ButtonStyles.elevation,
              offset: const Offset(0, 2),
            ),
        ],
      ),
      child: Material(
        // أفضل استخدام Material فوق InkWell للحصول على الـ Splash effect
        color: Colors.transparent,
        child: InkWell(
          borderRadius:
              BorderRadius.circular(borderRadius ?? screenWidth * 0.03),
          onTap: isLoading ? null : onPressed,
          child: Padding(
            padding: padding ?? EdgeInsets.zero,
            child: Center(child: buttonChild),
          ),
        ),
      ),
    );
  }
}
