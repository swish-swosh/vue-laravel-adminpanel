<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\Organisation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\OrganisationCollection;

class OrganisationController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:api');

      // use resource authorization
      $this->authorizeResource(Organisation::class, 'organisation');
    }

    public function index(Organisation $organisation)
    {
      return OrganisationCollection::collection(Organisation::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  Organisation $organisation
     * @return \Illuminate\Http\Response
     */
    public function show(Organisation $organisation)
    {
      return new OrganisationCollection($organisation);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\OrganisationRequest $organisation
     * @return \Illuminate\Http\Response
     */
    public function store(Request $organisationRequest)
    {
        dd('store todo!');
    }

}
