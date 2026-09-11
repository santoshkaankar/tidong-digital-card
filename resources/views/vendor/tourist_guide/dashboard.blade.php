<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourist Guide Vendor Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">Tourist Guide Vendor Dashboard</h1>
        
        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 p-4 bg-gray-50 rounded border">
            <p><strong>Vendor Name:</strong> {{ $vendor->name }}</p>
            <p><strong>Email:</strong> {{ $vendor->email }}</p>
            <p><strong>Role:</strong> {{ ucfirst($vendor->role) }}</p>
        </div>

        <form action="{{ route('vendor.tourist_guide.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Agency / Guide Name</label>
                <input type="text" name="agency_name" value="{{ old('agency_name', $guide->agency_name ?? '') }}" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">License Number</label>
                <input type="text" name="license_no" value="{{ old('license_no', $guide->license_no ?? '') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                <input type="text" name="contact_number" value="{{ old('contact_number', $guide->contact_number ?? '') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Languages Known (e.g. English, Hindi, Indonesian)</label>
                <input type="text" name="languages" value="{{ old('languages', $guide->languages ?? '') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" rows="3" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">{{ old('address', $guide->address ?? '') }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Save Tourist Guide Details</button>
        </form>
    </div>
</body>
</html>