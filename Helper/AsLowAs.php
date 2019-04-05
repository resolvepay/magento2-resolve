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

namespace Resolve\Resolve\Helper;

use Magento\Catalog\Model\ResourceModel\Category;
use Magento\Catalog\Model\ResourceModel\Product;

/**
 * Financing program helper
 *
 * @package Resolve\Resolve\Helper
 */
class AsLowAs extends FinancingProgram
{

    protected $_allRules = null;

    /**
     * Initialization
     *
     */
    protected function _init()
    {
        $this->isALS = true;
    }

    /**
     * Get categories from products
     *
     * @param Product\Collection $productCollection
     *
     * @return Category\Collection
     */
    protected function getCategoryCollection(Product\Collection $productCollection)
    {
        $categoryItemsIds = [];
        $flagProductWithoutMfpCategories = false;
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($productCollection as $product) {
            /** @var Category\Collection $categoryProductCollection */
            $categoryProductCollection = $product->getCategoryCollection();
            $categoryProductCollection
                ->addAttributeToFilter('resolve_category_mfp', array('neq' => ''))
                ->addAttributeToFilter('resolve_category_mfp', array('notnull' => true));
            $categoryIds = $categoryProductCollection->getAllIds();
            if (!empty($categoryIds)) {
                $categoryItemsIds = array_merge($categoryItemsIds, $categoryIds);
            } else {
                $flagProductWithoutMfpCategories = true;
            }
        }
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
        $categoryCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect(['resolve_category_mfp', 'resolve_category_mfp_type', 'resolve_category_mfp_priority', 'rs_category_mfp_start_date', 'resolve_category_mfp_end_date'])
            ->addAttributeToFilter('entity_id', array('in' => $categoryItemsIds));
        if ($flagProductWithoutMfpCategories) {
            $categoryCollection->setFlag('productWithoutMfpCategories', true);
        }
        return $categoryCollection;
    }

    /**
     * Get financing program value
     *
     * @param Product\Collection $productCollection
     *
     * @return string
     */
    public function getFinancingProgramValueALS(Product\Collection $productCollection)
    {
        $dynamicallyMFPValue = $this->getCustomerFinancingProgram();
        if (!empty($dynamicallyMFPValue)) {
            return $dynamicallyMFPValue;
        } elseif ($mfpValue = $this->getFinancingProgramFromProductsALS($productCollection)) {
            return $mfpValue;
        } elseif ($mfpValue = $this->getFinancingProgramFromCategoriesALS($productCollection)) {
            return $mfpValue;
        } elseif ($this->isFinancingProgramValidCurrentDate()) {
            return $this->getFinancingProgramDateRange();
        } else {
            return $this->getFinancingProgramDefault();
        }
    }

    /**
     * Is visible Learn more for ALA
     *
     * @return boolean
     */
    public function isVisibleLearnmore()
    {
        return $this->resolvePaymentConfig->getAsLowAsValue('learn_more');
    }
}
