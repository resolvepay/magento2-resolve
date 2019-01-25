<?php

namespace Resolve\Resolve\Block\Promotion\CartPage;

use Resolve\Resolve\Block\Promotion\AslowasAbstract;
use Resolve\Resolve\Model\Ui\ConfigProvider;
use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Session;
use Resolve\Resolve\Helper;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Class AsLowAs
 *
 * @package Resolve\Resolve\Block\Promotion\CartPage
 */
class Aslowas extends AslowasAbstract
{
    /**
     * Data which should be converted to json from the Block data.
     *
     * @var array
     */
    protected $data = ['logo', 'script', 'public_api_key', 'min_order_total', 'max_order_total', 'element_id'];

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Financing program helper factory
     *
     * @var Helper\FinancingProgram
     */
    protected $fpHelper;

    /**
     * Cart page block.
     *
     * @param Template\Context               $context
     * @param ConfigProvider                 $configProvider
     * @param \Resolve\Resolve\Model\Config   $configResolve
     * @param \Resolve\Resolve\Helper\Payment $helperResolve
     * @param Session                        $session
     * @param array                          $data
     * @param Helper\AsLowAs                 $asLowAs
     * @param \Resolve\Resolve\Helper\Rule    $rule
     */
    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        \Resolve\Resolve\Model\Config $configResolve,
        \Resolve\Resolve\Helper\Payment $helperResolve,
        Session $session,
        array $data = [],
        Helper\AsLowAs $asLowAs,
        \Resolve\Resolve\Helper\Rule $rule,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->checkoutSession = $session;
        parent::__construct($context, $configProvider, $configResolve, $helperResolve, $data, $asLowAs, $rule, $categoryCollectionFactory);
    }

    /**
     * Get current quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * Validate block before showing on front in checkout cart
     * There can be added new validators by needs.
     *
     * @return boolean
     */
    public function validate()
    {
        if ($this->getQuote()) {
            // Payment availability flag
            $isAvailableFlag = $this->getPaymentConfigValue('active');

            //Validate aslowas block based on appropriate values and conditions
            if ($isAvailableFlag && $this->resolvePaymentHelper->isResolveAvailable()) {
                return true;
            }
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
        $this->setData('element_id', 'als_pcc');

        parent::process();
    }

    /**
     * get MFP value for current cart
     * @return string
     */
    public function getMFPValue()
    {
        return $this->asLowAsHelper->getFinancingProgramValue();
    }

    public function getLearnMoreValue(){
        return $this->asLowAsHelper->isVisibleLearnmore() ? 'true' :'false';
    }
}
