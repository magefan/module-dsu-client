<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUClient\Model;

/**
 * Class Config
 * @package Magefan\DSUClient\Model
 */
class Config
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config path
     */
    const XML_PATH_LIVE_URL = 'dsuclient/general/live_url';

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getLiveUrl()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $liveUrl = trim($this->scopeConfig->getValue(self::XML_PATH_LIVE_URL, $storeScope));
        if ($liveUrl && $liveUrl[strlen($liveUrl)-1] != '/') {
            $liveUrl .= '/';
        }

        return $liveUrl;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getValue($path)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue($path, $storeScope);
    }
}
