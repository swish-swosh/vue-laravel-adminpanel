# vue2 / laravel 8 / adminpanel
Full stack Vue Laravel adminpanel - what I learned 

![What I learned](/images/logos-vue-laravel.jpg?raw=true)

This **work in progress**(!) shows what I learned using Laravel and Vue (with vue-bootstrap components) over several years of experience.

I'll be starting by only handling the index methods (+ viewAny and View in Policies), adding complexity as we dive deeper into the front- and backend functionality, in an attempt to keep everything as simple and manageable as possible. If anything seems confusing, incomprehensible, or subject to change or improvement, please contact me!

This document is intended to give a quick but comprehensive overview of the vue2-laravel-adminpanel inner workings enabling you to build extra features and enhancements.

The admin-panel has several useful functionalities such as a task scheduler, user management, remote server health monitoring with searchable log entries, schedule settings, and chart representations. The user manager includes role-based access control, which can be accessed using RBAC type access control. The Laravel backend uses oauth2 bearer tokens with the Passport package for authentication. If the user chooses to log in via third-party credentials, then the Socialite package is used. This application is by no means a 'hello world' sample!

  * A few gif screen recordings demonstrating login, access control, user profile and log search - analysis.
  
![Screen recordings](/videos/collage1.gif?raw=true)

