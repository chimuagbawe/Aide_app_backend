<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReviewRequest;
use App\Http\Requests\CreateServiceProviderAvailabilityRequest;
use App\Http\Requests\CreateServiceProviderRequest;
use App\Models\review_images;
use App\Models\reviews;
use App\Models\service_provider_availability;
use Illuminate\Http\Request;
use App\Models\service_providers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class serviceProviderController extends Controller
{
    public function createServiceProvider(CreateServiceProviderRequest $request): JsonResponse
    {
         // Extract validated data from the request
         $validatedData = $request->validated();

         // Create a new service provider record in the database
         $serviceProvider = service_providers::create([
             'user_id' => Auth::id(),
             'service_id' => $validatedData['service_id'],
             'hourly_rate' => $validatedData['hourly_rate'],
             'experience' => $validatedData['experience'],
             'location' => $validatedData['location'],
             'description' => $validatedData['description'],
         ]);

         // Return a JSON response with the created service provider
         return response()->json([
             'message' => 'Service provider created successfully.',
             'service_provider' => $serviceProvider,
         ], 201);
    }

    public function createServiceProviderAvailability(CreateServiceProviderAvailabilityRequest $request): JsonResponse
    {
         // Extract validated data from the request
         $validatedData = $request->validated();

         // Create a new service provider record in the database
         $serviceProvider = service_provider_availability::create([
             'service_provider_id' => $validatedData['service_provider_id'],
             'days_of_week' => json_encode($validatedData['days_of_week']), // Store the array as JSON
             'start_time' => $validatedData['start_time'],
             'end_time' => $validatedData['end_time'],
         ]);

         // Return a JSON response with the created service provider
         return response()->json([
             'message' => 'Service provider Availability created successfully.',
             'service_provider' => $serviceProvider,
         ], 201);
    }

    public function store(CreateReviewRequest $request): JsonResponse
    {
        // Get validated data from request
        $validated = $request->validated();

        // Create a review record
        $review = reviews::create([
            'user_id' => Auth::id(),
            'service_provider_id' => $validated['service_provider_id'],
            'rating' => $validated['rating'],
            'qualities'   =>  $validated['qualities'] ? json_encode($validated['qualities']) : null,
            'comment' => $validated['comment'] ?? null,
        ]);

        // Store review images if any
        if ($request->hasFile('images')) {
            foreach ($validated['images'] as $image) {
                $filename = date('YmdHi').uniqid().$image->getClientOriginalName();
                $image->move(public_path('upload/review_images'), $filename);
                review_images::create([
                    'review_id' => $review->id,
                    'image_url' => $filename,
                ]);
            }
        }

        // Return a JSON response
        return response()->json([
            'message' => 'Review submitted successfully.',
            'review' => $review,
        ], 201);
    }

    // Get all reviews for a specific service provider.
    public function getReviewsByProvider($serviceProviderId): JsonResponse
    {
        $reviews = reviews::where('service_provider_id', $serviceProviderId)->with('user')->get();
        $averageRating = $reviews->avg('rating');
        $reviewCount = $reviews->count();

        return response()->json([
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'reviewCount' => $reviewCount,
        ]);
    }


}