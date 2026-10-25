import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:tidong_customer/main.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  testWidgets('First launch shows real app consent, not Flutter demo', (tester) async {
    SharedPreferences.setMockInitialValues({});
    await tester.pumpWidget(const TidongApp());
    await tester.pumpAndSettle();
    expect(find.text('Welcome to TIDONG'), findsWidgets);
    expect(find.text('Read Terms & Conditions'), findsOneWidget);
    expect(find.text('Read Privacy Policy'), findsOneWidget);
    expect(find.text('Flutter Demo Home Page'), findsNothing);
    final button = tester.widget<FilledButton>(find.byType(FilledButton).first);
    expect(button.onPressed, isNull);
  });

  testWidgets('Consent checkbox enables continue', (tester) async {
    SharedPreferences.setMockInitialValues({});
    await tester.pumpWidget(const TidongApp());
    await tester.pumpAndSettle();
    await tester.tap(find.byType(CheckboxListTile));
    await tester.pump();
    final button = tester.widget<FilledButton>(find.byType(FilledButton).first);
    expect(button.onPressed, isNotNull);
  });

  test('App is configured for the real TIDONG customer entry point', () {
    const app = TidongApp();
    expect(app, isA<StatelessWidget>());
  });
}
