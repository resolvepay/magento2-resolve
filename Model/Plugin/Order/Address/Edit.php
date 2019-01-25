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

namespace Resolve\Resolve\Model\Plugin\Order\Address;

use Magento\Sales\Controller\Adminhtml\Order\Address;
use Magento\Framework\Controller\Result\RedirectFactory ;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Resolve\Resolve\Model\Ui\ConfigProvider;

/**
 * Class Edit
 */
class Edit
{
    /**
     * Result redirect factory
     *
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $forwardRedirectFactory;

    /**
     * Message manager
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Collection factory
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Construct
     *
     * @param RedirectFactory $forwardFactory
     * @param ManagerInterface $messageManager
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        RedirectFactory $forwardFactory,
        ManagerInterface $messageManager,
        CollectionFactory $collectionFactory
    ) {
        $this->forwardRedirectFactory = $forwardFactory;
        $this->_messageManager = $messageManager;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Plugin for edit order address in admin
     *
     * @param Address $controller
     * @param callable   $method
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function aroundExecute(Address $controller, \Closure $method)
    {
        $addressId = $controller->getRequest()->getParam('address_id');
        $orderCollection = $this->_collectionFactory->create()->addAttributeToSearchFilter(
            [
                ['attribute' => 'billing_address_id', 'eq' => $addressId . '%'],
                ['attribute' => 'shipping_address_id', 'eq' => $addressId . '%']
            ]
        )->load();
        $order = $orderCollection->getFirstItem();

        if ($order->getId() && $order->getPayment()->getMethod() == ConfigProvider::CODE) {
            $this->_messageManager->addWarning(
                __('Editing address is not available. Please contact Resolve for updating shipping/billing address.')
            );
            $resultRedirect = $this->forwardRedirectFactory->create();
            $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
            return $resultRedirect;
        }
        return $method();
    }
}
