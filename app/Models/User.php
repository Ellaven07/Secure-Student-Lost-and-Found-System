<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable([
    'name',
    'email',
    'password',
    'student_no',
    'department',
    'course',
    'year_level',
    'profile_photo_path',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Check whether the account is an administrator
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Check whether the account is a student
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    // Check whether the account is a viewer
    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    // Check whether the account is active
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
?>