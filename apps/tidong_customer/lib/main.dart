import 'package:geolocator/geolocator.dart';
import 'package:geocoding/geocoding.dart';
import 'package:flutter/material.dart';
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
