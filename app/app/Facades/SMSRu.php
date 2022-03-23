<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Classes\SMS\Client sms()
 * @method static \App\Classes\BulkSMS\Client bulkSms()
 * @method static \App\Classes\Call\Client callNumber()
 * @method static \App\Classes\Callback\Client callback()
 * @see \App\Classes\Channels\SmsRuApi
 */
class SMSRu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'sms_ru';
    }
}