## Table of Contents

  - [Managing resources](#managing-resources)
    - [FRONT-END](#FRONT-END)
    - [BACK-END](#BACK-END)
  - [Authenticating users](#authenticating-users)
  
### **Managing resources**
Tasks, user management, server health monitors, and system logging use RBAC control and can easily be scaled horizontally by distributing the data over several database tables or even independent databases using different back-end resource controllers. Small implementations can use the default resources and logs tables provided.

#### **FRONT-END**
The Vue2 front-end uses a **VUEX** store with a dedicated store resource module for clear back-end **API** communications.
Each resource-specific front-end component communicates via mapActions and mapGetters with the Veux store. The next example shows a typical bootstrap-vue table update await which is automatically called on mount, filtering, paging, ordering, date-time filtering, etc...
  * Component specific data preparation:
```
      tableProvider(ctx, callback){
        this.isBusy = true
        let params = 
          'resources?resourceType=Tasks' +
          '&dueDateTime=start=' + this.getISODateWithOffset(this.dateTime.start.selected) +
              ',end='+ this.getISODateWithOffset(this.dateTime.end.selected) +
          '&page=' + ctx.currentPage +
          '&size=' + ctx.perPage +
          '&filter=' + ctx.filter +
          '&orderBy=' + ctx.sortBy +
          '&order=' + (ctx.sortDesc ? 'desc' : 'asc')
        this.loadTable(params, callback)
      }
```
  * Async store calling functionality with response feedback
```
      async loadTable(params, callback) {
          let response = await this.retrieveResources(params)
          this.feedback = response.message
          this.table.totalRows = response.meta.total
          this.feedback = response.meta.total == 0 ? 'no data available' : response.message
          this.isBusy = false
          callback(response.data)
      },
```
  * The vuex store uses a dedicated resource module for back-end API communication and persisting data. Token management is handled with the Axios intercepters (including refresh tokens).
```
const actions = {
    async retrieveResources({ state, commit, rootState }, params) {
        let status, message, data, links, meta
        try {
            const response = await axios.get(
                globals.baseUrlBackend + params
            )
            status = response.status
            message = 'Resources loaded'
            links = response.data.links
            meta = response.data.meta
            data = response.data.data
            commit('SET_RESOURCES', data)
        } catch(err) {
            status = err.response.status
            message = err.response.statusText
            data=null
            links = null
            meta = null
        }
        return {
            'status': status,
            'message': message,
            'data': data,
            'links': links,
            'meta': meta
        }
    }
}
```
Front-end data storage uses Vuex store methods.  Cookies or local storage are available. By enabling the encryption module, browser data renders unreadable when checking the store. 

![Persisting data](/images/persisting-data.jpg?raw=true)

Vuex store cookie / encryption settings defined in /store/index.js:
```
const dataState = createPersistedState({
  // only persist these states:
  paths: [
    'auth.accessToken',
    'auth.rememberMe',
    'users.user',
    'components.countries',
    'components.roles',
    'components.resourceTypes'
  ],

// no encryption?, storage object not needed. Comment out for development!
// max item size = 5MB !

// encryption
  storage: {
        getItem: (key) => ls.get(key),
        setItem: (key, value) => ls.set(key, value),
        removeItem: (key) => ls.remove(key),
  },

  /* cookie way, max size of all cookies should not exceed 4096 bytes!
  storage: {
    getItem: key => Cookies.get(key),
    setItem: (key, value) => Cookies.set(key, value, {expires: 30 }),
    removeItem: key => Cookies.remove(key)
  }
  */
})
```
### **BACK-END**

#### Routing

The Laravel 8 backend uses a resource controller incorporating the API interface with full CRUD capabilities (the first part consists of the index method only)

  * Declare the resource route in routes\api.php
```
use App\Http\Controllers\Api\V1\ResourceController;

Route::resources([
    'resources' => ResourceController::class,
]);
```
#### Resource controller

The authentication uses Laravel policies except for the index method (the 'viewAny' policy only checks if a user is logged in) which has an RBAC type control access on each row.

- The class constructor sets the middleware on auth:api instead of the default, and 'AuthorizeResource' connects the resourcePolicy class to the controller.

- As you can see in the example below, the index method uses an 'ofGrantedType' method on the resource model, where RBAC type filtering commences. Only rows of resource types (like 'Tasks') for which a user has roles appointed get returned.

- The 'Scoper' methods filter out data from the supplied table columns or table columns from a relation ( the whereHas methods - see next section for details) or even JSON data nested in table column contents. It's important to notice that only scopes get called which have a URL parameter:

- Calling www.my-backend.com/api/resources?resourceType=UserProfile&dateTime=start=2020-07-09T19:52:31.630Z,end=2021-01-09T00:00:00.000Z will only call the resourceType scope and the dateTime scope (on the created_at column in this example).

- Define orderBy (column) and direction ( asc / desc ).

- Define pagination with page size.

- Configure in App\Http\Controllers\Api\V1\ResourceController.
```
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Scoping\Scoper;
use App\Scoping\Scopes\DateTimeScope;
use App\Scoping\Scopes\WhereHasScope;
use App\Scoping\Scopes\WhereHasLikeScope;
use App\Scoping\Scopes\DueDateTimeScope;
use App\Scoping\Scopes\WhereLikeScope;

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
      return ResourceCollection::collection(            // return ResourceCollection of Resource models (json)
        Resource::ofGrantedType('can_read')             // get data for which I'm granted (GrantTrait - rbac ) 
        ->withScopes($this->scopes())                   // use scopes to filter by resource type, date, (using url parameters)
        ->orderBy($request->orderBy, $request->order)   // asc / desc orderBy (url parameters)
        ->paginate($request->size)                      // page size (url parameters)
      );
    }
    
    protected function scopes()
    {
      return [
        'resourceType' => new WhereHasScope('resourceType', 'name'),            // filter on resourceType relation with name field
        'resourceTypeGroup' => new WhereHasScope('resourceTypeGroup', 'name'),  // filter out log - resource type group
        'dueDateTime' => new DateTimeScope('data->due'),                        // filter on data->due json object
        'nextRunDateTime' => new DateTimeScope('data->nextRun'),                
        'dateTime' => new DateTimeScope('created_at'),                          // filter DATE on column supplied
        'filter' => new WhereLikeScope('data->name'),                           // common filter on data (payload) field
        'resourceUser' => new WhereHasLikeScope('user', 'name'), 
      ];
    }
 }

```
#### Validation: form request rules
  * Although the front-end uses input validation on forms and other components, this type of input can never be fully trusted. With third-party tools such as Postman, bypassing front-end protection could easily be accomplished.
Server-side request validation is your first row of defense:

First, rules determine which data is necessary and in which form they need to be delivered. (integer, date, etc. ).

Second, only allowed parameters are passed and get set to default values when omitted.
  
```
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|integer',
            'resource_type_id' => 'required|integer'
        ];
    }

    /**
     * Sanitize route parameters.
     *
     * @return array
     */
    public function all($keys = null) 
    {
       $params = parent::all($keys);
       return [
           'order' => isset($params['order']) ? $params['order'] : 'asc',         // 'desc' | 'asc' order direction;
           'orderBy' => isset($params['orderBy']) ? $params['orderBy'] : 'id',    // order by column
           'size' => isset($params['size']) ? (integer) $params['size'] : 15,     // items per page
           'filterType' => isset($params['filterType']) ? $params['filterType'] : '',
       ];
    }
```
#### Scoping url parameters

  * The 'scopes' method in the controller defines the available filter methods. The array keys correspond to the URL parameters where the classes get instantiated by the column values or relations supplied to get the search results. By using the scoper, filtering gets easy. Declaring a 'scopes' entry in the return array by instantiating a class containing an Eloquent builder method/collection object makes filtering a snap.
  * Selecting scopes is handled by the scoper function which limits the scopes used, based on the supplied URL parameters.
  * Only scopes which are an instance of the scoping contract (interface) will return. Just add an extra scope to perform additional filtering!
  * Scoper in App\Scoping:
```
namespace App\Scoping;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;

class Scoper
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder, array $scopes)
    {
        foreach ($this->limitScopes($scopes) as $key => $scope) {
            if (!$scope instanceof Scope) {
                continue;
            }
            $scope->apply($builder, $this->request->get($key));
        }
        return $builder;
    }

    protected function limitScopes(array $scopes)
    {
        return Arr::only($scopes, array_keys($this->request->all()));
    }
}
```
  * Scoper interface:
```
 namespace App\Scoping\Contracts;

 use Illuminate\Database\Eloquent\Builder;

 interface Scope
 {
     public function apply(Builder $builder, $value);
 }
```
  * whereHas (relation) scope example:
```
namespace App\Scoping\Scopes;

use App\Scoping\Contracts\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class WhereHasScope implements Scope
{
    protected $col, $relCol;

    public function __construct ($col, $relCol) {
        $this->col = $col;
        $this->relCol = $relCol;
    }

    /**
     * checks column $col relation where $relCol = $value
     */
    public function apply(Builder $builder, $value)
    {
        return $builder->whereHas($this->col, function ($builder) use ($value) {
            $builder->where($this->relCol, $value);
        });
    }
}
```
  * DateTimeScope example:
```
namespace App\Scoping\Scopes;

use App\Traits\UrlTrait;
use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;

class DateTimeScope implements Scope
{
    use UrlTrait;   // convert url parameters to array helper
    protected $col;

    public function __construct($col)
    {
        // example: set column to query on construct ( like, 'logs.created_at')
        $this->col=$col;
    }

    public function apply(Builder $builder, $value)
    {
        // example: url param like, filterDateTime=start=2020-12-09%2013:44,end=2020-12-09%2013:55
        $query = [];
        $wheres = $this->queryToArray($value);

        if(isset($wheres['start'])) array_push($query, [$this->col, '>=', $wheres['start']]);
        if(isset($wheres['end'])) array_push($query, [$this->col, '<=', $wheres['end']]);

        return $builder->where($query)->get();
    }
}
```
  * UrlTrait example to decompose start and end 'date' parameters:
```
namespace App\Traits;

use App\Scoping\Scoper;
use Illuminate\Database\Eloquent\Builder;

trait UrlTrait
{
    /**
    * Parse out url query string into an associative array
    *
    * $qry can be any valid url or just the query string portion.
    * Will return false if no valid querystring found
    * multiple variables can be comma seperated like ?q=a=1,b=2  for array:2 [ [a] => 1 [b] => 2 ]
    *
    * @param $qry String
    * @return Array
    */
    public function queryToArray($qry)
    {
        $result = array();

        //string must contain at least one = and cannot be in first position
        if(strpos($qry,'=')) {

            if(strpos($qry,'?')!==false) {
            $q = parse_url($qry);
            $qry = $q['query'];
            }
        }else {
                return false;
        }

        foreach (explode('&', $qry) as $couple) {
            foreach (explode(',', $qry) as $sub) {
                list ($key, $val) = explode('=', $sub);
                $result[$key] = $val;
            }
        }
        return empty($result) ? false : $result;
    }
}
```
#### Policies

  * Policies are configured in App\Providers\AuthServiceProvider where policy mapping is done for each policy:
```
namespace App\Providers;
use App\Models\Resource;
use App\Policies\ResourcePolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Resource::class => ResourcePolicy::class,
    ];
    // ...
}
```
  * Define the policy in App\Policies\ResourcePolicy
- As the Policy methods only return True or False if access is allowed on the requested data, and the viewAny method returns all rows for a complete collection it takes a next level to handle individual rows. The 'viewAny' method is only used for Auth:check() which returns tre/false.
- The 'view' method (the controller 'show' method connects here) is able to check access through the GrantTrait method ( only one row )
```
namespace App\Policies;

use App\Models\User;
use App\Models\Resource;

use App\Traits\GrantTrait;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResourcePolicy
{
    use HandlesAuthorization, GrantTrait;
    
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        // need to be logged in, access can not be checked here (multiple types exist)
        return Auth::check();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Resource  $resource
     * @return mixed
     */
    public function view(User $user, Resource $resource)
    {
       // check if I'm granted for 'can_read', $myRoles, resourceType
       return $this->checkGranted('can_read', $user->pluckRoles(['name']), $resource->resourceType->name );
    }
    / ...
}
```
#### Resource database tables

  * Resource database tables share the same structure. By using common table column names you get scalability and flexibility. When a table contains too much data, just split and redistributed by type, date, group type, user or when active or not. Each table row can be related to one or more roles (via the resource type) to determine access or filtering by resource group type and user. When a user is assigned one or more roles, the role is assigned. If an 'update' privilege was assigned, regardless of other roles, it will be used and be available. ( OR implementation )
  * The data field can hold **any** JSON data structure. This field is used as a 'payload' which can be crafted in any form the front-end desires. ( like input forms, roles assigned, log data, tasks scheduled, ... )
  
| id | resource_type_id   | resource_group_type_id | user_id          | is_active | data        |
| -- |:------------------:|:----------------------:|:----------------:|:---------:|------------:|
|  1 | name  relation*    |  name relation*        | name relation*   | 1 / 0     | Json object |

\*one to one relationship

  * Database designer resource relationship output
![Database designer with resources parts](/images/db.png?raw=true)

  * Setting role access rights:
![Setting role access rights](/videos/adminpanel-access.gif?raw=true)

### **Authenticating users**

The back-end provides Oauth2 authentication for local authentication while Socialite gives access to third-party providers like Gmail, Facebook, Twitter, etc.., which avoids sharing user passwords to the app. A bearer token enables access, and a refresh token helps to re-establish the connection when the bearer authorization token expires. The handling processes are kept central in dedicated interceptor using request and response methods.

The Vue front-end provides all modal forms for authentication methods, registering, confirmation, and password updates and socialite connection buttons. The backend uses notifications to send the confirmation emails using easily adjustable templates:

![Database designer with resources parts](/images/login-options.jpg?raw=true)

