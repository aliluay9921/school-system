<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];
    protected $appends = ["CountUser"];

    public function users()
    {
        return $this->hasMany(User::class, 'school_id');
    }
    // countUser
    public function getCountUserAttribute()
    {
        $school = School::find($this->id);
        return  $school->users()->where("user_type", 3)->count();
    }
}