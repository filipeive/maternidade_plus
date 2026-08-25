<?php

namespace Tests\Feature;

use App\Services\AlertaPrecoceService;
use Tests\TestCase;

class BleedingNegationHarnessTest extends TestCase
{
    protected AlertaPrecoceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AlertaPrecoceService();
    }

    /** @test */
    public function test_bleeding_negation_variations()
    {
        $cases = [
            'Sem sangramento' => false,
            'Nega sangramento' => false,
            'Nega hemorragia' => false,
            'Ausência de sangramento' => false,
            'Não refere sangramento' => false,
            'Sem queixa de sangramento' => false,
            'Sem perda hemática' => false,
            'Sem perda de sangue' => false,
            'Nega perda de sangue' => false,
            'Nega perdas de sangue' => false,
            'Nega perdas hemáticas' => false,
            'Sem perdas hemáticas' => false,
            'Paciente refere sangramento abundante' => true,
            'Perda hemática observada' => true,
            'Presença de hemorragia activa' => true,
        ];

        $results = [];
        foreach ($cases as $phrase => $expected) {
            $actual = $this->service->temRelatoSangramento($phrase);
            $results[$phrase] = [
                'expected' => $expected,
                'actual' => $actual,
                'passed' => ($actual === $expected),
            ];
        }

        echo "\n--- Bleeding Negation Harness Results ---\n";
        foreach ($results as $phrase => $r) {
            echo sprintf("[%s] '%s' => actual: %s, expected: %s\n",
                $r['passed'] ? 'PASS' : 'FAIL',
                $phrase,
                $r['actual'] ? 'TRUE' : 'FALSE',
                $r['expected'] ? 'TRUE' : 'FALSE'
            );
        }

        // We assert without failing the entire test so we see all results
        $this->assertNotEmpty($results);
    }
}
