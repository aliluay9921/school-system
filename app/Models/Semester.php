<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Semester extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];

    public function degrees()
    {
        return $this->hasMany(Degree::class, 'semester_id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'class_id');
    }
}