<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'class_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'class_id');
    }
    public function degrees()
    {
        return $this->hasMany(Degree::class, 'class_id');
    }
    public function semester()
    {
        return $this->hasMany(Semester::class, 'class_id');
    }
    public function materials_stages_teachers()
    {
        return $this->hasMany(Material_stage_teacher::class, 'class_id');
    }
    public function exams()
    {
        return $this->hasMany(Exam::class, 'class_id');
    }
    public function daily_materials()
    {
        return $this->hasMany(DailyMaterial::class, 'class_id');
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}