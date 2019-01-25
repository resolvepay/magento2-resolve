<?php
/**
 * Resolve
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@resolvecommerce.com so we can send you a copy immediately.
 *
 * @category  Resolve
 * @package   Resolve_Resolve
 * @copyright Copyright (c) 2016 Resolve, Inc. (http://www.resolvecommerce.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Resolve\Resolve\Block\Onepage;

use Magento\Framework\View\Element\Template;
use Resolve\Resolve\Helper\Payment;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ResolveButton
 *
 * @package Resolve\Resolve\Block\Onepage
 */
class ResolveButton extends Template
{
    /**
     * Resolve payment model instance
     *
     * @var \Resolve\Resolve\Helper\Payment
     */
    protected $helper;

    /**
     * Current checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Current checkout quote
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * Button template
     *
     * @var string
     */
    protected $_template = 'Resolve_Resolve::onepage/button.phtml';

    /**
     * Resolve checkout button block
     *
     * @param Template\Context                $context
     * @param Payment                         $helper
     * @param \Magento\Checkout\Model\Session $session
     * @param array                           $data
     */
    public function __construct(
        Template\Context $context,
        \Resolve\Resolve\Helper\Payment $helper,
        \Magento\Checkout\Model\Session $session,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->quote = $session->getQuote();
        parent::__construct($context, $data);
    }

    /**
     * Get button image from system configs
     *
     * @return bool|mixed
     */
    public function getButtonImageSrc()
    {
        $buttonSrc = $this->_scopeConfig->getValue(
            'payment/resolve_gateway/checkout_button_code',
            ScopeInterface::SCOPE_WEBSITE
        );
        if ($buttonSrc) {
            return $buttonSrc;
        }
        return false;
    }

    /**
     * Show button only if quote isn't virtual at all
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->isAvailable()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Get checkout url
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout');
    }

    /**
     * Get button availability
     *
     * @return bool|mixed
     */
    public function isAvailable()
    {
        return $this->helper->isResolveAvailable() && $this->isButtonEnabled() ? true: false;
    }

    /**
     * Check is button enabled
     *
     * @return mixed
     */
    public function isButtonEnabled()
    {
        return $this->_scopeConfig->getValue(
            'payment/resolve_gateway/enable_checkout_button',
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
