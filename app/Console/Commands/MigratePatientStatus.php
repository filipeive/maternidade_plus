<?php

namespace App\Console\Commands;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MigratePatientStatus extends Command
{
    protected $signature = 'patients:migrate-status 
                            {--dry-run : Executar sem fazer alterações}
                            {--force : Forçar alteração mesmo que já tenha status}';
    
    protected $description = 'Migrar status das pacientes existentes baseado nas datas';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($isDryRun) {
            $this->warn('🧪 MODO DRY-RUN: Nenhuma alteração será feita');
        }

        $this->info('📋 Migrando status das pacientes existentes...');

        $query = Patient::where('ativo', true);
        
        // Se não for force, só processar quem não tem status definido
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('status_atual')
                  ->orWhere('status_atual', '');
            });
        }

        $patients = $query->get();

        if ($patients->isEmpty()) {
            $this->info('✅ Nenhuma paciente para processar');
            return 0;
        }

        $this->info("📊 Processando {$patients->count()} pacientes...\n");

        $stats = [
            'gestantes' => 0,
            'pos_parto' => 0,
            'nao_gestantes' => 0,
            'erros' => 0
        ];

        foreach ($patients as $patient) {
            $this->line("👤 {$patient->nome_completo} (ID: {$patient->id})");

            try {
                $novoStatus = $this->determinarStatus($patient);
                
                $this->info("   Status atual: " . ($patient->status_atual ?? 'NULL'));
                $this->info("   Novo status: {$novoStatus}");

                if (!$isDryRun) {
                    $patient->update(['status_atual' => $novoStatus]);
                    $this->info("   ✅ Atualizado");
                } else {
                    $this->info("   📝 Seria atualizado");
                }

                $stats[$novoStatus]++;

            } catch (\Exception $e) {
                $this->error("   ❌ Erro: " . $e->getMessage());
                $stats['erros']++;
            }

            $this->line(''); // Linha vazia
        }

        // Resumo
        $this->info("\n📈 RESUMO:");
        $this->line("Gestantes: {$stats['gestantes']}");
        $this->line("Pós-parto: {$stats['pos_parto']}");
        $this->line("Não gestantes: {$stats['nao_gestantes']}");
        
        if ($stats['erros'] > 0) {
            $this->error("Erros: {$stats['erros']}");
        }

        return 0;
    }

    private function determinarStatus(Patient $patient)
    {
        // Se não tem DUM, é não gestante
        if (!$patient->data_ultima_menstruacao) {
            return 'nao_gestante';
        }

        $dum = Carbon::parse($patient->data_ultima_menstruacao);
        $hoje = Carbon::now();
        
        // DUM futura = erro nos dados
        if ($dum->gt($hoje)) {
            $this->warn("   ⚠️  DUM futura detectada");
            return 'nao_gestante';
        }

        $semanas = $dum->diffInWeeks($hoje);
        
        // Mais de 42 semanas = provavelmente pós-parto
        if ($semanas > 42) {
            return 'pos_parto';
        }
        
        // Entre 22 e 42 semanas = gestante ativa
        if ($semanas >= 22) {
            return 'gestante';
        }
        
        // Menos de 22 semanas = gestação muito inicial ou erro
        if ($semanas < 22) {
            // Se for muito recente (menos de 4 semanas), pode ser gestação inicial
            if ($semanas >= 4) {
                return 'gestante';
            }
            
            // Muito recente, pode ser erro ou não gestante
            return 'nao_gestante';
        }

        return 'gestante'; // Default
    }
}