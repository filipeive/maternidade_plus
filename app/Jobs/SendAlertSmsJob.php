<?php

namespace App\Jobs;

use App\Models\Alerta;
use App\Services\SmsNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAlertSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de tentativas em caso de falha transitória
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public Alerta $alerta)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(SmsNotificationService $smsService): void
    {
        $smsService->sendHighRiskAlertSms($this->alerta);
    }
}
