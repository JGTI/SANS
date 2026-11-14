<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VisionEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'astronaut_id',
        'evaluator_id',
        'evaluated_at',
        'mission_day',
        'flight_phase',
        'eye',
        'sphere',
        'cylinder',
        'axis',
        'spherical_equivalent',
        'hyperopic_shift',
        'snellen_numerator',
        'snellen_denominator',
        'logmar',
        'with_correction',
        'intraocular_pressure',
        'reported_symptoms',
        'clinical_notes',
        'is_simulated',
    ];

    protected $casts = [
        'evaluated_at' => 'datetime',
        'mission_day' => 'integer',
        'sphere' => 'decimal:2',
        'cylinder' => 'decimal:2',
        'axis' => 'integer',
        'spherical_equivalent' => 'decimal:2',
        'hyperopic_shift' => 'decimal:2',
        'snellen_numerator' => 'integer',
        'snellen_denominator' => 'integer',
        'logmar' => 'decimal:3',
        'with_correction' => 'boolean',
        'intraocular_pressure' => 'decimal:1',
        'reported_symptoms' => 'array',
        'is_simulated' => 'boolean',
    ];

    public function astronaut(): BelongsTo
    {
        return $this->belongsTo(AstronautProfile::class, 'astronaut_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function sansObservation(): HasOne
    {
        return $this->hasOne(SansObservation::class, 'vision_evaluation_id');
    }

    /**
     * Accesor para formato legible de Snellen (ej: 20/40)
     */
    public function getSnellenFractionAttribute(): string
    {
        return "{$this->snellen_numerator}/{$this->snellen_denominator}";
    }

    /**
     * Accesor para formato clínico de refracción (ej: +1.25 -0.50 x 180°)
     */
    public function getRefractionFormulaAttribute(): string
    {
        $cyl = $this->cylinder >= 0 ? "+{$this->cylinder}" : "{$this->cylinder}";
        $sph = $this->sphere >= 0 ? "+{$this->sphere}" : "{$this->sphere}";
        return "{$sph} {$cyl} x {$this->axis}°";
    }
}
