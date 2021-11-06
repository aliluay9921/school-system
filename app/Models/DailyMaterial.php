<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyMaterial extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'class_id');
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}