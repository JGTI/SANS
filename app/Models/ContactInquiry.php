<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInquiry extends Model
{
    protected $fillable = ['name', 'email', 'company', 'phone', 'service', 'message', 'status', 'consented_at', 'email_sent_at'];

    protected function casts(): array
    {
        return ['consented_at' => 'datetime', 'email_sent_at' => 'datetime'];
    }
}
