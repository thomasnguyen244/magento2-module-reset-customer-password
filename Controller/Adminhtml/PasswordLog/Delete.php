<?php
/**
 * WorkWithThomas
 *
 * Do not edit or add to this file if you wish to upgrade to newer versions in the future.
 * If you wish to customise this module for your needs.
 * Please contact us https://workwiththomas.com/contact/.
 *
 * @category   WorkWithThomas
 * @package    Thomas_CustomerPassword
 * @copyright  Copyright (C) 2024 WorkWithThomas,.Jsc (https://workwiththomas.com/)
 * @license    https://workwiththomas.com/magento2-extension-license/
 */

declare(strict_types=1);

namespace Thomas\CustomerPassword\Controller\Adminhtml\PasswordLog;

/**
 * Class Delete
 *
 * @package Thomas\CustomerPassword\Controller\Adminhtml\PasswordLog
 */
class Delete extends \Thomas\CustomerPassword\Controller\Adminhtml\PasswordLog
{

    const ADMIN_RESOURCE = 'Thomas_CustomerPassword::delete';

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('passwordlog_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Thomas\CustomerPassword\Model\PasswordLog::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Password log.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['passwordlog_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Password log to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

