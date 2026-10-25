# TIDONG Customer — Windows APK build

This is a Flutter Android client of the existing Laravel customer dashboard. It does not contain mock orders or prices. It is not a full native API implementation.

## Prerequisites
- Flutter SDK and Android SDK installed.
- PowerShell, with Flutter on PATH.
- Internet access to tidong.in on the device.

## Build from the downloaded branch ZIP

Open PowerShell in the repository root (where the `apps` directory exists):

```powershell
$env:Path = "C:\src\flutter\flutter_windows_3.47.8-stable\flutter\bin;$env:Path"
cd .\apps\tidong_customer
flutter --version
Select-String -Path .\lib\main.dart -Pattern "void main\(\) => runApp\(const TidongApp\(\)\)"
flutter create --platforms android --project-name tidong_customer --org com.tidong .
Select-String -Path .\lib\main.dart -Pattern "void main\(\) => runApp\(const TidongApp\(\)\)"
Select-String -Path .\android\app\src\main\AndroidManifest.xml -Pattern 'android.permission.INTERNET|TIDONG Customer'
flutter pub get
flutter analyze
flutter test
flutter build apk --debug
```

If either `Select-String` for `TidongApp` returns no result, STOP: the wrong source is being built or `flutter create` replaced it. Restore the original `lib/main.dart` from this branch before building.

APK output: `build\app\outputs\flutter-apk\app-debug.apk`.

## Actual behavior
The app shows terms consent on first launch, then loads `https://tidong.in/customer` in an embedded WebView. Server-side login and customer role authorization are required. Verify login, session persistence, dashboard, orders and checkout on an actual Android phone. Live backend and payments have NOT been verified by these instructions.

Do not use the APK for real transactions until testing is complete.
