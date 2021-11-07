<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issuer_id');
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

    public function images()
    {
        return $this->hasMany(Image::class, 'report_id');
    }
}