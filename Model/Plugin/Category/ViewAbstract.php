<?php
namespace Resolve\Resolve\Model\Plugin\Category;

use Resolve\Resolve\Model\Config as Config;
use Resolve\Resolve\Helper\AsLowAs;
use Magento\Store\Model\StoreManagerInterface;
use Resolve\Resolve\Model\Ui\ConfigProvider;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

/**
 * Class ViewAbstract
 *
 * @package Resolve\Resolve\Model\Plugin\Category
 */
class ViewAbstract extends \Magento\Framework\DataObject
{
    /**
     * Data which should be converted to json from data.
     *
     * @var array
     */
    protected $data = ['logo', 'script', 'public_api_key'];

    /**
     * Colors which could be set in "data-resolve-color".
     *
     * @var array
     */
    protected $dataColors = ['blue', 'black'];

    /**
     * Resolve Min Mpp
     *
     * @var mixed
     */
    protected $minMPP = null;

    /**
     * Resolve config
     *
     * @var Config
     */
    protected $config;

    /**
     * AsLowAs helper
     *
     * @var Config
     */
    protected $asLowAsHelper;

    /**
     * Resolve config model payment
     *
     * @var \Resolve\Resolve\Model\Config
     */
    protected $resolvePaymentConfig;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * ProductList constructor.
     *
     * @param StoreManagerInterface         $storeManagerInterface
     * @param ConfigProvider                $configProvider
     * @param Config                        $configResolve
     * @param AsLowAs                       $asLowAs
     * @param ProductCollectionFactory $productCollectionFactory
     *
     */
    public function __construct(
            StoreManagerInterface $storeManagerInterface,
            ConfigProvider $configProvider,
            Config $configResolve,
            AsLowAs $asLowAs,
            ProductCollectionFactory $productCollectionFactory
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;

        $this->asLowAsHelper = $asLowAs;
        $this->configProvider = $configProvider;

        $currentWebsiteId = $storeManagerInterface->getStore()->getWebsiteId();
        $this->resolvePaymentConfig = $configResolve;
        $this->resolvePaymentConfig->setWebsiteId($currentWebsiteId);

        if ($this->resolvePaymentConfig->getAsLowAsLogo()) {
            $this->setData('logo', $this->resolvePaymentConfig->getAsLowAsLogo());

            $configProvider = $this->configProvider->getConfig();
            if ($configProvider['payment'][ConfigProvider::CODE]) {
                $config = $configProvider['payment'][ConfigProvider::CODE];
                $this->setData('script', $config['script']);
                $this->setData('public_api_key', $config['apiKeyPublic']);
            }
            // Set max and min options amounts from payment configuration
            $this->setData('min_order_total', $this->getPaymentConfigValue('min_order_total'));
            $this->setData('max_order_total', $this->getPaymentConfigValue('max_order_total'));
        }
    }

    /**
     * Get specified data about As Low AS
     * and convert it
     * to json format.
     *
     * @return string
     */
    public function getWidgetData()
    {
        if ($this->data && $this->resolvePaymentConfig->getAsLowAsLogo() &&
            $this->resolvePaymentConfig->getAsLowAsMonths()) {
            return $this->convertToJson($this->data);
        }
        return '';
    }

    /**
     * Get data-attribute for resolve logo color
     *
     * @return string
     */
    public function getDataResolveColor()
    {
        if(in_array($this->getData('logo'), $this->dataColors)) {
            return 'data-resolve-color="' . $this->getData('logo')  . '"';
        }
        return '';
    }

    /**
     * Get is defined value from configuration
     *
     * @param string $value
     * @return bool|mixed
     */
    public function getPaymentConfigValue($value)
    {
        return $this->resolvePaymentConfig->getConfigData($value) ?
            $this->resolvePaymentConfig->getConfigData($value): false;
    }

    /**Get Min Mpp
     *
     * @return float|int
     */
    protected function getMinMPP()
    {
        if ($this->minMPP == null) {
            $this->minMPP = $this->resolvePaymentConfig->getAsLowAsMinMpp();
            if (empty($this->minMPP)) {
                $this->minMPP = 0;
            }
        }

        return $this->minMPP;
    }
}
