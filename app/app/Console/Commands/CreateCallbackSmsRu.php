<?php

namespace App\Console\Commands;

use App\Classes\Channels\SmsRuApi;
use Illuminate\Console\Command;

class CreateCallbackSmsRu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms_ru:callback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create callback api sms.ru';

    protected $smsRuApi;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SmsRuApi $smsRuApi)
    {
        parent::__construct();
        $this->smsRuApi = $smsRuApi;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($host = env('CALLBACK_URL')) {
            $this->smsRuApi->callback()->add($host . route(name: 'sms_ru_callback', absolute: false));

            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}
