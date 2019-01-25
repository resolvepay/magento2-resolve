<?php
/**
 *
 *  * BSD 3-Clause License
 *  *
 *  * Copyright (c) 2018, Resolve
 *  * All rights reserved.
 *  *
 *  * Redistribution and use in source and binary forms, with or without
 *  * modification, are permitted provided that the following conditions are met:
 *  *
 *  *  Redistributions of source code must retain the above copyright notice, this
 *  *   list of conditions and the following disclaimer.
 *  *
 *  *  Redistributions in binary form must reproduce the above copyright notice,
 *  *   this list of conditions and the following disclaimer in the documentation
 *  *   and/or other materials provided with the distribution.
 *  *
 *  *  Neither the name of the copyright holder nor the names of its
 *  *   contributors may be used to endorse or promote products derived from
 *  *   this software without specific prior written permission.
 *  *
 *  * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 *  * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 *  * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 *  * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 *  * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 *  * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 *  * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 *  * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 *  * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace Resolve\Resolve\Model\Plugin\Product;

use Resolve\Resolve\Model\Plugin\Category\ViewAbstract;

/**
 * Class ListProduct
 *
 * @package Resolve\Resolve\Model\Plugin\Product
 */
class ListProduct extends ViewAbstract
{
    /**
     * @param $subject
     * @param $procede
     * @param \Magento\Catalog\Model\Product $product
     * @param string $price
     * @return string
     */
    public function aroundGetProductPrice($subject, $procede, \Magento\Catalog\Model\Product $product)
    {
        $priceHtml = $procede($product);
        if (!$this->resolvePaymentConfig->isAsLowAsEnabled('plp')) {
            return $priceHtml;
        }

        $mpp = $this->getMinMPP();
        $productType=$product->getTypeID();

        if ($productType == 'bundle') {
            $bundleObj = $product->getPriceInfo()->getPrice('final_price');
            $price = $bundleObj->getMinimalPrice()->getValue();
        } else {
            $price = $product->getFinalPrice();
        }
        if ($price > $mpp) {
            $productCollection = $this->productCollectionFactory->create()
                ->addAttributeToSelect(['resolve_product_promo_id', 'resolve_product_mfp_type', 'resolve_product_mfp_priority', 'resolve_product_mfp_start_date', 'resolve_product_mfp_end_date'])
                ->addAttributeToFilter('entity_id', $product->getId());

            $mfpValue = $this->asLowAsHelper->getFinancingProgramValueALS($productCollection);
            $learnMore = $this->asLowAsHelper->isVisibleLearnmore() ? 'true' :'false';
            $priceHtml .= '<div id="as_low_as_plp_' . $product->getId() . '" class="resolve-as-low-as" data-page-type="category" ' . $this->getDataResolveColor() . ' ' . (!empty($mfpValue) ? 'data-promo-id="' . $mfpValue . '"' : '') . ' data-amount="0" data-learnmore-show="'.$learnMore.'"></div>';
        }

        return $priceHtml;
    }
}