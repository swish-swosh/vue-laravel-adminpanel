<?php
namespace App\Traits;
use Illuminate\Support\Str;

trait JmesExpressionMakerTrait {

    /**
     * convert human readable filter query string to Jmes compatible expression for json filtering
     *
     */
    public function queryToExpression($expression){

        // 1. adjust human to machine comparison methods + remove spaces
        $expression=preg_replace('/((?<![<>])=)/', '$1=', $expression);
        $expression=str_replace(array(" or ", " and ", " not ", " "), 
                array("||", "&&", "!=", ""), $expression);

        // 2. add backticks around query parameters 
        // front:
        $expression=preg_replace('/(==|<=|>=|!=)/', '$1`', $expression);
        $expression=preg_replace('/([<>](?!=))/', '$1`', $expression);
        // end:
        $expression=preg_replace('/([a-zA-Z0-9]+$|`\d*\.?\d*$)/', '$1`', $expression);

        return $expression;
    }

}