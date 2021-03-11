<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\Log;
use App\Scoping\Scoper;

use Illuminate\Http\Request;

use App\Http\Requests\LogRequest;
use App\Scoping\Scopes\JsonScope;

use App\Http\Resources\LogResource;
use App\Http\Controllers\Controller;

use App\Http\Requests\LogGetRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\LogCollection;
use App\Scoping\Scopes\DateTimeScope;
use App\Scoping\Scopes\WhereHasScope;

class LogController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:api');
      $this->authorizeResource(Log::class, 'log');
    }

    public function index(LogGetRequest $request, Log $log)
    {
      // details in resourceController
      return LogCollection::collection(
        Log::ofGrantedType('can_read')
          ->withScopes($this->scopes()) 
          ->orderBy($request->orderBy, $request->order)
          ->paginate($request->size)
      );
    }

    /**
     * Display the specified resource.
     *
     * @param  Log $log
     * @return \Illuminate\Http\LogResource
     */
    public function show(Log $log)
    {
      return new LogResource($log);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Log  $request
     * @return \Illuminate\Http\LogResource
     */
    public function store(LogRequest $request, Log $log)
    {
      $log = Log::create($request->all());
      return new LogResource($log);
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Log  $request
     * @return \Illuminate\Http\LogResource
     */
    public function destroy(Log $log)
    {
      $log->delete();
      return response(['message'=>'Log deleted']);
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Resource  $request
     * @return response message
     */
    public function destroyMany(Log $log, Request $request)
    {
      $ids = $request->ids;
      // Resource::whereIn('id', $request->ids)->delete();
    } 

    protected function scopes()
    {
      return [
        'resourceType' => new WhereHasScope('resourceType', 'name'),            // filter out log - resource type
        'resourceTypeGroup' => new WhereHasScope('resourceTypeGroup', 'name'),  // filter out log - resource type group
        'dateTime' => new DateTimeScope('created_at'),                          // filter DATE column supplied
        'filterJson' => new jsonScope('logs', 'data'),                          // filter JSON 'data' column name in logs table
      ];
    }

    protected function resourceAbilityMap()
    {
        return array_merge(parent::resourceAbilityMap(), [
            'destroyMany' => 'deleteMany'
        ]);
    }

    protected function resourceMethodsWithoutModels()
    {
        return array_merge(parent::resourceMethodsWithoutModels(), ['destroyMany']);
    }
}
