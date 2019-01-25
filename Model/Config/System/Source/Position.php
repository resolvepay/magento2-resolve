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

namespace Resolve\Resolve\Model\Config\System\Source;

/**
 * Class Position
 *
 * @package Resolve\Resolve\Model\Config
 */
class Position implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            '0' => __('↑ Center top'),
            '1' => __('↓ Center bottom'),
            '2' => __('↖ Sidebar top'),
            '3' => __('↙ Sidebar bottom')
        ];
    }

    /**
     * Get checkout cart position
     *
     * @return array
     */
    public function getCCPosition()
    {
        return [
            '0' => __('↑ Center Top'),
            '1' => __('↓ Center bottom'),
            '2' => __('↑ Near checkout button'),
        ];
    }

    /**
     * Bml positions source getter for Catalog Product Page
     *
     * @return array
     */
    public function getBmlPositionsCPP()
    {
        return [
            '0' => __('↑ Header (center) top'),
            '1' => __('↓ Header (center) bottom'),
            '2' => __('↑ Near checkout button')
        ];
    }

    /**
     * Block placement for Product Detail Page
     *
     * @return array
     */
    public function getBlockPlacementPDP()
    {
        return [
            '0' => __('After Price'),
            '1' => __('Before Price'),
            '2' => __('End of Product Info')
        ];
    }
}
