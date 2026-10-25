import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:tidong_customer/main.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  testWidgets('First launch requires explicit consent', (tester) async {
    SharedPreferences.setMockInitialValues({});
    await tester.pumpWidget(const TidongApp());
    await tester.pumpAndSettle();
    expect(find.text('Welcome to TIDONG'), findsWidgets);
    expect(find.text('Read Terms & Conditions'), findsOneWidget);
    expect(find.text('Read Privacy Policy'), findsOneWidget);
    final continueButton = tester.widget<FilledButton>(
      find.widgetWithText(FilledButton, 'Accept and continue'));
    expect(continueButton.onPressed, isNull);
    await tester.tap(find.byType(CheckboxListTile));
    await tester.pump();
    await tester.tap(find.text('Accept and continue'));
    await tester.pumpAndSettle();
    expect(find.text('TIDONG'), findsOneWidget);
    final prefs = await SharedPreferences.getInstance();
    expect(prefs.getString('terms_consent_version'), tidongConsentVersion);
  });

  testWidgets('Customer home never fabricates products or orders', (tester) async {
    SharedPreferences.setMockInitialValues({
      'terms_consent_version': tidongConsentVersion,
    });
    await tester.pumpWidget(const TidongApp());
    await tester.pumpAndSettle();
    expect(find.text('TIDONG'), findsOneWidget);
    expect(find.text('Scan QR'), findsOneWidget);
    expect(find.text('TIDONG AI'), findsOneWidget);
    expect(find.textContaining('No live prices'), findsOneWidget);
    await tester.tap(find.byIcon(Icons.shopping_cart_outlined).last);
    await tester.pumpAndSettle();
    expect(find.text('Cart'), findsWidgets);
    await tester.tap(find.byIcon(Icons.person_outline).last);
    await tester.pumpAndSettle();
    expect(find.text('Customer Care'), findsOneWidget);
    expect(find.text('WhatsApp'), findsOneWidget);
  });
}
