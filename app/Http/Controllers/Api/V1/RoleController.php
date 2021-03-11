<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Requests\RoleRequest;

use App\Http\Resources\RoleResource;
use App\Http\Resources\RoleCollection;

class RoleController extends Controller
{

    public function __construct()
    {
        // auth:api middleware authentication required
        $this->middleware('auth:api');

        // use policies to check roles or owner resource authorization
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Role $role)
    {
        return RoleCollection::collection(Role::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return new RoleCollection($role);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request, Role $role)
    {
        $role = Role::create($request->all());
        return new RoleCollection($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        $role->update($request->all());
        return new RoleCollection($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return response(['message'=>'Role deleted']);
    }
}
