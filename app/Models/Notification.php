<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];


    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_users', 'notification_id', 'user_id');
    }
    public function issuer()
    {
        return $this->belongsTo(User::class, 'from');
    }
}