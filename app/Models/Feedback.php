<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use HasFactory, Uuids, SoftDeletes;
    protected $guarded = [];
    protected $table = 'feedbacks';
    protected $dates = ['deleted_at'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
