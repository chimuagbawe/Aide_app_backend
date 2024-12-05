<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\bookings;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\checkAdmin;
use App\Models\promo;
use App\Models\services;
use Illuminate\Http\Request;

class bookingsController extends Controller
{

    public function store(StoreBookingRequest $request){

        $validated = $request->validated();

        $promoResult = $this->validatePromo($validated);

        // Check if there's any error from promo validation
        if ($promoResult['error']) {
            return response()->json(['error' => $promoResult['error']], 422);
        }

        if ($this->isOverlappingBooking($validated)) {
            return response()->json(['error' => 'The provider is already booked for this time slot.'], 422);
        }

        // Create the booking with the validated total cost and discount
        $booking = bookings::create([
            'user_id' => Auth::id(),
            'provider_id' => $validated['provider_id'],
            'service_id' => $validated['service_id'],
            'booking_date' => $validated['booking_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'location' => $validated['location'],
            'promo_code' => $validated['promo_code'],
            'discount_amount' => $promoResult['discount'],
            'total_cost' => $promoResult['totalCost'],
            'payment_method' => $validated['payment_method'],
            'status' => $validated['status'],
        ]);

        // Increment the bookings count for the service
        services::findOrFail($validated['service_id'])->increment('bookings');

        return response()->json([
            'message' => 'Booking created successfully!',
            'booking' => $booking,
        ], 201);
    }

    private function isOverlappingBooking(array $validated){
        return bookings::where('provider_id', $validated['provider_id'])
            ->where('booking_date', $validated['booking_date'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
            })->exists();
    }

    private function validatePromo(array $validated){
        // Find the promo
        $promo = Promo::where('code', $validated['promo_code'])
                      ->where('active', true)
                      ->where('start_date', '<=', now())
                      ->where('end_date', '>=', now())
                      ->first();

        if (!$promo) {
            return [
                'error' => 'Invalid or expired promo code.',
                'discount' => 0,
                'totalCost' => $validated['total_cost']
            ];
        }

        // Check usage limit
        if ($promo->usage_limit && $promo->usage_count >= $promo->usage_limit) {
            return [
                'error' => 'Promo code usage limit exceeded.',
                'discount' => 0,
                'totalCost' => $validated['total_cost']
            ];
        }

        // Calculate discount
        $discount = $promo->discount_percentage
            ? ($validated['total_cost'] * $promo->discount_percentage) / 100
            : $promo->discount_amount ?? 0;

        // Calculate total cost after discount
        $totalCost = $validated['total_cost'] - $discount;

        if ($totalCost < 0) {
            $totalCost = 0;
        }

        // Increment promo usage count
        $promo->increment('usage_count');

        return [
            'error' => null, // No error
            'discount' => $discount,
            'totalCost' => $totalCost
        ];
    }

    public function update(UpdateBookingRequest $request, $id){
        $validated = $request->validated();
        $booking = bookings::findOrFail($id);

        $promoResult = $this->validatePromo($validated);

        // Check if there's any error from promo validation
        if ($promoResult['error']) {
            return response()->json(['error' => $promoResult['error']], 422);
        }

        if ($this->OverlappingBooking($validated, $id)) {
            return response()->json(['error' => 'The provider is already booked for this time slot.'], 422);
        }

        $booking->update([
            'provider_id' => $validated['provider_id'],
            'service_id' => $validated['service_id'],
            'booking_date' => $validated['booking_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'location' => $validated['location'],
            'promo_code' => $validated['promo_code'],
            'discount_amount' => $promoResult['discount'],
            'total_cost' => $promoResult['totalCost'],
        ]);

        return response()->json([
            'message' => 'Booking updated successfully!',
            'booking' => $booking,
        ], 200);
    }

    private function OverlappingBooking(array $validated, $id){
        return bookings::where('provider_id', $validated['provider_id'])
            ->where('booking_date', $validated['booking_date'])
            ->where('id', '!=', $id) // Ensure we don't check the current booking
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
            })->exists();
    }

    public function cancelBooking(Request $request, $bookingId){
        $booking = bookings::find($bookingId);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found.'], 404);
        }
        if ($booking->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'You do not have permission to cancel this booking.'], 403);
        }
        if ($booking->status === 'canceled') {
            return response()->json(['message' => 'Booking is already canceled.'], 200);
        }

        $booking->status = 'canceled';
        $booking->save();

        // Optionally, log the cancellation or take other actions (e.g. sending notifications)

        return response()->json([
            'message' => 'Booking canceled successfully!',
            'booking' => $booking
        ], 200);
    }

    public function confirmBooking($bookingId){
        $booking = Bookings::find($bookingId);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found.'], 404);
        }
        if ($booking->status !== 'pending') {
            return response()->json(['error' => 'Only pending bookings can be confirmed.'], 422);
        }

        $booking->status = 'confirmed';
        $booking->save();

        return response()->json([
            'message' => 'Booking confirmed successfully!',
            'booking' => $booking
        ], 200);
    }

}