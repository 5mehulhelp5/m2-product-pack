<?php
/**
 * Copyright © Qoliber. All rights reserved.
*
* @category    Qoliber
* @package     Qoliber_ProductPack
 * @author      Wojciech M. Wnuk <wwnuk@qoliber.com>
*/

/**
* Created by Q-Solutions Studio
*
* @category    Qoliber
* @package     Qoliber_ProductPack
* @author      Wojciech M. Wnuk <wwnuk@qoliber.com>
*/

declare(strict_types=1);

namespace Qoliber\ProductPack\ViewModel\Product;

use Magento\Catalog\Helper\Data as TaxHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManager;
use Magento\Tax\Model\Config;

class Price implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /** @var string  */
    public const OPTION_DISPLAY_TYPE_XPATH = 'product_pack/settings/option_display_type';

    /**
     * @var string
     */
    public const XPATH_PRODUCT_PDP_CONFIG_CALCULATED_PRICE = 'product_pack/settings/pdp_display_calculated_price';

    protected ScopeConfigInterface $scopeConfig;

    protected PriceCurrencyInterface $priceCurrency;

    protected ?array $priceConfig = null;

    protected StoreManager $storeManager;

    protected TaxHelper $taxHelper;

    protected Config $taxConfig;

    protected JsonSerializer $jsonSerializer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        PriceCurrencyInterface $priceCurrency,
        StoreManager $storeManager,
        TaxHelper $taxHelper,
        Config $taxConfig,
        JsonSerializer $jsonSerializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->taxHelper = $taxHelper;
        $this->taxConfig = $taxConfig;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getOptionDisplayType(): string
    {
        return $this->scopeConfig->getValue(
            self::OPTION_DISPLAY_TYPE_XPATH,
            ScopeInterface::SCOPE_STORES,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @param $basePrice
     * @param $price
     * @param $discountType
     * @param $discountValue
     * @param float|null $specialPrice
     */
    public function calculatePrices($basePrice, $price, $discountType, $discountValue, ?float $specialPrice = 0.00, $packQty = 1): array
    {
        if ($specialPrice) {
            return [
                'price' => $this->priceCurrency->format($basePrice, false),
                'base_price' => $this->priceCurrency->format($specialPrice, false)
            ];
        }

        $x = $price ? $basePrice / $price : 1;

        if ($discountType === 'fixed') {
            $price -= $discountValue;
        } elseif ($discountType === 'percent') {
            $price *= (1 - ($discountValue / 100));
        }

        if ($price < 0) {
            $price = 0;
        }

        $basePrice = $price * $x;
        $qtyPrice = $price * $packQty;
        return [
            'price' => $this->priceCurrency->format($basePrice, false),
            'base_price' => $this->priceCurrency->format($price, false),
            'qty_price' => $this->priceCurrency->format($qtyPrice, false)
        ];
    }

    public function getPriceConfigJson(Product $product): string
    {
        $data = $this->getPriceConfig($product);
        return $this->jsonSerializer->serialize($data);
    }

    /**
     * Get Price Config
     */
    protected function getPriceConfig(Product $product): array
    {
        $store = $this->storeManager->getStore();
        if (is_null($this->priceConfig)) {
            $specialPrice = $product->getSpecialPrice();
            $price = $product->getPriceModel()->getFinalPrice(1, $product);
            $basePrice = $this->taxHelper->getTaxPrice(
                $product,
                $price,
                true,
                null,
                null,
                null,
                $store,
                $this->taxConfig->priceIncludesTax($store)
            );
            $packOptions = $product->getExtensionAttributes()->getPackOptions();

            $data = [
                '0' => [
                    'base_price' => $this->priceCurrency->format($price, false),
                    'price' => $this->priceCurrency->format($basePrice, false)
                ]
            ];

            foreach ($packOptions as $packOption) {
                $data[$packOption['packoption_id']] = $this->calculatePrices(
                    $basePrice,
                    $price,
                    $packOption['discount_type'],
                    $packOption['discount_value'],
                    (float)$specialPrice,
                    $packOption['pack_size']
                );
            }

            $this->priceConfig = $data;
        }

        return $this->priceConfig;
    }

    /**
     * Return config value to display Price * pack qty in PDP page
     *
     * @return bool|string
     * @throws NoSuchEntityException
     */
    public function displayCalculatedPrice($asJson = false): mixed
    {
        $result = (bool) $this->scopeConfig->getValue(
            self::XPATH_PRODUCT_PDP_CONFIG_CALCULATED_PRICE,
            ScopeInterface::SCOPE_STORES,
            $this->storeManager->getStore()->getId()
        );

        return ($asJson) ? $this->jsonSerializer->serialize($result) : $result;
    }
}
