import 'package:geolocator/geolocator.dart';
import 'package:geocoding/geocoding.dart';
import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() => runApp(const TidongApp());

const tidongConsentVersion = '2026-10-v1';

class TidongApp extends StatelessWidget {
  const TidongApp({super.key});

  @override
  Widget build(BuildContext context) => MaterialApp(
    title: 'TIDONG Customer',
    debugShowCheckedModeBanner: false,
    theme: ThemeData(
      colorScheme: ColorScheme.fromSeed(
        seedColor: const Color(0xFFF4A6B5),
        primary: const Color(0xFFD94B69),
        secondary: const Color(0xFFE78DA2),
      ),
      scaffoldBackgroundColor: const Color(0xFFFFF5F7),
      useMaterial3: true,
    ),
    home: const ConsentGate(),
  );
}

class ConsentGate extends StatefulWidget {
  const ConsentGate({super.key});

  @override
  State<ConsentGate> createState() => _ConsentGateState();
}

class _ConsentGateState extends State<ConsentGate> {
  bool loading = true;
  bool accepted = false;
  bool completed = false;

  @override
  void initState() {
    super.initState();
    _checkConsent();
  }

  Future<void> _checkConsent() async {
    final prefs = await SharedPreferences.getInstance();
    if (!mounted) return;
    setState(() {
      completed = prefs.getString('terms_consent_version') == tidongConsentVersion;
      loading = false;
    });
  }

  Future<void> _openPolicy(String path) async {
    final uri = Uri.https('tidong.in', path);
    if (!await launchUrl(uri, mode: LaunchMode.externalApplication) && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Could not open policy page. Please try again.')),
      );
    }
  }

  Future<void> _continue() async {
    if (!accepted) return;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('terms_consent_version', tidongConsentVersion);
    await prefs.setString('terms_consent_at', DateTime.now().toUtc().toIso8601String());
    if (mounted) setState(() => completed = true);
  }

  @override
  Widget build(BuildContext context) {
    if (loading) return const Scaffold(body: Center(child: CircularProgressIndicator()));
    if (completed) return const CustomerHome();
    return Scaffold(
      appBar: AppBar(title: const Text('Welcome to TIDONG')),
      body: SafeArea(child: Center(child: ConstrainedBox(
        constraints: const BoxConstraints(maxWidth: 480),
        child: ListView(padding: const EdgeInsets.all(24), children: [
          Icon(Icons.favorite_rounded, size: 70, color: Theme.of(context).colorScheme.primary),
          const SizedBox(height: 18),
          Text('Welcome to TIDONG', textAlign: TextAlign.center,
            style: Theme.of(context).textTheme.headlineMedium),
          const SizedBox(height: 18),
          const Text('Before continuing, please review the TIDONG Terms & Conditions and Privacy Policy on tidong.in. Your consent is required to use this app.'),
          const SizedBox(height: 12),
          const Text('Location is optional and requested only when you use location features. Camera and microphone permissions are requested when using scanning or voice features.'),
          const SizedBox(height: 12),
          Wrap(spacing: 8, children: [
            TextButton(onPressed: () => _openPolicy('/terms-and-conditions'),
              child: const Text('Read Terms & Conditions')),
            TextButton(onPressed: () => _openPolicy('/privacy-overview'),
              child: const Text('Read Privacy Policy')),
          ]),
          const SizedBox(height: 20),
          CheckboxListTile(
            value: accepted,
            onChanged: (value) => setState(() => accepted = value ?? false),
            title: const Text('I agree to the TIDONG Terms & Conditions and Privacy Policy.'),
            controlAffinity: ListTileControlAffinity.leading,
          ),
          const SizedBox(height: 12),
          FilledButton(onPressed: accepted ? _continue : null,
            child: const Text('Accept and continue')),
        ]),
      ))),
    );
  }
}

class TidongQrScanner extends StatefulWidget {
  const TidongQrScanner({super.key});
  @override
  State<TidongQrScanner> createState() => _TidongQrScannerState();
}

class _TidongQrScannerState extends State<TidongQrScanner> {
  bool handling = false;
  final MobileScannerController scanner = MobileScannerController();

