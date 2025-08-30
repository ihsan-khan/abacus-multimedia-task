<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'last_activity_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getOnlineDurationAttribute()
    {
        if (!$this->last_activity_at || !$this->login_at) {
            return 0;
        }

        return $this->last_activity_at->diffInSeconds($this->login_at);
    }

    public function getLoginDurationAttribute()
    {
        if (!$this->login_at) {
            return 0;
        }

        if ($this->logout_at) {
            return $this->logout_at->diffInSeconds($this->login_at);
        }

        return now()->diffInSeconds($this->login_at);
    }
}
