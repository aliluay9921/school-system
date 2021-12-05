<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material_stage_teacher extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];

    protected $with = ['stage', 'material', 'user'];
    public function user()
    {
        return $this->belongsTo(User::class, 'teacher_id')->withTrashed();
    }
    public function stage()
    {
        return $this->belongsTo(Stage::class, 'class_id');
    }
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}