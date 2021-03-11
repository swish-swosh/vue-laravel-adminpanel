<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $fillable= [
        'name',
        'description'
    ];
    
    public function users()
    {
        return $this->hasMany(User::class, 'id', 'organisation_id');
    }
}