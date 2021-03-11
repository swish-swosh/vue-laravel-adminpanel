<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Resource;

use App\Scoping\Scoper;
use App\Scoping\Scopes\DateTimeScope;
use App\Scoping\Scopes\WhereHasScope;
use App\Scoping\Scopes\WhereLikeScope;
use App\Scoping\Scopes\WhereScope;
use App\Scoping\Scopes\DueDateTimeScope;
use App\Scoping\Scopes\WhereHasLikeScope;

use Illuminate\Http\Request;
use App\Http\Requests\ResourceRequest;
use App\Http\Requests\ResourceGetRequest;
use App\Http\Resources\ResourceResource;
use App\Http\Resources\ResourceCollection;

class ResourceController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:api');
      $this->authorizeResource(Resource::class, 'resource');
    }

    public function index(ResourceGetRequest $request)
    {
      return ResourceCollection::collection(                  // return ResourceCollection of Resource models (json)
        Resource::ofGrantedType('can_read')                   // get data for which I'm granted (GrantTrait - rbac ) 
        ->withScopes($this->scopes())                         // use scopes to filter by resource type, date, (using url parameters)
        ->join('users', 'users.id', '=', 'resources.user_id') // using join to get relation (much faster)
        ->select('resources.id', 'users.name', 'users.email', // wanted table & relation columns (non-ambiguous ref.)
        'resources.user_id', 'resources.resource_type_id',
        'resources.resource_type_group_id',
        'resources.data', 'resources.updated_at',
        'resources.created_at', 'resources.is_active')
        ->orderBy($request->orderBy, $request->order)   // asc / desc orderBy on table & relation (i.e. resources.id, users.name )
        ->paginate($request->size)                      // page size (url parameters)
      );
    }

    /**
     * Display the specified resource.
     *
     * @param  Resource $resource
     * @return \Illuminate\Http\ResourceResource
     */
    public function show(Resource $resource)
    {
      return new ResourceResource($resource);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ResourceRequest  $request
     * @return \Illuminate\Http\ResourceResource
     */
    public function store(ResourceRequest $request)
    {
        $resource = Resource::create($request->all());
        return new ResourceResource($resource);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\ResourceRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ResourceRequest $request, $id)
    {
        $resource = Resource::find($id);
        $user = User::find($resource->user_id);

        if($request->has('name')){
            $user->name = $request->name;
            $user->save();
        }

        if($request->has('roles')){
            $user->roles()->sync($request->roles);
        }

        $resource->data = $request->data;
        $resource->save();

        return new ResourceResource($resource);
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Resource  $request
     * @return response message
     */
    public function destroy(Resource $resource)
    {
      $resource->delete();
      return response(['message'=>'Resource deleted']);
    }

     /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Resource  $request
     * @return response message
     */
    public function destroyMany(Resource $resource, Request $request)
    {

      $ids = $request->ids;
      //  dd('destroyManyResources method called');
      // Resource::whereIn('id', $request->ids)->delete();
    } 

    protected function scopes()
    {
      // for resources model:
      return [
        'resourceType' => new WhereHasScope('resourceType', 'name'),            // filter on resourceType relation with name field
        'resourceTypeGroup' => new WhereHasScope('resourceTypeGroup', 'name'),  // filter out log - resource type group
        'dueDateTime' => new DateTimeScope('data->due'),                        // filter on data->due json object
        'nextRunDateTime' => new DateTimeScope('data->nextRun'),                
        'dateTime' => new DateTimeScope('resources.created_at'),                          // filter DATE on column supplied
        'filter' => new WhereLikeScope('data->name'),                           // common filter on data (payload) field
        'resourceUser' => new WhereHasLikeScope('user', 'name'),
        'resourceUserId' => new WhereScope('user_id'),                          // search on user_id column
      ];
    }

    // make extra policies available (with model)
    protected function resourceAbilityMap()
    {
        return array_merge(parent::resourceAbilityMap(), [
            'destroyMany' => 'deleteMany'
        ]);
    }

    // make extra policies available (without model)
    protected function resourceMethodsWithoutModels()
    {
        return array_merge(parent::resourceMethodsWithoutModels(), ['destroyMany']);
    }
}

