<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyMaterial extends Model
{
    use HasFactory, Uuids, SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'materials' => 'array'
    ];
    protected $dates = ['deleted_at'];

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'class_id');
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
