<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUClient\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Class Update
 * @package Magefan\DSUClient\Model
 */
class Update
{
    /**
     * @var \Magefan\DSUClient\Model\Config
     */
    protected $config;

    /**
     * @var \Magefan\DSUClient\Model\Update\MediaManager
     */
    protected $mediaManager;
    /**
     * @var \Magefan\DSUClient\Model\Update\DatabaseManager
     */
    protected $databaseManager;
    /**
     * @var \Zend\Http\RequestFactory
     */
    protected $zendRequestFactory;
    /**
     * @var \Zend\Http\ClientFactory
     */
    protected $zendClientFactory;
    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * Update constructor.
     * @param Config $config
     * @param Update\MediaManager $mediaManager
     * @param Update\DatabaseManager $databaseManager
     * @param \Zend\Http\RequestFactory $zendRequestFactory
     * @param \Zend\Http\ClientFactory $zendClientFactory
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        Config $config,
        \Magefan\DSUClient\Model\Update\MediaManager $mediaManager,
        \Magefan\DSUClient\Model\Update\DatabaseManager $databaseManager,
        \Zend\Http\RequestFactory $zendRequestFactory,
        \Zend\Http\ClientFactory $zendClientFactory,
        ModuleListInterface $moduleList
    ) {
        $this->config = $config;
        $this->mediaManager = $mediaManager;
        $this->databaseManager = $databaseManager;
        $this->zendRequestFactory = $zendRequestFactory;
        $this->zendClientFactory = $zendClientFactory;
        $this->moduleList = $moduleList;
    }

    /**
     * @param $email
     * @param $secret
     * @param $type
     * @throws LocalizedException
     */
    public function execute($email, $secret, $type)
    {
        $type = trim($type);  // cms // blog // configuration // database // media //
        if (!$type) {
            throw new LocalizedException(__('Update type is empty.'));
        }

        if (!in_array($type, ['cms', 'blog', 'configuration', 'database', 'media'])) {
            throw new LocalizedException(__('Type %1 is not allowed.', $type));
        }

        if ('blog' == $type) {
            $blog = $this->moduleList->getOne('Magefan_Blog');

            if (!$blog) {
                throw new LocalizedException(__('Magefan Blog is not installed.', $type));
            }
        }

        $email = trim($email);
        if (!$email) {
            throw new LocalizedException(__('Email is empty.'));
        }

        $secret = trim($secret);
        if (!$secret) {
            throw new LocalizedException(__('Secret is empty.'));
        }

        $response = $this->sendApiRequest($type, $email, $secret);

        $jsonResponse = @json_decode($response, true);

        if (isset($jsonResponse['message'])) {
            throw new LocalizedException(__($jsonResponse['message']));
        }

        if ('media' == $type) {
            $this->mediaManager->execute($response);
        } else {
            $this->databaseManager->execute($response, $type);
        }
    }

    /**
     * @param $type
     * @param $email
     * @param $secret
     * @return string
     */
    protected function sendApiRequest($type, $email, $secret)
    {
        $uri      = 'index.php/rest/V1/dsuserver/get/' . $type;

        $liveUrl = $this->config->getLiveUrl();
        if ($liveUrl[strlen($liveUrl)-1] != '/') {
            $liveUrl.='/';
        }

        $data = [
            'email' => $email,
            'secret' => $secret
        ];
        $zendRequest = $this->zendRequestFactory->create();
        $zendRequest->setMethod(\Zend\Http\Request::METHOD_POST);

        $zendClient = $this->zendClientFactory->create();
        $zendClient->setRequest($zendRequest);
        $zendClient->setOptions(['timeout' => 3600]);
        $zendClient->setRawBody(json_encode($data));
        $zendClient->setUri($liveUrl . $uri);
        $zendClient->setHeaders(['Content-type' => 'application/json']);

        $response = $zendClient->send()->getBody();

        return $response;
    }
}
