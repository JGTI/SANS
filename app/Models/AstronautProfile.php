<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AstronautProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'name',
        'agency',
        'mission_name',
        'role',
        'launch_date',
        'return_date',
        'days_in_microgravity',
        'status',
        'baseline_se_od',
        'baseline_se_os',
        'baseline_logmar_od',
        'baseline_logmar_os',
        'medical_notes',
        'is_simulated',
    ];

    protected $casts = [
        'launch_date' => 'date',
        'return_date' => 'date',
        'days_in_microgravity' => 'integer',
        'baseline_se_od' => 'decimal:2',
        'baseline_se_os' => 'decimal:2',
        'baseline_logmar_od' => 'decimal:3',
        'baseline_logmar_os' => 'decimal:3',
        'is_simulated' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(VisionEvaluation::class, 'astronaut_id')->orderBy('mission_day');
    }

    public function sansObservations(): HasMany
    {
        return $this->hasMany(SansObservation::class, 'astronaut_id')->orderBy('mission_day');
    }

    public function latestEvaluation()
    {
        return $this->hasOne(VisionEvaluation::class, 'astronaut_id')->latestOfMany('evaluated_at');
    }

    public function latestSansObservation()
    {
        return $this->hasOne(SansObservation::class, 'astronaut_id')->latestOfMany('observed_at');
    }
}
