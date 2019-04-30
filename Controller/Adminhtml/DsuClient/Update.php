<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUClient\Controller\Adminhtml\DsuClient;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Update
 * @package Magefan\DSUClient\Controller\Adminhtml\DsuClient
 */
class Update extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of current admin session
     */
    const ADMIN_RESOURCE = 'Magefan_DSUClient::development_store_update';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var
     */
    public $type;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magefan\DSUClient\Model\Update
     */
    protected $update;

    /**
     * Update constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magefan\DSUClient\Model\Update $update
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magefan\DSUClient\Model\Update $update,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->update = $update;
        $this->messageManager = $messageManager;
        $this->request = $request;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $email = $this->request->getPostValue('email');
            $secret = $this->request->getPostValue('secret');
            $type = $this->request->getPostValue('type');  // cms // blog // configuration // database // media //

            $this->update->execute($email, $secret, $type);

            $this->messageManager->addSuccessMessage(__('Import processed successfully!'));
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
