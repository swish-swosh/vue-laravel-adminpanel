<?php
namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\ResourceTypeGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceTypeRequest;
use App\Http\Resources\ResourceTypeGroupCollection;

class ResourceTypeGroupController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:api');

      // use resource authorization
      $this->authorizeResource(ResourceTypeGroup::class, 'ResourceTypeGroup');
    }

    public function index()
    {
      return ResourceTypeGroupCollection::collection(ResourceTypeGroup::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  ResourceTypeGroup $resourceTypeGroup
     * @return \Illuminate\Http\ResourceTypeGroupCollection
     */
    public function show(ResourceTypeGroup $resourceTypeGroup)
    {
        return new ResourceTypeGroupCollection($resourceTypeGroup);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ResourceTypeRequest  $request
     * @return \Illuminate\Http\ResourceTypeGroupResource
     */
    public function store(ResourceTypeRequest $request, ResourceTypeGroup $resourceTypeGroup)
    {
        $resourceTypeGroup = ResourceTypeGroup::create($request->all());
        return new ResourceTypeGroupCollection($resourceTypeGroup);
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\ResourceTypeGroup  $request
     * @return response message
     */
    public function destroy(ResourceTypeGroup $resourceTypeGroup)
    {
        $resourceTypeGroup->delete();
        return response(['message'=>'Resource type group deleted']);
    }
}
