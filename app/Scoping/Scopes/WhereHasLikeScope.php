<?php

namespace App\Scoping\Scopes;

use App\Scoping\Contracts\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;


class WhereHasLikeScope implements Scope
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
            $builder->where($this->relCol, 'LIKE', '%'.$value.'%');
        });
    }
}