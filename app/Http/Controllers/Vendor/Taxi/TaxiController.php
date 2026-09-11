<?php

namespace App\Http\Controllers\Vendor\Taxi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor\TouristSpot;
use App\Models\Vendor\Taxi;
use App\Models\Vendor\TaxiBooking;
use App\Models\Vendor\BookingStop;
use App\Models\Vendor\VehicleDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TaxiController extends Controller
{
    private function getCommonData()
    {
        try {
            $spots = TouristSpot::where('status', 'active')->get();
        } catch (\Exception $e) {
            $spots = collect([]);
        }

        try {
            $taxis = Taxi::where('status', 'available')->get();
        } catch (\Exception $e) {
            $taxis = collect([]);
        }

        return compact('spots', 'taxis');
    }

    public function index(Request $request)
    {
        $data = $this->getCommonData();
        return view('taxi.booking', $data);
    }

    public function storeBooking(Request $request)
    {
        $request->validate([
            'pickup_location' => 'required|string',
            'pickup_datetime' => 'required',
            'taxi_id'         => 'required|exists:taxis,id',
            'selected_spots'  => 'required|array|min:1',
            'selected_spots.*'=> 'exists:tourist_spots,id'
        ]);

        $taxi = Taxi::findOrFail($request->taxi_id);

        DB::beginTransaction();
        try {
            $spotCount = count($request->selected_spots);
            $estimatedDistanceKm = $spotCount * 15;
            $estimatedDurationMins = $spotCount * 90;

            $distanceFare = $estimatedDistanceKm * ($taxi->rate_per_km ?? 0);
            $baseFare = $taxi->base_fare ?? 0;
            $grandTotal = $baseFare + $distanceFare;

            $booking = TaxiBooking::create([
                'booking_number'      => 'TB-' . strtoupper(Str::random(8)),
                'user_id'             => auth()->id() ?? null,
                'vendor_id'           => $taxi->user_id ?? auth()->id(),
                'taxi_id'             => $taxi->id,
                'pickup_location'     => $request->pickup_location,
                'pickup_datetime'     => $request->pickup_datetime,
                'total_distance_km'   => $estimatedDistanceKm,
                'total_duration_mins' => $estimatedDurationMins,
                'base_fare'           => $baseFare,
                'distance_fare'       => $distanceFare,
                'toll_charges'        => 0.00,
                'night_charges'       => 0.00,
                'tip_amount'          => 0.00,
                'grand_total'         => $grandTotal,
                'booking_status'      => 'pending',
                'payment_status'      => 'pending',
                'notes'               => $request->notes ?? ''
            ]);

            foreach ($request->selected_spots as $order => $spotId) {
                BookingStop::create([
                    'taxi_booking_id' => $booking->id,
                    'tourist_spot_id' => $spotId,
                    'stop_order'      => $order + 1
                ]);
            }

            DB::commit();
            return redirect()->route('vendor.taxi.success', $booking->id)->with('success', 'Taxi booking request submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Booking failed: ' . $e->getMessage());
        }
    }

    public function bookingSuccess($id)
    {
        $booking = TaxiBooking::with(['taxi', 'stops.spot', 'vendor'])->findOrFail($id);
        return view('taxi.success', compact('booking'));
    }

    public function updateCharges(Request $request, $id)
    {
        $booking = TaxiBooking::findOrFail($id);

        if ($request->has('toll_charges')) {
            $booking->toll_charges = $request->toll_charges;
        }

        if ($request->has('tip_amount')) {
            $booking->tip_amount = $request->tip_amount;
        }

        $booking->grand_total = ($booking->base_fare ?? 0) + ($booking->distance_fare ?? 0) + ($booking->toll_charges ?? 0) + ($booking->night_charges ?? 0) + ($booking->tip_amount ?? 0);
        $booking->save();

        return redirect()->back()->with('success', 'Charges updated successfully!');
    }

    public function activeRides()
    {
        try {
            $rides = TaxiBooking::where('vendor_id', auth()->id())->whereIn('booking_status', ['pending', 'accepted', 'in_progress'])->latest()->get();
        } catch (\Exception $e) {
            $rides = collect([]);
        }
        return view('taxi.active-rides', compact('rides'));
    }

    public function rideHistory()
    {
        try {
            $rides = TaxiBooking::where('vendor_id', auth()->id())->whereIn('booking_status', ['completed', 'cancelled'])->latest()->get();
        } catch (\Exception $e) {
            $rides = collect([]);
        }
        return view('taxi.ride-history', compact('rides'));
    }

    public function vehicleDocs()
    {
        try {
            $documents = VehicleDocument::where('user_id', auth()->id())->get()->keyBy('document_type');
        } catch (\Exception $e) {
            $documents = collect([]);
        }
        return view('taxi.vehicle-docs', compact('documents'));
    }

    public function uploadDoc(Request $request)
    {
        $request->validate([
            'document_type'   => 'required|string',
            'document_number' => 'required|string',
            'issue_date'      => 'required|date',
            'expiry_date'     => 'required|date|after:issue_date',
            'document_file'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $filePath = null;
        if ($request->hasFile('document_file')) {
            $filePath = $request->file('document_file')->store('vehicle_docs', 'public');
        }

        // Online Verification Check
        $apiVerificationPassed = $this->verifyDocumentViaApi($request->document_type, $request->document_number);
        $status = $apiVerificationPassed ? 'verified' : 'pending';

        $dataToUpdate = [
            'document_number'     => $request->document_number,
            'issue_date'          => $request->issue_date,
            'expiry_date'         => $request->expiry_date,
            'verification_status' => $status
        ];

        if ($filePath) {
            $dataToUpdate['document_file'] = $filePath;
        }

        VehicleDocument::updateOrCreate(
            [
                'user_id'       => auth()->id(),
                'document_type' => $request->document_type
            ],
            $dataToUpdate
        );

        $msg = $apiVerificationPassed 
            ? 'Document verified online and activated!' 
            : 'Document saved! Status pending for Admin approval.';

        return redirect()->back()->with('success', $msg);
    }

    private function verifyDocumentViaApi($type, $docNumber)
    {
        // Yahan Verification API ka code lagta h. Abhi ke liye fallback logic h.
        return false;
    }

    public function bookingOrders()
    {
        try {
            $bookings = TaxiBooking::where('vendor_id', auth()->id())->latest()->get();
        } catch (\Exception $e) {
            $bookings = collect([]);
        }
        return view('taxi.booking-orders', compact('bookings'));
    }

    public function paymentStatus()
    {
        try {
            $payments = TaxiBooking::where('vendor_id', auth()->id())->latest()->get();
        } catch (\Exception $e) {
            $payments = collect([]);
        }
        return view('taxi.payment-status', compact('payments'));
    }
}