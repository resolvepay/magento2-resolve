<?php
namespace Resolve\Resolve\Controller\Adminhtml\Rule;

class Index extends \Resolve\Resolve\Controller\Adminhtml\Rule
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Resolve_Resolve::rule');
        $resultPage->getConfig()->getTitle()->prepend(__('Payment Restrictions Rules'));
        $resultPage->addBreadcrumb(__('Payment Restrictions Rules'), __('Payment Restrictions Rules'));
        return $resultPage;
    }
}