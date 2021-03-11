<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\ResourceType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceTypeRequest;
use App\Http\Resources\ResourceTypeResource;
use App\Http\Resources\ResourceTypeCollection;

class ResourceTypeController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:api');

      // use resource authorization
      $this->authorizeResource(ResourceType::class, 'resourceType');
    }

    public function index()
    {
      return ResourceTypeCollection::collection(ResourceType::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  ResourceType $resourceType
     * @return \Illuminate\Http\ResourceTypeCollection
     */
    public function show(ResourceType $resourceType)
    {
        return new ResourceTypeCollection($resourceType);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ResourceTypeRequest  $request
     * @return \Illuminate\Http\ResourceTypeResource
     */
    public function store(ResourceTypeRequest $request, ResourceType $resourceType)
    {
        $resourceType = ResourceType::create($request->all());
        return new ResourceTypeResource($resourceType);
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\ResourceType  $request
     * @return response message
     */
    public function destroy(ResourceType $resourceType)
    {
        $resourceType->delete();
        return response(['message'=>'Resource type deleted']);
    }
}