  Future<void> _onDetect(BarcodeCapture capture) async {
    if (handling || capture.barcodes.isEmpty) return;
    final raw = capture.barcodes.first.rawValue;
    final uri = raw == null ? null : Uri.tryParse(raw);
    if (uri == null || uri.scheme != 'https' ||
        uri.host.toLowerCase() != 'tidong.in' ||
        !(uri.path.startsWith('/menu/') || uri.path.startsWith('/customer/'))) {
      setState(() => handling = true);
      await showDialog<void>(context: context, builder: (ctx) => AlertDialog(
        title: const Text('Unsupported QR code'),
        content: const Text('Only verified TIDONG links can be opened from this scanner.'),
        actions: [TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('OK'))],
      ));
      if (mounted) setState(() => handling = false);
      return;
    }
    setState(() => handling = true);
    await scanner.stop();
    if (!mounted) return;
    final open = await showDialog<bool>(context: context, builder: (ctx) => AlertDialog(
      title: const Text('Open TIDONG QR link?'),
      content: Text(uri.toString()),
      actions: [
        TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancel')),
        FilledButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Open')),
      ],
    ));
    if (open == true) await launchUrl(uri, mode: LaunchMode.externalApplication);
    if (mounted) {
      setState(() => handling = false);
      await scanner.start();
    }
  }

  @override
  void dispose() { scanner.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(title: const Text('Scan TIDONG QR')),
    body: Column(children: [
      Expanded(child: MobileScanner(controller: scanner, onDetect: _onDetect)),
      const Padding(padding: EdgeInsets.all(16), child: Text(
        'Scan a TIDONG restaurant table QR code. Links are checked before opening.',
        textAlign: TextAlign.center)),
    ]),
  );
}

class CustomerHome extends StatefulWidget {
  const CustomerHome({super.key});
  @override
  State<CustomerHome> createState() => _CustomerHomeState();
}

