<?php

namespace App\Scoping\Scopes;

// use JsonPath\JsonObject;
use Exception;

use Illuminate\Support\Str;
use Neftaio\Jmes\Facades\Jmes;
use App\Scoping\Contracts\Scope;
use App\Traits\JmesExpressionMakerTrait;
use Illuminate\Database\Eloquent\Builder;

class JsonScope implements Scope
{
    // adjust form input (via url parameters) to jsonObject like filtering methods 
    use JmesExpressionMakerTrait;
    protected $table;
    protected $where;

    public function __construct($table, $where){
        $this->table = $table;
        $this->where = $where;
    }

    // filter json in data column
    public function apply(Builder $builder, $value)
    {
        $ids = [];

        if(is_null($value)) return; // no query parameter value = no filtering
        $expression = $this->queryToExpression($value); // converte $value to Jmes compatible expression

        $path = $this->where;
        $builder->chunk(100, function ($rows) use($expression, $path, &$ids) {
            foreach ($rows as $row) {
                try {
                    if(Jmes::search("{$path}[?{$expression}]", json_decode($row, true)) != null){
                        array_push($ids, $row->id);
                    }
                }    
                catch (Exception $e) {
                    return;
                }
            }
        });
        return $builder->whereIn($this->table.".id", $ids);
    }
} 