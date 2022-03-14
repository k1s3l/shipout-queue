<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Nexmo\Laravel\Facade\Nexmo;

class SmsNexmo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phone;
    public $code;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $phone, string $code)
    {
        $this->phone = $phone;
        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = Nexmo::message()->send([
            'from' => 'VONAGE',
            'to' => $this->phone,
            'text' => "Code is: {$this->code} ",
        ]);
        logger($message);
    }
}
