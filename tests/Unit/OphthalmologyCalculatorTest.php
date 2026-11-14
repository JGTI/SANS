<?php

namespace Tests\Unit;

use App\Services\OphthalmologyCalculator;
use PHPUnit\Framework\TestCase;

class OphthalmologyCalculatorTest extends TestCase
{
    private OphthalmologyCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new OphthalmologyCalculator();
    }

    public function test_spherical_equivalent_formula(): void
    {
        // SE = SPH + (CYL / 2)
        // Ejemplo 1: Esfera +1.50, Cilindro -0.50 -> 1.50 + (-0.25) = +1.25
        $this->assertEquals(1.25, $this->calculator->calculateSphericalEquivalent(1.50, -0.50));

        // Ejemplo 2: Esfera 0.00, Cilindro -1.00 -> 0.00 + (-0.50) = -0.50
        $this->assertEquals(-0.50, $this->calculator->calculateSphericalEquivalent(0.00, -1.00));

        // Ejemplo 3: Esfera +2.00, Cilindro 0.00 -> +2.00
        $this->assertEquals(2.00, $this->calculator->calculateSphericalEquivalent(2.00, 0.00));
    }

    public function test_snellen_to_logmar_conversion(): void
    {
        // 20/20 -> log10(20/20) = log10(1) = 0.000
        $this->assertEquals(0.000, $this->calculator->snellenToLogmar(20, 20));

        // 20/40 -> log10(40/20) = log10(2) = 0.301
        $this->assertEquals(0.301, $this->calculator->snellenToLogmar(20, 40));

        // 20/200 -> log10(200/20) = log10(10) = 1.000
        $this->assertEquals(1.000, $this->calculator->snellenToLogmar(20, 200));
    }

    public function test_hyperopic_shift_calculation(): void
    {
        // Línea base pre-vuelo: SE = +0.25 D
        // Evaluación en vuelo día 90: SE = +1.25 D
        // Delta = +1.25 - (+0.25) = +1.00 D
        $shift = $this->calculator->calculateHyperopicShift(1.25, 0.25);
        $this->assertEquals(1.00, $shift);
    }

    public function test_sans_risk_stratification(): void
    {
        // Escenario leve/normal
        $lowRisk = $this->calculator->evaluateSansRisk([
            'frisen_grade' => 0,
            'globe_flattening' => false,
            'choroidal_folds' => false,
            'hyperopic_shift' => 0.00,
        ]);
        $this->assertEquals('low', $lowRisk['tier']);
        $this->assertEmpty($lowRisk['factors']);

        // Escenario severo con papiledema y aplanamiento
        $severeRisk = $this->calculator->evaluateSansRisk([
            'frisen_grade' => 3,
            'globe_flattening' => true,
            'choroidal_folds' => true,
            'hyperopic_shift' => 1.25,
            'rnfl_thickness' => 135,
        ]);
        $this->assertContains($severeRisk['tier'], ['high', 'severe']);
        $this->assertNotEmpty($severeRisk['factors']);
        $this->assertNotEmpty($severeRisk['recommendation']);
    }
}
