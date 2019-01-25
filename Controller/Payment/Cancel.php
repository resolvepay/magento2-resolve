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

namespace Resolve\Resolve\Controller\Payment;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Action;

/**
 * Payment cancel action
 *
 * @package Resolve\Resolve\Controller\Payment
 */
class Cancel extends Action
{
    /**
     * Resolve cancel action
     * redirects to checkout cart in case if customer return from resolve to merchant.
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        // Redirects customer to checkout cart page.
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('checkout');
        return $resultRedirect;
    }
}
