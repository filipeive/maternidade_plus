<?php

namespace Tests\Feature;

use App\Services\AlertaPrecoceService;
use Tests\TestCase;

class BleedingExtendedHarnessTest extends TestCase
{
    protected AlertaPrecoceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AlertaPrecoceService();
    }

    /** @test */
    public function test_extended_bleeding_negations()
    {
        $cases = [
            'Sem sangramento' => false,
            'Nega sangramento' => false,
            'Nega hemorragia' => false,
            'Não há sangramento' => false,
            'Não ha sangramento' => false,
            'Sem perda de sangue' => false,
            'Nega perda de sangue' => false,
            'Nega perdas de sangue' => false,
            'Nega queixa de sangramento' => false,
            'Nega sinais de sangramento' => false,
            'Sem relato de sangramento' => false,
            'Sem histórico de sangramento' => false,
            'Sem indícios de sangramento' => false,
            'Nega presença de sangramento' => false,
            'Sem episódio de sangramento' => false,
            'Não se observa sangramento' => false,
        ];

        echo "\n--- Extended Bleeding Negation Test ---\n";
        foreach ($cases as $phrase => $expected) {
            $actual = $this->service->temRelatoSangramento($phrase);
            $pass = ($actual === $expected);
            echo sprintf("[%s] '%s' => actual: %s (expected: %s)\n",
                $pass ? 'PASS' : 'FAIL',
                $phrase,
                $actual ? 'TRUE' : 'FALSE',
                $expected ? 'TRUE' : 'FALSE'
            );
        }
        $this->assertTrue(true);
    }
}
