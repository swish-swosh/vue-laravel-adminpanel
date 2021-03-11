<?php

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