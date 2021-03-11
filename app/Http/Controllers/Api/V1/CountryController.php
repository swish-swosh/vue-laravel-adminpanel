<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\CountryCollection;

use App\Models\Country;

class CountryController extends Controller
{
    public function index(Country $country)
    {
      return CountryCollection::collection(Country::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  Country $country
     * @return \Illuminate\Http\Response
     */
    public function show(Country $country)
    {
        return new CountryCollection($country);
    }

}
