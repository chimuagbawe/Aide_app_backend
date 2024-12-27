<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\services;

class ServiceController extends Controller
{
public function createService(CreateServiceRequest $request){
    $filename = null;
    if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = date('YmdHi').uniqid().$file->getClientOriginalName();
            $file->move(public_path('upload/service_thumbnails'), $filename);
    }

    // Create the service with validated data
    $service = Services::create(array_merge($request->validated(),['user_id' => Auth::id(),'thumbnail' => $filename,'status' => 'active',]));

    // Return a JSON response indicating success
    return response()->json([
        'message' => 'Service created successfully',
        'service' => $service,
    ]);
}

public function updateService(UpdateServiceRequest $request, $id){
    $service = Services::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
    $filename = null;
    if ($request->hasFile('thumbnail')) {
        $file = $request->file('thumbnail');
        if ($service->thumbnail && file_exists(public_path('upload/service_thumbnails/'.$service->thumbnail))) {
            @unlink(public_path('upload/service_thumbnails/'.$service->thumbnail));
        }
        $filename = date('YmdHi').uniqid().$file->getClientOriginalName();
        $file->move(public_path('upload/service_thumbnails'), $filename);
    }
    $service->update([
        'name' => $request->name,
        'description' => $request->description,
        'thumbnail' => $filename,
        'service_type' => $request->service_type
    ]);
    // if ($request->filled('name')) {
    //     $service->name = $request->input('name');
    // }
    // if ($request->filled('description')) {
    //     $service->description = $request->input('description');
    // }
    // if ($request->filled('thumbnail')) {
    //     $service->thumbnail = $filename;
    // }
    // if ($request->filled('service_type')) {
    //     $service->service_type = $request->input('service_type');
    // }

    // $service->update($request->validated());
    // why doesn't the above line work
    // why put method doesnt work

    return response()->json([
        'message' => 'Service updated successfully',
        'service' => $service,
    ]);
}

public function deleteService($id){
    $service = Services::findOrFail($id);

    if ($service->user_id !== Auth::id()) {
        return response()->json([
            'message' => 'Unauthorized action.',
        ], 403);
    }

    if ($service->image && file_exists(public_path('upload/service_thumbnails/' . $service->thumbnail))) {
        @unlink(public_path('upload/service_thumbnails/' . $service->thumbnail));
    }

    $service->delete();

    // Return a JSON response indicating success
    return response()->json([
        'message' => 'Service deleted successfully',
    ]);
}

public function getService($id){
    // Find the service by its ID, or return a 404 response if not found
    $service = Services::findOrFail($id);

    return response()->json([
        'message' => 'Service retrieved successfully',
        'service' => $service,
    ]);
}

public function getAllServices(){
    $services = Services::all()->limit(4)->get();

    // Return a JSON response with the list of services
    return response()->json([
        'message' => 'Services retrieved successfully',
        'services' => $services,
    ]);
}

public function getPopularServices()
{
    // Fetch services ordered by the number of bookings, descending
    $popularServices = Services::orderBy('bookings', 'desc')->take(4)->get();

    // Return a JSON response with the list of popular services
    return response()->json([
        'message' => 'Popular services retrieved successfully',
        'services' => $popularServices,
    ]);
}


public function getServicesByCategory($category_id){
    // Retrieve services that belong to the specified category
    $services = Services::where('category_id', $category_id)->get();

    return response()->json([
        'message' => 'Services retrieved successfully',
        'services' => $services,
    ]);
}

public function getServicesByProvider($user_id) {
    // Retrieve services that belong to the specified provider (user)
    $services = Services::where('user_id', $user_id)->get();

    // Check if the collection is empty
    if ($services->isEmpty()) {
        return response()->json(['message' => 'No service created'], 404);
    }

    return response()->json([
        'message' => 'Service retrieved successfully',
        'services' => $services,
    ]);
}


}