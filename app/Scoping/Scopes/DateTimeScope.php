<?php

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
