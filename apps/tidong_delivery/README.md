# TIDONG Delivery Partner — Android

Initial Android wrapper for the **existing** authenticated Laravel delivery
dashboard at https://tidong.in/delivery. The dashboard controls delivery
availability and job status; the app does not invent deliveries or payments.

## Build on Windows

From this directory:

```powershell
flutter create --platforms android --project-name tidong_delivery --org com.tidong .
flutter pub get
flutter analyze
flutter test
flutter build apk --debug
```

APK: `build/app/outputs/flutter-apk/app-debug.apk`.

## Current limits

This is a first-stage embedded website app, **not** a native offline delivery
application. Existing Laravel authentication and role middleware remain in
charge of authorization. Login, redirect, WebView session persistence,
availability updates, job updates, PIN verification, location permissions,
notifications, and actual Android build still need end-to-end testing.
Do not use in production until these checks pass.
