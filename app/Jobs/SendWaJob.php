<?php

namespace App\Jobs;

use App\Models\WaLog;
use App\Services\WaGateway\WaGatewayInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public int $waLogId;

    public function __construct(int $waLogId)
    {
        $this->waLogId = $waLogId;
    }

    public function handle(WaGatewayInterface $gateway): void
    {
        $log = WaLog::find($this->waLogId);
        if (! $log) {
            return;
        }
        if ($log->status === WaLog::STATUS_SENT) {
            return;
        }

        $result = $gateway->send($log->phone_number, $log->message_body);

        if ($result['success']) {
            $log->update([
                'status' => WaLog::STATUS_SENT,
                'sent_at' => now(),
                'provider_id' => $result['providerId'] ?? null,
                'error_message' => null,
            ]);
        } else {
            $fail = (int) ($log->fail_count ?? 0) + 1;
            $log->update([
                'status' => $fail >= $this->tries ? WaLog::STATUS_FAILED : WaLog::STATUS_QUEUED,
                'fail_count' => $fail,
                'error_message' => $result['error'] ?? 'Unknown error',
                'failed_at' => $fail >= $this->tries ? now() : null,
            ]);
            if ($fail < $this->tries) {
                $this->release(now()->addSeconds($this->backoff * $fail));
            }
        }
    }
}
