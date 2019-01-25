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
 * Source model for sizes in admin panel
 *
 * @package Resolve\Resolve\Model\Config\System\Source
 */
class Size implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Default sizes
     *
     * @var array
     */
    public static $sizes = [
        '120x90',
        '150x100',
        '170x100',
        '190x100',
        '234x60',
        '300x50',
        '468x60',
        '300x250',
        '336x280',
        '540x200',
        '728x90',
        '800x66',
        '250x250',
        '280x280',
        '120x240',
        '120x600',
        '234x400'
    ];

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = array();
        foreach (self::$sizes as $size) {
            $options[] = array('value' => $size, 'label' => $size);
        }
        return $options;
    }
}
