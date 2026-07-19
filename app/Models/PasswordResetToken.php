<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $fillable = ['email', 'token', 'otp', 'expires_at', 'attempts'];

    protected function casts()
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && $this->attempts < 5;
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }
}
