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

namespace Resolve\Resolve\Model;

use Resolve\Resolve\Api\ResolveCheckoutManagerInterface;
use Resolve\Resolve\Gateway\Helper\Util;
use Resolve\Resolve\Helper\FinancingProgram;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Resolve\Resolve\Model\Config as Config;

/**
 * Class ResolveCheckoutManager
 *
 * @package Resolve\Resolve\Model
 */
class ResolveCheckoutManager implements ResolveCheckoutManagerInterface
{

    /**
     * Gift card id cart key
     *
     * @var string
     */
    const ID = 'i';

    /**
     * Gift card amount cart key
     *
     * @var string
     */
    const AMOUNT = 'a';

    /**
     * Money format
     */
    const MONEY_FORMAT = "%.2f";

    /**
     * Injected checkout session
     *
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Injected model quote
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * Injected repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Product metadata
     *
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * Module resource
     *
     * @var \Magento\Framework\Module\ResourceInterface
     */
    protected $moduleResource;

    /**
     * Resolve financing program helper
     *
     * @var \Resolve\Resolve\Helper\FinancingProgram
     */
    protected $helper;

    /**
     * Resolve config model
     *
     * @var \Resolve\Resolve\Model\Config
     */
    protected $resolveConfig;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    private $productHelper;

    /**
     * Initialize resolve checkout
     *
     * @param Session                                    $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param ProductMetadataInterface                   $productMetadata
     * @param \Magento\Framework\Module\ResourceInterface $moduleResource
     * @param ObjectManagerInterface                     $objectManager
     * @param FinancingProgram $helper
     * @param Config                                     $resolveConfig
     */
    public function __construct(
        Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        ProductMetadataInterface $productMetadata,
        \Magento\Framework\Module\ResourceInterface $moduleResource,
        ObjectManagerInterface $objectManager,
        FinancingProgram $helper,
        Config $resolveConfig,
        \Magento\Catalog\Helper\Product $productHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quote = $this->checkoutSession->getQuote();
        $this->quoteRepository = $quoteRepository;
        $this->productMetadata = $productMetadata;
        $this->moduleResource = $moduleResource;
        $this->objectManager = $objectManager;
        $this->helper = $helper;
        $this->resolveConfig = $resolveConfig;
        $this->productHelper = $productHelper;
    }

