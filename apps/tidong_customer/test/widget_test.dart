import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:tidong_customer/main.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  testWidgets('Consent screen requires explicit agreement', (tester) async {
    SharedPreferences.setMockInitialValues({});
    await tester.pumpWidget(const TidongApp());
    await tester.pumpAndSettle();

    expect(find.text('Welcome to TIDONG'), findsWidgets);
    expect(find.text('Read Terms & Conditions'), findsOneWidget);
    expect(find.text('Read Privacy Policy'), findsOneWidget);
    final button = tester.widget<FilledButton>(
      find.widgetWithText(FilledButton, 'Accept and continue'));
    expect(button.onPressed, isNull);
    await tester.tap(find.byType(CheckboxListTile));
    await tester.pump();
    final enabled = tester.widget<FilledButton>(
      find.widgetWithText(FilledButton, 'Accept and continue'));
    expect(enabled.onPressed, isNotNull);
  });

  testWidgets('Legacy offline screen never fabricates products or orders',
      (tester) async {
    SharedPreferences.setMockInitialValues({});
    await tester.pumpWidget(const MaterialApp(home: CustomerHome()));
    await tester.pumpAndSettle();
    expect(find.text('Scan QR'), findsOneWidget);
    expect(find.textContaining('No live prices'), findsOneWidget);
  });
}
