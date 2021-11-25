<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, Uuids, SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at'];
    protected $with = ['user', 'parent', "parent.user", "report", "report.issuer"];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }
    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }
    public function parent()
    {
        return $this->belongsTo(Comment::class, "parent_id", "id");
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'target_id');
    }
}