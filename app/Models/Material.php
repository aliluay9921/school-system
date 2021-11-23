<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, Uuids, SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at'];


    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
    public function reports()
    {
        return $this->hasMany(Report::class, 'material_id');
    }
    public function degrees()
    {
        return $this->hasMany(Degree::class, 'material_id');
    }
    public function materials_stages_teachers()
    {
        return $this->hasMany(Material_stage_teacher::class, 'material_id');
    }
    public function exams()
    {
        return $this->hasMany(Exam::class, 'material_id');
    }
}
