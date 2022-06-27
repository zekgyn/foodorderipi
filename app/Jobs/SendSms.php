<?php

namespace App\Jobs;

use App\Services\SMSService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $textMessage, $mobileNumber;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(String $textMessage, String $mobileNumber)
    {
        $this->textMessage = $textMessage;
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SMSService $SMSService)
    {
        if (config('app.env') == 'production') {
            $SMSService->sendMessage($this->textMessage, $this->mobileNumber);
        }
    }
}
