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

namespace Resolve\Resolve\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\Session;

/**
 * Identify Financing Program for customer
 */
class IdentifyFinancingProgram implements ObserverInterface
{
    /**
     * Init
     *
     */
    public function __construct(
        Session $customerSession
    ) {
        $this->_customerSession = $customerSession;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $financingProgramValue = $controller->getRequest()->getParam('resolve_fpid');
        if (empty($financingProgramValue)) {
            return;
        }
        if ($this->_customerSession->isLoggedIn()) {
            $this->_updateLoggedInCustomerMFP($financingProgramValue);
        } else {
            $this->_updateGuestCustomerMFP($financingProgramValue);
        }
        return $this;
    }

    /**
     * Update for logged-in customer
     *
     * @param string $financingProgramValue
     */
    protected function _updateLoggedInCustomerMFP($financingProgramValue)
    {
        $customer = $this->_customerSession->getCustomer();
        $customerMFPValue = $customer->getResolveCustomerMfp();
        if (empty($customerMFPValue) || ($financingProgramValue != $customerMFPValue)) {
            $customerData = $customer->getDataModel();
            $customerData->setCustomAttribute('resolve_customer_mfp', $financingProgramValue);
            $customer->updateData($customerData);
            $customer->save();
            //in case if customer logout to keep actual value during session
            $this->_updateGuestCustomerMFP($financingProgramValue);
        }
    }

    /**
     * Update for guest
     *
     * @param string $financingProgramValue
     */
    protected function _updateGuestCustomerMFP($financingProgramValue)
    {
        $sessionMFPValue = $this->_customerSession->getResolveCustomerMfp();
        if (empty($sessionMFPValue) || ($financingProgramValue != $sessionMFPValue)) {
            $this->_customerSession->setResolveCustomerMfp($financingProgramValue);
        }
    }
}
