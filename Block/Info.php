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

namespace Resolve\Resolve\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;

/**
 * Payment Block Info class
 *
 * @package Resolve\Resolve\Block
 */
class Info extends ConfigurableInfo
{
    /**
     * Changed standard template
     *
     * @var string
     */
    protected $_template = 'Resolve_Resolve::payment/info/edit.phtml';

    /**
     * Retrieve translated label
     *
     * @param string $field
     * @return Phrase|string
     */
    protected function getLabel($field)
    {
        return __($field);
    }

    /**
     * Is admin panel
     *
     * @return bool
     */
    protected function isInAdminPanel()
    {
        return $this->_appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;
    }

    /**
     * Get domain url
     *
     * @return string
     */
    protected function getDomainUrl()
    {
        return $this->_scopeConfig->getValue('payment/resolve_gateway/mode') == 'sandbox' ?
            'sandbox.resolve.com' : 'www.resolve.com';
    }

    /**
     * Get Public Api Key
     *
     * @return string
     */
    protected function getPublicApiKey()
    {
        return $this->_scopeConfig->getValue('payment/resolve_gateway/mode') == 'sandbox' ?
            $this->_scopeConfig->getValue('payment/resolve_gateway/public_api_key_sandbox') :
            $this->_scopeConfig->getValue('payment/resolve_gateway/public_api_key_production');
    }

    /**
     * Get admin resolve URL
     *
     * @return string
     */
    protected function getAdminResolveUrl()
    {
        $loanId = $this->getInfo()->getOrder()->getPayment()->getAdditionalInformation('charge_id');
        return sprintf('https://%s/dashboard/#/details/%s?trk=%s', $this->getDomainUrl(), $loanId,
            $this->getPublicApiKey()
        );
    }

    /**
     * Get frontend resolve URL
     *
     * @return string
     */
    protected function getFrontendResolveUrl()
    {
        $loanId = $this->getInfo()->getOrder()->getPayment()->getAdditionalInformation('charge_id');
        return sprintf("https://%s/u/#/loans/%s?trk=%s", $this->getDomainUrl(), $loanId, $this->getPublicApiKey());
    }

    /**
     * Retrieve resolve main url
     *
     * @return string
     */
    public function getResolveMainUrl()
    {
        if ($this->isInAdminPanel()) {
            return $this->getAdminResolveUrl();
        } else {
            return $this->getFrontendResolveUrl();
        }
    }
}
