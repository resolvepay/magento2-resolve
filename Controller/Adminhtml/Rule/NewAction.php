<?php
namespace Resolve\Resolve\Controller\Adminhtml\Rule;
use Magento\Framework\App\ResponseInterface;

class NewAction extends \Resolve\Resolve\Controller\Adminhtml\Rule
{
    public function execute()
    {
        $this->_forward('edit');
    }
}