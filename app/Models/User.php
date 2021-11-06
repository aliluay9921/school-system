<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function reports()
    {
        return $this->hasMany(Report::class, 'user_id');
    }
    public function degrees()
    {
        return $this->hasMany(Degree::class, 'user_id');
    }
    public function materials_stages_teachers()
    {
        return $this->hasMany(Material_stage_teacher::class, 'teacher_id');
    }
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function issuers()
    {
        return $this->hasMany(Report::class, 'issuer_id');
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