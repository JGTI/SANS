<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SansObservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'astronaut_id',
        'vision_evaluation_id',
        'observed_at',
        'mission_day',
        'eye',
        'frisen_scale_grade',
        'choroidal_folds',
        'retinal_folds',
        'posterior_globe_flattening',
        'cotton_wool_spots',
        'rnfl_thickness_microns',
        'sans_risk_tier',
        'risk_factors',
        'clinical_action_recommended',
        'is_simulated',
    ];

    protected $casts = [
        'observed_at' => 'datetime',
        'mission_day' => 'integer',
        'frisen_scale_grade' => 'integer',
        'choroidal_folds' => 'boolean',
        'retinal_folds' => 'boolean',
        'posterior_globe_flattening' => 'boolean',
        'cotton_wool_spots' => 'boolean',
        'rnfl_thickness_microns' => 'integer',
        'risk_factors' => 'array',
        'is_simulated' => 'boolean',
    ];

    public function astronaut(): BelongsTo
    {
        return $this->belongsTo(AstronautProfile::class, 'astronaut_id');
    }

    public function visionEvaluation(): BelongsTo
    {
        return $this->belongsTo(VisionEvaluation::class, 'vision_evaluation_id');
    }

    public function getRiskBadgeClassAttribute(): string
    {
        return match ($this->sans_risk_tier) {
            'severe' => 'danger',
            'high' => 'danger',
            'moderate' => 'warning',
            default => 'success',
        };
    }
}
