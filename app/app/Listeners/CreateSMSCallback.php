<?php

namespace App\Listeners;

use App\Classes\Channels\SmsRuApi;
use App\Events\TunnelCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateSMSCallback
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(protected SmsRuApi $smsRuApi)
    {
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\TunnelCreated  $event
     * @return void
     */
    public function handle(TunnelCreated $event)
    {
        foreach ($event->getTunnels() as $tunnel) {
            $this->smsRuApi->callback()->add($tunnel['public_url']);
        }
    }
}
