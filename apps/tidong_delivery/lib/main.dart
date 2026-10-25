import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:webview_flutter/webview_flutter.dart';

void main() => runApp(const TidongDeliveryApp());

class TidongDeliveryApp extends StatelessWidget {
  const TidongDeliveryApp({super.key});

  @override
  Widget build(BuildContext context) => MaterialApp(
    title: 'TIDONG Delivery Partner',
    debugShowCheckedModeBanner: false,
    theme: ThemeData(
      colorScheme: ColorScheme.fromSeed(
        seedColor: const Color(0xFFD94B69),
        primary: const Color(0xFFD94B69),
      ),
      scaffoldBackgroundColor: const Color(0xFFFFF5F7),
      useMaterial3: true,
    ),
    home: const DeliveryHome(),
  );
}

class DeliveryHome extends StatefulWidget {
  const DeliveryHome({super.key});

  @override
  State<DeliveryHome> createState() => _DeliveryHomeState();
}

class _DeliveryHomeState extends State<DeliveryHome> {
  late final WebViewController controller;
  bool loading = true;
  String? error;
  int selected = 0;

  static final pages = <Uri>[
    Uri.https('tidong.in', '/delivery'),
    Uri.https('tidong.in', '/login'),
  ];

  @override
  void initState() {
    super.initState();
    controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(NavigationDelegate(
        onPageStarted: (_) {
          if (mounted) setState(() { loading = true; error = null; });
        },
        onPageFinished: (_) {
          if (mounted) setState(() => loading = false);
        },
        onWebResourceError: (e) {
          if (e.isForMainFrame == true && mounted) {
            setState(() {
              loading = false;
              error = 'Unable to load TIDONG. Check your internet connection.';
            });
          }
        },
        onNavigationRequest: (request) {
          final uri = Uri.tryParse(request.url);
          if (uri == null) return NavigationDecision.prevent;
          if (uri.scheme == 'https' &&
              (uri.host == 'tidong.in' || uri.host == 'www.tidong.in')) {
            return NavigationDecision.navigate;
          }
          if (uri.scheme == 'https' || uri.scheme == 'tel' ||
              uri.scheme == 'mailto' || uri.scheme == 'geo') {
            launchUrl(uri, mode: LaunchMode.externalApplication);
          }
          return NavigationDecision.prevent;
        },
      ))
      ..loadRequest(pages.first);
  }

  void openPage(int index) {
    setState(() => selected = index);
    controller.loadRequest(pages[index]);
  }

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(
      title: const Text('TIDONG Delivery Partner'),
      actions: [
        IconButton(
          tooltip: 'Refresh',
          icon: const Icon(Icons.refresh),
          onPressed: () => controller.reload(),
        ),
      ],
    ),
    body: Stack(children: [
      WebViewWidget(controller: controller),
      if (loading) const LinearProgressIndicator(),
      if (error != null)
        Center(child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(mainAxisSize: MainAxisSize.min, children: [
            Text(error!, textAlign: TextAlign.center),
            const SizedBox(height: 12),
            FilledButton(
              onPressed: () => controller.reload(),
              child: const Text('Retry'),
            ),
          ]),
        )),
    ]),
    bottomNavigationBar: NavigationBar(
      selectedIndex: selected,
      onDestinationSelected: openPage,
      destinations: const [
        NavigationDestination(
          icon: Icon(Icons.delivery_dining_outlined),
          label: 'Delivery Jobs',
        ),
        NavigationDestination(
          icon: Icon(Icons.login_outlined),
          label: 'Login',
        ),
      ],
    ),
  );
}
