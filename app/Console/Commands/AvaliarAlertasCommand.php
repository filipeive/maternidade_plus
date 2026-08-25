<?php

namespace App\Console\Commands;

use App\Services\AlertaPrecoceService;
use Illuminate\Console\Command;

class AvaliarAlertasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alertas:avaliar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Avalia todas as gestantes ativas e gera alertas clínicos precoces';

    /**
     * Execute the console command.
     */
    public function handle(AlertaPrecoceService $service): int
    {
        $this->info('Iniciando avaliação clínica de alertas precoces...');

        $resultado = $service->avaliarTodas();

        $this->info("Avaliação concluída com sucesso!");
        $this->table(
            ['Métrica', 'Total'],
            [
                ['Gestantes avaliadas', $resultado['avaliadas']],
                ['Novos alertas gerados', $resultado['novos_alertas']],
            ]
        );

        return Command::SUCCESS;
    }
}
