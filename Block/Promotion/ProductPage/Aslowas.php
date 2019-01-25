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

namespace Resolve\Resolve\Block\Promotion\ProductPage;

use Resolve\Resolve\Block\Promotion\AslowasAbstract;

/**
 * Class AsLowAs
 *
 * @package Resolve\Resolve\Block\Promotion\ProductPage
 */
class Aslowas extends AslowasAbstract
{
    /**
     * As low as data
     *
     * @var array
     */
    protected $data = ['logo', 'script', 'public_api_key', 'min_order_total', 'max_order_total',
            'selector', 'currency_rate', 'backorders_options', 'element_id'];

    /**
     * Validate block before showing on front
     * Specify validation for product "As Low As" logic.
     *
     * @return bool|void
     */
    public function validate()
    {
        $product = $this->resolvePaymentHelper->getProduct();
        if ($this->resolvePaymentConfig->getConfigData('active')
                && $this->resolvePaymentHelper->isResolveAvailableForProduct($product)
        ) {
                if ((float)$product->getFinalPrice() < (float)$this->resolvePaymentConfig->getAsLowAsMinMpp()) {
                    return false;
                }
            return true;
        }
        return false;
    }

    /**
     * Add selector data to the block context.
     * This needs for bundle product, because bundle has
     * different structure.
     */
    public function process()
    {
        if ($this->type && $this->type == 'bundle') {
            $this->setData('selector', '.bundle-info');
        } else {
            $this->setData('selector', '.product-info-main');
        }
        if (!$this->resolvePaymentConfig->isCurrentStoreCurrencyUSD()) {
            $rate = $this->resolvePaymentConfig->getUSDCurrencyRate();
            if ($rate) {
                $this->setData('currency_rate', $rate);
            }
        }
        $product = $this->resolvePaymentHelper->getProduct();
        $this->setData(
                'backorders_options',
                $this->resolvePaymentHelper->getConfigurableProductBackordersOptions($product)
        );
        $this->setData('element_id', 'als_pdp');

        parent::process();
    }

    /**
     * get MFP value for current product
     * @return string
     */
    public function getMFPValue()
    {
        $productCollection = $this->resolvePaymentHelper->getProduct()->getCollection()
            ->addAttributeToSelect(['resolve_product_promo_id', 'resolve_product_mfp_type', 'resolve_product_mfp_priority', 'resolve_product_mfp_start_date', 'resolve_product_mfp_end_date'])
            ->addAttributeToFilter('entity_id', $this->resolvePaymentHelper->getProduct()->getId());

        return $this->asLowAsHelper->getFinancingProgramValueALS($productCollection);
    }

    /**
     * Get product id on PDP
     *
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProductId()
    {
        return $this->resolvePaymentHelper->getProduct()->getId();
    }

    public function getLearnMoreValue(){
        return $this->asLowAsHelper->isVisibleLearnmore() ? 'true' :'false';
    }
}
