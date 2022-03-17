<?php

namespace App\Console\Commands;

use App\Classes\Channels\SmsRuApi;
use Illuminate\Console\Command;

class DeleteCallbackSmsRu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms_ru:delete {--url} {--A|all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        protected SmsRuApi $smsRuApi,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = $this->option('url');
        $all = $this->option('all');

        if (!$url && !$all) {
            $this->error('Fill --url or --all');

            return 0;
        } elseif ($all) {
            $callbacks = $this->smsRuApi->callback()->get()
                ->getBody()
                ->getContents();

            $callbacks = json_decode($callbacks)->callback ?? [];

            foreach ($callbacks as $callback) {
                $this->line("Deleting callback {$callback}");
                $this->smsRuApi->callback()->remove($callback);
            }
        } else {
            $this->smsRuApi->callback()->remove($url);
        }

        $this->line('<fg=green>Callback removing completed!</fg=green>');

        return 1;
    }
}
