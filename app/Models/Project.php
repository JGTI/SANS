<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['invitation_token', 'token_hash'];

    protected function casts(): array
    {
        return ['invitation_token' => 'encrypted', 'answers' => 'array', 'link_enabled' => 'boolean',
            'expires_at' => 'datetime', 'draft_saved_at' => 'datetime', 'submitted_at' => 'datetime'];
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProjectAttachment::class);
    }

    public function rotateToken(): void
    {
        $this->invitation_token = Str::random(64);
        $this->token_hash = hash('sha256', $this->invitation_token);
    }

    public function invitationUrl(): string
    {
        return route('onboarding.show', ['token' => $this->invitation_token]);
    }
}
