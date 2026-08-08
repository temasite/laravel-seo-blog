<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => UserStatus::Active->value,
    ];

    public function activate(): void
    {
        $this->changeStatus(UserStatus::Active);
    }

    public function suspend(): void
    {
        $this->changeStatus(UserStatus::Suspended);
    }

    private function changeStatus(UserStatus $status): void
    {
        $suspendedAt = null;

        if ($status === UserStatus::Suspended) {
            $suspendedAt = $this->status === UserStatus::Suspended
                ? $this->suspended_at ?? now()
                : now();
        }

        $this->forceFill([
            'status' => $status,
            'suspended_at' => $suspendedAt,
        ])->saveOrFail();
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
            'status' => UserStatus::class,
            'last_login_at' => 'datetime',
            'suspended_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
