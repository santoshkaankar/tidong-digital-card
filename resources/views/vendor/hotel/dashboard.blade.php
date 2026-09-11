<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Vendor Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">Hotel Vendor Dashboard</h1>
        
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

        <form action="{{ route('vendor.hotel.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Hotel Name</label>
                <input type="text" name="hotel_name" value="{{ old('hotel_name', $hotel->hotel_name ?? '') }}" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Category</label>
                <input type="text" name="category" value="{{ old('category', $hotel->category ?? '') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                <input type="text" name="contact_number" value="{{ old('contact_number', $hotel->contact_number ?? '') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" rows="3" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">{{ old('address', $hotel->address ?? '') }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Save Hotel Details</button>
        </form>
    </div>
</body>
</html>