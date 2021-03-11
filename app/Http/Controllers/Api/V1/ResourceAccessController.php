<?php
namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\ResourceAccess;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ResourceRequest;
use App\Http\Resources\ResourceAccessResource;
use App\Http\Resources\ResourceAccessCollection;

class ResourceAccessController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:api');
      $this->authorizeResource(ResourceAccess::class, 'resourceAccess');
    }

    public function index()
    {
      return ResourceAccessCollection::collection(ResourceAccess::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  ResourceAccess $resourceAccess
     * @return \Illuminate\Http\ResourceAccessResource
     */
    public function show(ResourceAccess $resourceAccess)
    {
        return new ResourceAccessCollection($resourceAccess);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ResourceAccessRequest  $request
     * @return \Illuminate\Http\ResourceAccessResource
     */
    public function store(ResourceRequest $request, ResourceAccess $resourceAccess)
    {
        $resourceAccess = ResourceAccess::create($request->all());
        return new ResourceAccessResource($resourceAccess);
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\ResourceAccess  $request
     * @return response message
     */
    public function destroy(ResourceAccess $resourceAccess)
    {
        $resourceAccess->delete();
        return response(['message'=>'Resource access deleted']);
    }
}
