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

    public function getAvatarUrlAttribute(): string
    {
        return self::avatarForCaseOrCode($this->code ?? 'ASTRO-01');
    }

    public static function avatarForCaseOrCode(?string $identifier): string
    {
        $file = match (strtoupper((string) $identifier)) {
            'ASTRO-01', 'CASE-PROG-02' => 'astro-01.jpg',
            'ASTRO-02', 'CASE-STABLE-01' => 'astro-02.jpg',
            'ASTRO-03', 'CASE-NOSCAN-03A' => 'astro-03.jpg',
            'ASTRO-04', 'CASE-ASO-05' => 'astro-04.jpg',
            'ASTRO-05', 'CASE-NORECENT-03B' => 'astro-05.jpg',
            'ASTRO-06', 'CASE-TILT-04' => 'astro-06.jpg',
            default => 'astro-01.jpg',
        };

        return asset('images/astronauts/' . $file) . '?v=color2';
    }

    public static function crewMetaForCase(?string $caseId): array
    {
        return match (strtoupper((string) $caseId)) {
            'CASE-STABLE-01' => [
                'code' => 'ASTRO-02',
                'name' => 'Dr. Sara Lindqvist',
                'role' => 'Science Officer · ISS Exp 71',
                'avatar' => asset('images/astronauts/astro-02.jpg') . '?v=color2',
            ],
            'CASE-NOSCAN-03A' => [
                'code' => 'ASTRO-03',
                'name' => 'Kenji Sato',
                'role' => 'Flight Engineer · ISS Exp 72',
                'avatar' => asset('images/astronauts/astro-03.jpg') . '?v=color2',
            ],
            'CASE-ASO-05' => [
                'code' => 'ASTRO-04',
                'name' => 'Maj. Elena Rostova',
                'role' => 'Orbital Specialist · Mixed Asymmetry',
                'avatar' => asset('images/astronauts/astro-04.jpg') . '?v=color2',
            ],
            'CASE-NORECENT-03B' => [
                'code' => 'ASTRO-05',
                'name' => 'Dr. Marcus Thorne',
                'role' => 'Payload Specialist · Delayed Window',
                'avatar' => asset('images/astronauts/astro-05.jpg') . '?v=color2',
            ],
            'CASE-TILT-04' => [
                'code' => 'ASTRO-06',
                'name' => 'Capt. Mateo Vargas',
                'role' => '-6° HDT Analog & Orbital Subject',
                'avatar' => asset('images/astronauts/astro-06.jpg') . '?v=color2',
            ],
            default => [
                'code' => 'ASTRO-01',
                'name' => 'CDR Alex Mercer',
                'role' => 'Mission Commander · Artemis III',
                'avatar' => asset('images/astronauts/astro-01.jpg') . '?v=color2',
            ],
        };
    }
}
