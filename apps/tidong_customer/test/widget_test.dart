import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:tidong_customer/main.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();
  testWidgets('TIDONG app shows navigation and no fabricated products', (tester) async {
    SharedPreferences.setMockInitialValues({});
    await tester.pumpWidget(const TidongApp());
    await tester.pumpAndSettle();
    expect(find.text('TIDONG'), findsOneWidget);
    expect(find.text('Cart'), findsWidgets);
    expect(find.textContaining('No live prices'), findsOneWidget);
    await tester.tap(find.byIcon(Icons.shopping_cart_outlined).last);
    await tester.pumpAndSettle();
    expect(find.text('Cart'), findsWidgets);
  });
}
