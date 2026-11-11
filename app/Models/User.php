<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable([
    'name',
    'first_name',
    'middle_name',
    'last_name',
    'email',
    'password',
    'phone_number',
    'student_no',
    'department',
    'course',
    'year_level',
    'profile_photo_path',
    'role',
    'status',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isVisitor(): bool
    {
        return $this->role === 'visitor';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
