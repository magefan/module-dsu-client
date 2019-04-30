<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUClient\Controller\Adminhtml\DsuClient;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Request
 * @package Magefan\DSUClient\Controller\Adminhtml\DsuClient
 */
class Request extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of current admin session
     */
    const ADMIN_RESOURCE = 'Magefan_DSU::config_dsuclient';

    /**
     * @var \Magefan\DSUClient\Model\Request
     */
    protected $requestModel;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Request constructor.
     * @param Context $context
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magefan\DSUClient\Model\Request $requestModel
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magefan\DSUClient\Model\Request $requestModel
    ) {
        parent::__construct($context);
        $this->messageManager = $messageManager;
        $this->requestModel = $requestModel;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $name = $this->getRequest()->getPost('name');
        $email = $this->getRequest()->getPost('email');

        try {
            $this->requestModel->send($name, $email);

            $this->messageManager->addSuccessMessage(__('Request has been sent successfully. Please wait until moderator approve it.'));
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Unexpected Error'));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }
}
