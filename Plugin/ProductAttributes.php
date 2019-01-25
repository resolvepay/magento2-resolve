<?php
namespace Resolve\Resolve\Plugin;

class ProductAttributes
{
    protected $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->objectManager = $objectManager;
    }

    public function aroundGetProductAttributes(\Magento\Quote\Model\Quote\Config $subject, \Closure $closure)
    {
        $attributesTransfer = $closure();

        $attributes = $this->objectManager->create('Resolve\Resolve\Model\ResourceModel\Rule')->getAttributes();

         foreach ($attributes as $code) {
            $attributesTransfer[] = $code;
        }

        return $attributesTransfer;

    }
}