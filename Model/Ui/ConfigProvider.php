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

namespace Resolve\Resolve\Model\Ui;

use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\ProductMetadataInterface;
use Resolve\Resolve\Model\Config as ConfigResolve;

/**
 * Class ConfigProvider
 * Config provider for the payment method
 *
 * @package Resolve\Resolve\Model\Ui
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**#@+
     * Define constants
     */
    const CODE = 'resolve_gateway';
    const SUCCESS = 0;
    const FRAUD = 1;
    /**#@-*/

    /**
     * Resolve config model
     *
     * @var \Resolve\Resolve\Model\Config
     */
    protected $resolveConfig;

    /**
     * Injected config object
     *
     * @var \Magento\Payment\Gateway\ConfigInterface
     */
    protected $config;

    /**
     * Injected url builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Product metadata object
     *
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * Inject all needed object for getting data from config
     *
     * @param ConfigInterface          $config
     * @param UrlInterface             $urlInterface
     * @param CheckoutSession          $checkoutSession
     * @param ProductMetadataInterface $productMetadata
     * @param ConfigResolve             $configResolve
     */
    public function __construct(
        ConfigInterface $config,
        UrlInterface $urlInterface,
        CheckoutSession $checkoutSession,
        ProductMetadataInterface $productMetadata,
        ConfigResolve $configResolve
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlInterface;
        $this->checkoutSession = $checkoutSession;
        $this->productMetadata = $productMetadata;
        $this->resolveConfig = $configResolve;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'transactionResults' => [
                        self::SUCCESS => __('Success'),
                        self::FRAUD => __('Fraud')
                    ],
                    'apiKeyPublic' => $this->resolveConfig->getPublicApiKey(),
                    'apiUrl' => $this->resolveConfig->getApiUrl(),
                    'merchant' => [
                        'public_api_key' => $this->resolveConfig->getPublicApiKey(),
                        'user_confirmation_url' => $this->urlBuilder
                            ->getUrl('resolve/payment/confirm', ['_secure' => true]),
                        'user_cancel_url' => $this->urlBuilder
                            ->getUrl('resolve/payment/cancel', ['_secure' => true]),
                        'user_confirmation_url_action' => 'POST',
                        'charge_declined_url' => $this->urlBuilder
                            ->getUrl('checkout', ['_secure' => true]),
                        'id' => $this->resolveConfig->getPublicApiKey(),
                        'success_url' => $this->urlBuilder
                            ->getUrl('resolve/payment/confirm', ['_secure' => true]),
                        'cancel_url'=> $this->urlBuilder
                            ->getUrl('checkout', ['_secure' => true])
                    ],
                    'config' => [
                        'financial_product_key' => null
                    ],
                    'mode' => $this->config->getValue('mode'),
                    'redirectUrl' => $this->urlBuilder->getUrl('resolve/checkout/start', ['_secure' => true]),
                    'afterResolveConf' => $this->config->getValue('after_resolve_conf'),
                    'info' => $this->config->getValue('info'),
                    'visibleType' => $this->config->getValue('control') ? true: false,
                    'description' => $this->config->getValue('info-description'),
                    'visibleTypeDescription' => $this->config->getValue('control-description') ? true: false
                ]
            ]
        ];
    }
}
