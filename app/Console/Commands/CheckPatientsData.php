<?php
// Criar este arquivo em: app/Console/Commands/CheckPatientsData.php

namespace App\Console\Commands;

use App\Models\Patient;
use Illuminate\Console\Command;

class CheckPatientsData extends Command
{
    protected $signature = 'patients:check';
    protected $description = 'Verificar dados das gestantes de forma simples';

    public function handle()
    {
        $this->info('=== Verificação Rápida das Gestantes ===');
        
        $patients = Patient::where('ativo', true)->get();
        
        $this->info("Total de gestantes ativas: {$patients->count()}\n");
        
        foreach ($patients as $patient) {
            $this->line("👤 {$patient->nome_completo} (ID: {$patient->id})");
            
            // Verificar DUM
            if ($patient->data_ultima_menstruacao) {
                $this->line("   📅 DUM: {$patient->data_ultima_menstruacao->format('d/m/Y')}");
                
                // Verificar idade gestacional
                $semanas = $patient->idade_gestacional;
                if ($semanas) {
                    $this->info("   🤱 Idade gestacional: {$semanas} semanas ({$patient->trimestre})");
                    $this->line("   📊 Status: {$patient->status_gravidez}");
                    $this->line("   ⚠️  Risco: {$patient->risco_gestacional}");
                } else {
                    $this->warn("   ❌ Idade gestacional: NULL");
                    
                    // Investigar por que é NULL
                    $dum = $patient->data_ultima_menstruacao;
                    $hoje = now();
                    $diasDiff = $dum->diffInDays($hoje);
                    $semanasDiff = $dum->diffInWeeks($hoje);
                    
                    $this->line("   🔍 Dias desde DUM: {$diasDiff}");
                    $this->line("   🔍 Semanas calculadas: {$semanasDiff}");
                    
                    if ($dum->gt($hoje)) {
                        $this->error("   ❌ DUM é futura!");
                    } elseif ($semanasDiff > 42) {
                        $this->warn("   ⚠️  Mais de 42 semanas (provável pós-parto)");
                    }
                }
                
                // Verificar DPP
                if ($patient->data_provavel_parto) {
                    $this->line("   🍼 DPP: {$patient->data_provavel_parto->format('d/m/Y')}");
                } else {
                    $this->warn("   ❌ DPP não calculada");
                }
                
            } else {
                $this->warn("   ❌ Sem DUM registrada");
            }
            
            $this->line(''); // Linha vazia
        }
        
        // Estatísticas
        $comDUM = $patients->whereNotNull('data_ultima_menstruacao')->count();
        $gestantes = $patients->filter(fn($p) => $p->status_gravidez === 'Gestante')->count();
        $posParto = $patients->filter(fn($p) => $p->status_gravidez === 'Pós-parto')->count();
        
        $this->info('=== ESTATÍSTICAS ===');
        $this->line("Com DUM: {$comDUM}");
        $this->line("Gestantes: {$gestantes}");
        $this->line("Pós-parto: {$posParto}");
        
        return 0;
    }
}