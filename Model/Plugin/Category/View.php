<?php
namespace Resolve\Resolve\Model\Plugin\Category;

/**
 * Class View
 *
 * @package Resolve\Resolve\Model\Plugin\Category
 */
class View extends ViewAbstract
{

    /**
     * @param $subject
     * @param string $productListHtml
     * @return string
     */
    public function afterGetProductListHtml($subject, $productListHtml)
    {
        if (!$this->resolvePaymentConfig->isAsLowAsEnabled('plp')) {
            return $productListHtml;
        }

        $productListHtml .= '<span data-mage-init=\'{"Resolve_Resolve/js/aslowasPLP": ' . $this->getWidgetData() . '}\'></span>';

        return $productListHtml;
    }
}
