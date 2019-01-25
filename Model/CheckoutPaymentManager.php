<?php

namespace Resolve\Resolve\Model;

use Resolve\Resolve\Api\CheckoutPaymentManagerInterface;
use Magento\Checkout\Model\Session;
use Magento\Quote\Api\CartManagementInterface;

/**
 * Class CheckoutPaymentManager
 *
 * @package Resolve\Resolve\Model
 */
class CheckoutPaymentManager implements CheckoutPaymentManagerInterface
{
    /**
     * Checkout session object
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * Quote manager
     *
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $quoteManager;

    /**
     * Inject checkout session
     *
     * @param Session  $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Init payment
     *
     * @return bool|string
     */
    public function initPayment()
    {
        $quote = $this->session->getQuote();
        if ($quote->getId()) {
            $payment = $quote->getPayment();
            $data['method'] = \Resolve\Resolve\Model\Ui\ConfigProvider::CODE;
            $payment->importData($data);
            $quote->save();
            return true;
        }
        return false;
    }

    /**
     * Verify resolve selection
     *
     * @return bool|mixed
     */
    public function verifyResolve()
    {
        $quote = $this->session->getQuote();
        if ($quote->getId()) {
            $payment = $quote->getPayment();
            if ($payment->getData('method') == \Resolve\Resolve\Model\Ui\ConfigProvider::CODE) {
                //Clear data after verification
                $payment->setData('method', null);
                return true;
            }
        }
        return false;
    }
}
