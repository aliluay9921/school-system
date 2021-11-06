<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material_stage_teacher extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    public function stage()
    {
        return $this->belongsTo(Stage::class, 'class_id');
    }
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}