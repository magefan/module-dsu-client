<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUClient\Controller\Adminhtml\DsuClient;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Magefan\DSUClient\Controller\Adminhtml\DsuClient
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of current admin session
     */
    const ADMIN_RESOURCE = 'Magefan_DSUClient::development_store_update';
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magefan_DSUClient::development_store_update');
        $resultPage->addBreadcrumb(__('Development Store Update'), __('Development Store Update'));
        $resultPage->addBreadcrumb(__('Manage Development Store Update'), __('Manage Development Store Update'));
        $resultPage->getConfig()->getTitle()->prepend(__('Development Store Update'));

        return $resultPage;
    }
}
