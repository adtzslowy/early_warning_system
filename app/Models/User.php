<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "name",
        "email",
        "google_id",
        "avatar",
        "password",
        "preferences",
        "foto_profil",
        "telegram_chat_id",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ["password", "remember_token"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
            "preferences" => "array",
        ];
    }

    /**
     * URL publik foto profil (via storage link), atau null bila belum ada.
     */
    public function fotoUrl(): ?string
    {
        return $this->foto_profil
            ? asset("storage/" . $this->foto_profil)
            : null;
    }

    /**
     * Inisial nama untuk avatar fallback (maks 2 huruf).
     */
    public function initials(): string
    {
        return \Illuminate\Support\Str::of($this->name)
            ->explode(" ")
            ->map(fn($w) => \Illuminate\Support\Str::substr($w, 0, 1))
            ->take(2)
            ->implode("");
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class)->withTimestamps();
    }
}
