<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category    Resolve
 * @package     Resolve_Resolve
 * @copyright   Copyright (c) 2014 One Pica, Inc. (http://www.onepica.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Resolve\Resolve\Model\Plugin\Order;

use \Magento\Sales\Controller\Adminhtml\Order\Create\Save as SaveAction;
use \Magento\Framework\Controller\Result\RedirectFactory ;

/**
 * Class Create
 *
 * @package Resolve\Resolve\Model\Plugin\Order
 */
class Create
{
    /**
     * Result redirect factory
     *
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $forwardRedirectFactory;

    /**
     * Inject redirect factory
     *
     * @param RedirectFactory $forwardFactory
     */
    public function __construct(RedirectFactory $forwardFactory)
    {
        $this->forwardRedirectFactory = $forwardFactory;
    }

    /**
     * Plugin for save order new order in admin
     *
     * @param SaveAction $controller
     * @param callable   $method
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function aroundExecute(SaveAction $controller, \Closure $method)
    {
        $data = $controller->getRequest()->getParam('payment');
        if (isset($data['method']) && $data['method'] == \Resolve\Resolve\Model\Ui\ConfigProvider::CODE) {
            $resultRedirect = $this->forwardRedirectFactory->create();
            $resultRedirect->setPath('resolve/resolve/error');
            return $resultRedirect;
        }
        return $method();
    }
}
