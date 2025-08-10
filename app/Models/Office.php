<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Office extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
            'name',
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
    protected $withCount = ['users'];

}