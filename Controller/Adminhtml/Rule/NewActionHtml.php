<?php
namespace Resolve\Resolve\Controller\Adminhtml\Rule;

class NewActionHtml extends \Resolve\Resolve\Controller\Adminhtml\Rule
{
    public function execute()
    {
        $this->newConditions('actions');
    }
}