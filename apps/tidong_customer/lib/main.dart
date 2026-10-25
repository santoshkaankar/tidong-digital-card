import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() => runApp(const TidongApp());

class TidongApp extends StatelessWidget {
  const TidongApp({super.key});

  @override
  Widget build(BuildContext context) => MaterialApp(
    title: 'TIDONG Customer',
    debugShowCheckedModeBanner: false,
    theme: ThemeData(colorSchemeSeed: const Color(0xFF087F71), useMaterial3: true),
    home: const CustomerHome(),
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
          trailing: const Icon(Icons.edit_outlined), onTap: _editAddress),
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
