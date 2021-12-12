<?php

namespace App\Classes\Channels;

use GuzzleHttp\Client as HttpClient;
use App\Classes\{
    BulkSMS\Client as BulkSMSClient,
    SMS\Client as SMSClient,
};
use App\Classes\Factory\{
    FactoryInterface,
    MapFactory
};
use Psr\{
    Container\ContainerInterface,
    Http\Client\ClientInterface
};

class SmsRuApi
{
    /**
     * @return array
     */
    private $_apiId;

    /**
     * @return ClientInterface
     */
    protected $httpClient;

    /**
     * @return ContainerInterface
     */
    protected $factory;

    public function __construct(
        array $config, ClientInterface $httpClient = new HttpClient
    ) {
        $this->_apiId = $config['api_id'];
        $this->httpClient = $httpClient;
        $this->setFactory(
            new MapFactory(
                [
                    'send' => SMSClient::class,
                    'bulkSend' => BulkSMSClient::class
                ]
            )
        );
    }


    public function __call($name, $args)
    {
        $class = $this->factory->get($name);
        call_user_func_array($class, $args);
    }

    public function setFactory(FactoryInterface&ContainerInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    public function send($options)
    {
        return $this->httpClient->get('https://sms.ru/sms/send', $options + ['api_id' => $this->_apiId]);
    }
}