    /**
     * Init checkout and get retrieve increment id
     * form resolve checkout
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initCheckout()
    {
        // collection totals before submit
        $this->quote->collectTotals();
        $this->quote->reserveOrderId();
        $orderIncrementId = $this->quote->getReservedOrderId();
        $discountAmount = $this->quote->getBaseSubtotal() - $this->quote->getBaseSubtotalWithDiscount();
        $shippingAddress = $this->quote->getShippingAddress();

        $response = [];
        if ($discountAmount > 0.001) {
            $discountDescription = $shippingAddress->getDiscountDescription();
            $discountDescription = ($discountDescription) ? sprintf(__('Discount (%s)'), $discountDescription) :
                sprintf(__('Discount'));
            $response['discounts'][$discountDescription] = [
                'discount_amount' => Util::formatToCents($discountAmount)
            ];
        }
        try {
            $country = $this
                ->quote
                ->getBillingAddress()
                ->getCountry();
            $result = $this->quote
                ->getPayment()
                ->getMethodInstance()
                ->canUseForCountry($country);
            if (!$result) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Your billing country isn\'t allowed by Resolve.')
                );
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        if ($orderIncrementId) {
            $this->quoteRepository->save($this->quote);
            $response['checkout_id'] = $orderIncrementId;
        }
        if ($this->productMetadata->getEdition() == 'Enterprise') {
            $giftWrapperItemsManager = $this->objectManager->create('Resolve\Resolve\Api\GiftWrapManagerInterface');
            $wrapped = $giftWrapperItemsManager->getWrapItems();
            if ($wrapped) {
                $response['wrapped_items'] = $wrapped;
            }
            $giftCards = $this->quote->getGiftCards();
            if ($giftCards) {
                $giftCards = unserialize($giftCards);
                foreach ($giftCards as $giftCard) {
                    $giftCardDiscountDescription = sprintf(__('Gift Card (%s)'), $giftCard[self::ID]);
                    $response['discounts'][$giftCardDiscountDescription] = [
                        'discount_amount' => Util::formatToCents($giftCard[self::AMOUNT])
                    ];
                }
            }
        }

        $items = [];

        $billingAddress = $this->quote->getBillingAddress();

        foreach ($this->quote->getAllItems() as $item) {
            $items[] = array(
                'sku' => $item->getSku(),
                'display_name' => $item->getName(),
                'name' => $item->getName(),
                'item_url' => $item->getProduct()->getProductUrl(),
                'item_image_url' => $this->productHelper->getImageUrl($item->getProduct()),
                'qty' => intval($item->getQty()),
                'quantity' => intval($item->getQty()),
                'unit_price' => $this->formatCents($item->getPrice())
            );
        }

        $response['currency'] = $this->quote->getQuoteCurrencyCode();
        $response['shipping_amount'] = $this->formatCents($shippingAddress->getShippingAmount());
        $response['shipping_type'] = $shippingAddress->getShippingMethod();
        $response['tax_amount'] = $this->formatCents($shippingAddress->getTaxAmount());
        $response['purchase_order_id'] = $orderIncrementId;
        $response['order_id'] = $orderIncrementId;
        $response['po_number'] = $orderIncrementId;
        $response['config'] = ['required_billing_fields' => 'name,address,email'];
        $response['items'] = $items;
        $response['billing'] = [
            'name' => [
                'full' => $this->quote->getBillingAddress()->getName()
            ],
            'email' => $this->quote->getCustomerEmail(),
            'phone_number' => $this->quote->getBillingAddress()->getTelephone(),
            'phone_number_alternative' => $this->quote->getBillingAddress()->getTelephone(),
            'address' => [
                'line1'   => $billingAddress->getStreetLine(1),
                'line2'   => $billingAddress->getStreetLine(2),
                'city'    => $billingAddress->getCity(),
                'state'   => $billingAddress->getRegion(),
                'country' => $billingAddress->getCountryModel()->getCountryId(),
                'zipcode' => $billingAddress->getPostcode(),
            ]
        ];

        if ($shippingAddress) {
            $response['shipping'] = [
                'name' => [
                    'full' => $shippingAddress->getName()
                ],
                'phone_number' => $shippingAddress->getTelephone(),
                'phone_number_alternative' => $shippingAddress->getTelephone(),
                'address' => [
                    'line1' => $shippingAddress->getStreetLine(1),
                    'line2' => $shippingAddress->getStreetLine(2),
                    'city' => $shippingAddress->getCity(),
                    'state' => $shippingAddress->getRegion(),
                    'country' => $shippingAddress->getCountryModel()->getCountryId(),
                    'zipcode' => $shippingAddress->getPostcode(),
                ]
            ];
        }

        $discountAmtResolve = (-1) * $this->quote->getShippingAddress()->getDiscountAmount();
        if ($discountAmtResolve > 0.001) {
            $discountCode = $this->quote->getShippingAddress()->getDiscountDescription();
            $response['discounts'] = [
                $discountCode => [
                    'discount_amount' => $this->formatCents($discountAmtResolve)
                ]
            ];
        }

        $response['total_amount'] = $this->formatCents($this->quote->getGrandTotal());

        $response['metadata'] = [
            'platform_type' => $this->productMetadata->getName() . ' 2',
            'platform_version' => $this->productMetadata->getVersion() . ' ' . $this->productMetadata->getEdition(),
            'platform_resolve' => $this->moduleResource->getDbVersion('Resolve_Resolve')
        ];

        $financingProgramValue = $this->helper->getFinancingProgramValue();
        if ($financingProgramValue) {
            $response['financing_program'] = $financingProgramValue;
        }

        return json_encode($response);
    }

    public function formatCents($amount) {
        return sprintf(self::MONEY_FORMAT, $amount);
    }
}
