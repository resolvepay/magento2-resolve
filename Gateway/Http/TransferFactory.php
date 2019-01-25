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

namespace Resolve\Resolve\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferInterface;
use Resolve\Resolve\Gateway\Http\Client\ClientService;

/**
 * Class TransferFactory
 */
class TransferFactory extends AbstractTransferFactory
{
    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request)
    {
        $method = isset($request['method']) ? $request['method'] : ClientService::POST;
        $storeId = isset($request['storeId']) ? $request['storeId'] : '';
         $this->transferBuilder
            ->setMethod($method)
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic',
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate, br'
            ])
            ->setBody($request['body'])
            ->setAuthUsername($this->getPublicApiKey($storeId))
            ->setAuthPassword($this->getPrivateApiKey($storeId))
            ->setUri($this->getApiUrl($request['path']));
         return $this->transferBuilder->build();
    }

    /**
     * Get Api url
     *
     * @param string $additionalPath
     * @return string
     */
    protected function getApiUrl($additionalPath)
    {
        return $this->action->getUrl($additionalPath);
    }
}
