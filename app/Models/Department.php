<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $guarded = [];

    function users()
    {
        return $this->hasMany(User::class);
    }

    function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }
}
