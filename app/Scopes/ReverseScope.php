<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ReverseScope implements Scope
{
    protected $fillable = [
        'body'
    ];

    public function apply(Builder $builder, Model $model)
    {
        $builder->orderBy('id', 'desc');
    } 
}
