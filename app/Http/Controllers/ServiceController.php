<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\services;

class ServiceController extends Controller
{
public function createService(CreateServiceRequest $request){
    $imagePath = null;
    if ($request->hasFile('photo')) {
        $imagePath = $request->file('photo')->store('upload/service_images', 'public');
    }

    // Create the service with validated data
    $service = Services::create(array_merge($request->validated(),['user_id' => Auth::id(),'image' => $imagePath,'status' => 'active',]));

    // Return a JSON response indicating success
    return response()->json([
        'message' => 'Service created successfully',
        'service' => $service,
    ]);
}

public function updateService(UpdateServiceRequest $request, $id){
    $service = Services::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        if ($service->photo && file_exists(public_path('upload/service_images/'.$service->photo))) {
            @unlink(public_path('upload/service_images/'.$service->photo));
        }
        $filename = date('YmdHi').$file->getClientOriginalName();
        $file->move(public_path('upload/service_images'), $filename);
        $service->photo = $filename;
    }
    $service->update($request->validated());

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

    if ($service->image && file_exists(public_path('upload/service_images/' . $service->image))) {
        @unlink(public_path('upload/service_images/' . $service->image));
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
    $services = Services::all();

    // Return a JSON response with the list of services
    return response()->json([
        'message' => 'Services retrieved successfully',
        'services' => $services,
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

public function getServicesByProvider($user_id){
    // Retrieve services that belong to the specified provider (user)
    $services = Services::where('user_id', $user_id)->get();

    return response()->json([
        'message' => 'Services retrieved successfully',
        'services' => $services,
    ]);
}

}