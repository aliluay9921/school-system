<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
    public function stage()
    {
        return $this->belongsTo(Stage::class, 'class_id');
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
