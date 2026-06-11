import 'package:flutter/material.dart';
import 'package:easy_localization/easy_localization.dart' hide TextDirection;

bool isArabicLocale(BuildContext context) => context.locale.languageCode == 'ar';

TextDirection appTextDirection(BuildContext context) {
  return isArabicLocale(context) ? TextDirection.rtl : TextDirection.ltr;
}

AlignmentDirectional appStartAlignment() => AlignmentDirectional.centerStart;

IconData backIconForLocale(BuildContext context) {
  return isArabicLocale(context) ? Icons.arrow_forward : Icons.arrow_back;
}

IconData exitIconForLocale(BuildContext context) {
  return Icons.logout;
}

Widget directionalPage(BuildContext context, Widget child) {
  return Directionality(textDirection: appTextDirection(context), child: child);
}
