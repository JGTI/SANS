<?php

namespace App\Services;

class OphthalmologyCalculator
{
    /**
     * Calcula el Equivalente Esférico (Spherical Equivalent).
     * Fórmula clínica estándar: SE = Esfera + (Cilindro / 2)
     */
    public function calculateSphericalEquivalent(float $sphere, float $cylinder): float
    {
        return round($sphere + ($cylinder / 2.0), 2);
    }

    /**
     * Convierte agudeza visual de Snellen a notación científica LogMAR.
     * Fórmula: LogMAR = log10(Denominador / Numerador)
     * Ejemplos:
     *  20/20  -> log10(20/20)  = 0.000
     *  20/40  -> log10(40/20)  = 0.301
     *  20/200 -> log10(200/20) = 1.000
     */
    public function snellenToLogmar(int $numerator = 20, int $denominator = 20): float
    {
        if ($numerator <= 0 || $denominator <= 0) {
            return 0.000;
        }

        return round(log10($denominator / $numerator), 3);
    }

    /**
     * Convierte fracción en cadena (e.g. '20/40') a LogMAR.
     */
    public function convertSnellenToLogmar(string $snellen): float
    {
        $parts = explode('/', trim($snellen));
        $numerator = isset($parts[0]) && is_numeric($parts[0]) ? (int) $parts[0] : 20;
        $denominator = isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : 20;

        return $this->snellenToLogmar($numerator, $denominator);
    }

    /**
     * Calcula el Desplazamiento Hipermetrópico (Hyperopic Shift) relativo a la línea base.
     * Un valor positivo indica que el astronauta se está volviendo más hipermétrope en microgravedad.
     */
    public function calculateHyperopicShift(float $currentSE, ?float $baselineSE): float
    {
        if ($baselineSE === null) {
            return 0.00;
        }

        return round($currentSE - $baselineSE, 2);
    }

    /**
     * Evalúa el nivel de riesgo y compatibilidad con SANS mediante un modelo explicable.
     * 
     * @param array $data {
     *   frisen_grade: int (0-5),
     *   globe_flattening: bool,
     *   choroidal_folds: bool,
     *   hyperopic_shift: float,
     *   logmar_shift: float,
     *   rnfl_thickness: int|null (µm)
     * }
     */
    public function evaluateSansRisk(array $data): array
    {
        $score = 0;
        $factors = [];

        $frisen = (int) ($data['frisen_grade'] ?? 0);
        if ($frisen >= 3) {
            $score += 45;
            $factors[] = "Edema de papila severo (Grado Frisen {$frisen}/5)";
        } elseif ($frisen >= 1) {
            $score += 25;
            $factors[] = "Edema del disco óptico confirmado (Grado Frisen {$frisen}/5)";
        }

        if (! empty($data['globe_flattening'])) {
            $score += 20;
            $factors[] = "Aplanamiento posterior del globo ocular detectado por ecografía/OCT";
        }

        if (! empty($data['choroidal_folds'])) {
            $score += 20;
            $factors[] = "Presencia de pliegues coroideos / estrías retinianas";
        }

        if (! empty($data['cotton_wool_spots'])) {
            $score += 15;
            $factors[] = "Manchas algodonosas detectadas en polo posterior";
        }

        $hyperopicShift = (float) ($data['hyperopic_shift'] ?? 0);
        if ($hyperopicShift >= 1.00) {
            $score += 25;
            $factors[] = "Desplazamiento hipermetrópico marcado (+{$hyperopicShift} D vs línea base)";
        } elseif ($hyperopicShift >= 0.50) {
            $score += 15;
            $factors[] = "Desplazamiento hipermetrópico moderado (+{$hyperopicShift} D vs línea base)";
        }

        $rnfl = $data['rnfl_thickness'] ?? null;
        if ($rnfl !== null && $rnfl >= 120) {
            $score += 15;
            $factors[] = "Engrosamiento de la capa de fibras nerviosas retinianas (RNFL: {$rnfl} µm)";
        }

        // Determinar estrato de riesgo y recomendación médica
        if ($score >= 60) {
            $tier = 'severe';
            $recommendation = 'Notificación inmediata a Flight Surgeon. Protocolo de imagen OCT de alta prioridad y restricción de tareas EVA críticas.';
        } elseif ($score >= 40) {
            $tier = 'high';
            $recommendation = 'Seguimiento oftalmológico semanal en órbita. Teleconferencia clínica privada con médico de vuelo y ajuste de gafas ajustables de lectura.';
        } elseif ($score >= 20) {
            $tier = 'moderate';
            $recommendation = 'Monitoreo quincenal de agudeza visual y fondo de ojo. Registro continuo de síntomas de astenopia.';
        } else {
            $tier = 'low';
            $recommendation = 'Valores dentro de tolerancia operacional en microgravedad. Continuar cronograma estándar de revisiones cada 30 días.';
        }

        return [
            'tier' => $tier,
            'score' => min(100, $score),
            'factors' => $factors,
            'recommendation' => $recommendation,
        ];
    }
}
