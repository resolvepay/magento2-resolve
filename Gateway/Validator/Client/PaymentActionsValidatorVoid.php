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

namespace Resolve\Resolve\Gateway\Validator\Client;

use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class PaymentActionsValidatorVoid
 */
class PaymentActionsValidatorVoid extends PaymentActionsValidator
{
    /**#@+
     * Define constants
     */
    const RESPONSE_TYPE = 'type';
    const RESPONSE_TYPE_VOID = 'void';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $errorMessages = [];
        $validationResult = true;

        return $this->createResult($validationResult, $errorMessages);
    }

    /**
     * Validate response type
     *
     * @param array $response
     * @return bool
     */
    protected function validateResponseType(array $response)
    {
        return ($response[self::RESPONSE_TYPE] == self::RESPONSE_TYPE_VOID);
    }
}
