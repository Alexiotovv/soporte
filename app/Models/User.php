<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;
use App\Models\UserLocation;
use App\Models\UserLocationLog;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    public const ROLE_REGULAR = 0;
    public const ROLE_ADMIN = 1;
    public const ROLE_SUPPORT = 2;
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'phone',
        'office_id',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'integer',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function location()
    {
        return $this->hasOne(UserLocation::class);
    }

    public function locationLogs()
    {
        return $this->hasMany(UserLocationLog::class);
    }

    public static function roleOptions(): array
    {
        return [
            self::ROLE_REGULAR => 'Regular User',
            self::ROLE_SUPPORT => 'Support Team',
            self::ROLE_ADMIN => 'Administrator',
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roleOptions()[$this->is_admin] ?? 'Unknown';
    }

    public function isAdminUser(): bool
    {
        return (int) $this->is_admin === self::ROLE_ADMIN;
    }

    public function isSupportUser(): bool
    {
        return (int) $this->is_admin === self::ROLE_SUPPORT;
    }
}