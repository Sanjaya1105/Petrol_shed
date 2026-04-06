<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'username', 'role_id', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * URL for the role-specific home page after login (role_number: 1 Dev, 2 Admin, 3 Data-entry).
     */
    public function dashboardUrl(): string
    {
        $this->loadMissing('role');

        if ($this->role === null) {
            return route('s_login');
        }

        return match ((int) $this->role->role_number) {
            1 => route('dev.show', ['page' => 'home']),
            2 => route('admin.show', ['page' => 'home']),
            3 => route('data-entry.show', ['page' => 'home']),
            default => route('s_login'),
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
