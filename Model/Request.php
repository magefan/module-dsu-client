<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUClient\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Request
 * @package Magefan\DSUClient\Model
 */
class Request
{
    /**
     * @var \Magefan\DSUClient\Model\Config
     */
    protected $config;
    /**
     * @var \Zend\Http\HeadersFactory
     */
    protected $zendHeadersFactory;
    /**
     * @var \Zend\Http\RequestFactory
     */
    protected $zendRequestFactory;
    /**
     * @var \Zend\Http\ClientFactory
     */
    protected $zendClientFactory;

    /**
     * Request constructor.
     * @param Config $config
     * @param \Zend\Http\HeadersFactory $zendHeadersFactory
     * @param \Zend\Http\RequestFactory $zendRequestFactory
     * @param \Zend\Http\ClientFactory $zendClientFactory
     */
    public function __construct(
        Config $config,
        \Zend\Http\HeadersFactory $zendHeadersFactory,
        \Zend\Http\RequestFactory $zendRequestFactory,
        \Zend\Http\ClientFactory $zendClientFactory
    ) {
        $this->config = $config;
        $this->zendHeadersFactory = $zendHeadersFactory;
        $this->zendRequestFactory = $zendRequestFactory;
        $this->zendClientFactory = $zendClientFactory;
    }

    /**
     * @param $name
     * @param $email
     * @return string
     */
    public function send($name, $email)
    {
        $uri      = 'index.php/rest/V1/dsuserver/request/'.$name.'/'.$email;
        $liveUrl = $this->config->getLiveUrl();

        $zendHeaders = $this->zendHeadersFactory->create();
        $zendHeaders->addHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);

        $zendRequest = $this->zendRequestFactory->create();
        $zendRequest->setHeaders($zendHeaders);

        $zendRequest->setUri($liveUrl . $uri);
        $zendRequest->setMethod(\Zend\Http\Request::METHOD_PUT);

        $options = [
            'adapter' => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
            'maxredirects' => 0,
            'timeout' => 30
        ];

        $zendClient = $this->zendClientFactory->create();
        $zendClient->setOptions($options);
        $response = $zendClient->send($zendRequest)->getBody();

        $jsonResponse = @json_decode($response, true);

        if (!$jsonResponse) {
            throw new LocalizedException(__('Unexpected error on DSU server: %1', $response));
        }

        if (isset($jsonResponse['message'])) {
            throw new LocalizedException(__($jsonResponse['message']));
        }
    }
}
