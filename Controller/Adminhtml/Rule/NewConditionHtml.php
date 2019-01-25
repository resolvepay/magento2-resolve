<?php
namespace Resolve\Resolve\Controller\Adminhtml\Rule;

class NewConditionHtml extends \Resolve\Resolve\Controller\Adminhtml\Rule
{
    public function execute()
    {
        $this->newConditions('conditions');
    }
}