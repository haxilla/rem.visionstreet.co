<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'adminEmail',
        'password',
        'adminFirst',
        'adminLast',
        'adminHandle',
        'adminAvatar',
        'authLevel',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->adminEmail;
    }

    public function routeNotificationForMail(): string
    {
        return $this->adminEmail;
    }
}