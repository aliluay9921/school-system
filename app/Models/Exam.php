<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, Uuids, SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at'];


    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
    public function stage()
    {
        return $this->belongsTo(Stage::class, 'class_id');
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
