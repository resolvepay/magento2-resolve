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

namespace Resolve\Resolve\Model\Plugin\Payment\Checks;

use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Model\Quote;

/**
 * Class CanUseForCountry
 *
 * @package Resolve\Resolve\Model\Plugin\Payment\Checks
 */
class CanUseForCountry
{
    /**
     * Verify if afirm payment applicable
     *
     * @param \Magento\Payment\Model\Checks\CanUseForCountry $subject
     * @param callable                                       $method
     * @param MethodInterface                                $payment
     * @param Quote                                          $quote
     * @return bool
     */
    public function aroundIsApplicable(
        \Magento\Payment\Model\Checks\CanUseForCountry $subject,
        \Closure $method,
        MethodInterface $payment,
        Quote $quote
    ) {
        if ($payment->getCode() == \Resolve\Resolve\Model\Ui\ConfigProvider::CODE) {
            if (!$quote->getCustomerId() && $quote->getIsVirtual()) {
                return false;
            }
        }
        return $method($payment, $quote);
    }
}
