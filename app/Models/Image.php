<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory, Uuids;
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}