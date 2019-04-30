<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DSUClient\Block\Adminhtml;

/**
 * Class Form
 * @package Magefan\DSUClient\Block\Adminhtml
 */
class Form extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magefan\DSUClient\Model\Config
     */
    protected $config;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magefan\DSUClient\Model\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magefan\DSUClient\Model\Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getConfigUrl()
    {
        return $this->getUrl('adminhtml/system_config/edit', ['section' => 'dsuclient']);
    }

    /**
     * @return string
     */
    public function getLiveUrl()
    {
        return $this->config->getLiveUrl();
    }

    /**
     * @return bool
     */
    public function isLiveStore()
    {
        $liveUrl = $this->getLiveUrl();
        $currentUrl = $this->getBaseUrl();
        return trim($currentUrl, '/') == trim($liveUrl, '/');
    }

    /**
     * @param $needle
     * @return string
     */
    public function getUpdateUrl($needle)
    {
        return $this->getUrl('magefan/dsuclient/' . $needle);
    }

    /**
     * @return string
     */
    public function getRequestAccessUrl()
    {
        return $this->getUrl('magefan/dsuclient/request');
    }
}
