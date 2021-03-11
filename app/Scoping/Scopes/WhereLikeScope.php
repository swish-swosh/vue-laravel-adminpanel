<?php

namespace App\Scoping\Scopes;
use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;

class WhereLikeScope implements Scope
{
    protected $col;

    public function __construct ($col) {
        $this->col = $col;
    }

    /**
     * checks column $col relation where $relCol = $value
     */
    public function apply(Builder $builder, $value)
    {
        return $builder->where($this->col, 'LIKE', '%'.$value.'%');
    }
}