class _CustomerHomeState extends State<CustomerHome> {
  int tab = 0;
  String address = '';
  bool locating = false;
  final addressInput = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadAddress();
  }

  Future<void> _loadAddress() async {
    final prefs = await SharedPreferences.getInstance();
    if (mounted) setState(() => address = prefs.getString('delivery_address') ?? '');
  }

  Future<void> _editAddress() async {
    addressInput.text = address;
    final saved = await showDialog<bool>(context: context, builder: (ctx) => AlertDialog(
      title: const Text('Delivery address'),
      content: TextField(controller: addressInput, maxLines: 3,
        decoration: const InputDecoration(hintText: 'House, street, area, city and PIN')),
      actions: [
        TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancel')),
        FilledButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Save')),
      ],
    ));
    if (saved == true && addressInput.text.trim().isNotEmpty) {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('delivery_address', addressInput.text.trim());
      if (mounted) setState(() => address = addressInput.text.trim());
    }
  }

  Future<void> _detectLocation() async {
    setState(() => locating = true);
    try {
      if (!await Geolocator.isLocationServiceEnabled()) {
        throw Exception('Please enable location services');
      }
      var permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }
      if (permission == LocationPermission.denied ||
          permission == LocationPermission.deniedForever) {
        throw Exception('Location permission not granted. Enter address manually.');
      }
      final point = await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(accuracy: LocationAccuracy.medium));
      final results = await placemarkFromCoordinates(point.latitude, point.longitude);
      if (!mounted) return;
      final p = results.isEmpty ? null : results.first;
      addressInput.text = p == null
        ? '${point.latitude.toStringAsFixed(5)}, ${point.longitude.toStringAsFixed(5)}'
        : [p.street, p.subLocality, p.locality, p.administrativeArea, p.postalCode]
            .where((v) => v != null && v.trim().isNotEmpty).join(', ');
      final confirmed = await showDialog<bool>(context: context, builder: (ctx) => AlertDialog(
        title: const Text('Confirm delivery location'),
        content: TextField(controller: addressInput, maxLines: 3,
          decoration: const InputDecoration(labelText: 'Full address, house number and PIN')),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancel')),
          FilledButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Save address')),
        ],
      ));
      if (confirmed == true && addressInput.text.trim().isNotEmpty) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('delivery_address', addressInput.text.trim());
        if (mounted) setState(() => address = addressInput.text.trim());
      }
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
    } finally {
      if (mounted) setState(() => locating = false);
    }
  }

  Future<void> _openAi() async {
    final uri = Uri.https('tidong.in', '/customer/assistant/food');
    if (!await launchUrl(uri, mode: LaunchMode.externalApplication) && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Could not open TIDONG AI')),
      );
    }
  }

  Future<void> _openContact(Uri uri) async {
    if (!await launchUrl(uri, mode: LaunchMode.externalApplication) && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Unable to open link on this device.')),
      );
    }
  }

  Widget _contactLinks() => Padding(
    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
    child: Card(child: Padding(
      padding: const EdgeInsets.all(12),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text('Connect with TIDONG', style: Theme.of(context).textTheme.titleMedium),
        Wrap(spacing: 4, runSpacing: 4, children: [
          TextButton.icon(onPressed: () => _openContact(Uri.parse('https://www.youtube.com/@tidongin')),
            icon: const Icon(Icons.play_circle_outline), label: const Text('YouTube')),
          TextButton.icon(onPressed: () => _openContact(Uri.parse('https://www.facebook.com/profile.php?id=61575563442650')),
            icon: const Icon(Icons.people_outline), label: const Text('Facebook')),
          TextButton.icon(onPressed: () => _openContact(Uri.parse('https://x.com/TidongIndia')),
            icon: const Icon(Icons.alternate_email), label: const Text('X')),
          TextButton.icon(onPressed: () => _openContact(Uri.parse('https://www.instagram.com/tidong.in/')),
            icon: const Icon(Icons.camera_alt_outlined), label: const Text('Instagram')),
          TextButton.icon(onPressed: () => _openContact(Uri.parse('mailto:tidongindia@gmail.com')),
            icon: const Icon(Icons.email_outlined), label: const Text('Email')),
          TextButton.icon(onPressed: () => _openContact(Uri.parse('https://wa.me/919634759912')),
            icon: const Icon(Icons.chat_outlined), label: const Text('WhatsApp')),
        ]),
      ]),
    )),
  );

  @override
  void dispose() {
    addressInput.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    const titles = ['Home', 'Explore', 'Cart', 'Orders', 'Profile'];
    return Scaffold(
      appBar: AppBar(title: const Text('TIDONG'), actions: [
        IconButton(tooltip: 'Delivery address', icon: const Icon(Icons.location_on_outlined), onPressed: _editAddress),
      ]),
      body: Column(children: [
        ListTile(leading: const Icon(Icons.place_outlined),
          title: Text(address.isEmpty ? 'Set delivery address' : address, maxLines: 1, overflow: TextOverflow.ellipsis),
          trailing: IconButton(
            tooltip: 'Use current location',
            icon: locating ? const SizedBox(width: 20, height: 20,
              child: CircularProgressIndicator(strokeWidth: 2)) : const Icon(Icons.my_location),
            onPressed: locating ? null : _detectLocation),
          onTap: _editAddress),
        if (tab == 0) Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: Row(children: [
            Expanded(child: FilledButton.icon(
              onPressed: () => Navigator.push(context,
                MaterialPageRoute(builder: (_) => const TidongQrScanner())),
              icon: const Icon(Icons.qr_code_scanner), label: const Text('Scan QR'))),
            const SizedBox(width: 12),
            Expanded(child: FilledButton.tonalIcon(
              onPressed: _openAi,
              icon: const Icon(Icons.auto_awesome), label: const Text('TIDONG AI'))),
          ]),
        ),
        if (tab == 4) _contactLinks(),
        Expanded(child: Center(child: Padding(padding: const EdgeInsets.all(24),
          child: Column(mainAxisSize: MainAxisSize.min, children: [
            Icon([Icons.home_outlined, Icons.search, Icons.shopping_cart_outlined,
              Icons.receipt_long_outlined, Icons.person_outline][tab], size: 72),
            const SizedBox(height: 16),
            Text(titles[tab], style: Theme.of(context).textTheme.headlineMedium),
            const SizedBox(height: 12),
            const Text('TIDONG backend connection is pending. No live prices, orders or payments are shown.',
              textAlign: TextAlign.center),
          ])))),
      ]),
      bottomNavigationBar: NavigationBar(selectedIndex: tab, onDestinationSelected: (i) => setState(() => tab = i),
        destinations: const [
          NavigationDestination(icon: Icon(Icons.home_outlined), label: 'Home'),
          NavigationDestination(icon: Icon(Icons.search), label: 'Explore'),
          NavigationDestination(icon: Icon(Icons.shopping_cart_outlined), label: 'Cart'),
          NavigationDestination(icon: Icon(Icons.receipt_long_outlined), label: 'Orders'),
          NavigationDestination(icon: Icon(Icons.person_outline), label: 'Profile'),
        ]),
    );
  }
}
