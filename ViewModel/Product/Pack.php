<?php
/**
 * Copyright © Qoliber. All rights reserved.
 *
 * @category    Qoliber
 * @package     Qoliber_ProductPack
 * @author      Jakub Winkler <jwinkler@qoliber.com>
 * @author      Wojciech M. Wnuk <wwnuk@qoliber.com>
 * @author      Łukasz Owczarczuk <lowczarczuk@qoliber.com>
 */

declare(strict_types=1);

namespace Qoliber\ProductPack\ViewModel\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item\Option\Collection;
use Magento\Quote\Model\ResourceModel\Quote\Item\Option\CollectionFactory;
use Magento\Store\Model\ScopeInterface as StoreScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Qoliber\ProductPack\Api\Data\PackOptionInterface;
use Qoliber\ProductPack\Api\PackOptionRepositoryInterface;
use Qoliber\ProductPack\Model\Config\Source\SpecialPriceCalculationType;
use Qoliber\ProductPack\Model\PackOption;
use Qoliber\ProductPack\Model\PackOptionFactory;
use Qoliber\ProductPack\Model\Product\Price;

class Pack implements ArgumentInterface
{
    protected Request $request;

    protected PackOptionRepositoryInterface $packOptionRepository;

    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    protected PriceCurrencyInterface $priceCurrency;

    /** @var PackOptionFactory */
    protected PackOptionFactory $packOptionFactory;

    protected Registry $coreRegistry;

    protected ProductRepositoryInterface $productRepository;

    protected StoreManagerInterface $storeManager;

    protected Image $image;

    /** @var CollectionFactory */
    protected CollectionFactory $itemOptionCollectionFactory;

    protected Resolver $localeResolver;

    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param CollectionFactory $itemOptionCollectionFactory
     * @param PackOptionFactory $packOptionFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $itemOptionCollectionFactory,
        Request $request,
        PackOptionRepositoryInterface $packOptionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PriceCurrencyInterface $priceCurrency,
        PackOptionFactory $packOptionFactory,
        Registry $coreRegistry,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        Image $image,
        Resolver $localeResolver
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->itemOptionCollectionFactory = $itemOptionCollectionFactory;
        $this->request = $request;
        $this->packOptionRepository = $packOptionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->priceCurrency = $priceCurrency;
        $this->packOptionFactory = $packOptionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->image = $image;
        $this->localeResolver = $localeResolver;
    }

    public function getLocaleCode(): string
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Get Pack Options
     *
     * @return PackOptionInterface[]
     */
    public function getPackOptions(): array
    {
        $productId = $this->request->getParam('id', false);

        if ($productId) {
            $this->searchCriteriaBuilder->addFilter(PackOptionInterface::PRODUCT_ID, $productId);

            return $this->sortPackOptionsByPricePerUnit(
                $this->packOptionRepository->getList($this->searchCriteriaBuilder->create())->getItems()
            );
        }

        return [];
    }

    /**
     * @param PackOptionInterface[] $packOptions
     * @return PackOptionInterface[]
     */
    private function sortPackOptionsByPricePerUnit(array $packOptions): array
    {
        if ($packOptions === []) {
            return [];
        }

        $packOptionPrices = [];
        $packOptionSorted = [];
        /** @var PackOption $packOption */
        foreach ($packOptions as $packOption) {
            $packOptionPrices[$packOption->getId()] = $this->getPricePerUnit($packOption);
            $packOptionSorted[$packOption->getId()] = $packOption;
        }

        arsort($packOptionPrices);

        return array_replace($packOptionPrices, $packOptionSorted);
    }

    public function getDataAttributes(PackOption $packOption): string
    {
        $html = '';
        foreach ($packOption->getData() as $key => $value) {
            if ($key === PackOptionInterface::PRODUCT_ID || $key === PackOptionInterface::SORT_ORDER) {
                continue;
            } elseif ($key === PackOptionInterface::PACKOPTION_ID) {
                $html .= sprintf(' value="%s"', $value);
            } else {
                $html .= sprintf(' data-%s="%s"', $key, $value);
            }
        }

        return $html;
    }

    /**
     * Render Discount
     */
    public function renderDiscount(PackOptionInterface $packOption): string
    {
        if ($packOption->getDiscountType() === 'fixed') {
            return __("%1 Off", $this->priceCurrency->format($packOption->getDiscountValue(), false))->render();
        } else {
            return __("%1 % Off", $packOption->getDiscountValue())->render();
        }
    }

    /**
     * Render From Array
     */
    public function renderDiscountFromArray(array $packOption): string
    {
        /** @var PackOption $packOptionModel */
        $packOptionModel = $this->packOptionFactory->create();
        $packOptionModel->setData($packOption);

        return $this->renderDiscount($packOptionModel);
    }

    /**
     * Render Price
     *
     * @throws NoSuchEntityException
     */
    public function renderPricePerUnit(PackOptionInterface $packOption): string
    {
        return $this->format($this->getPricePerUnit($packOption));
    }

    public function format(float $amount): string
    {
        return $this->priceCurrency->format($amount, false);
    }

    /**
     * Render Price
     *
     * @throws NoSuchEntityException
     */
    public function getPricePerUnit(PackOptionInterface $packOption): float
    {
        if (!($product = $this->getProduct()) instanceof Product) {
            $product = $this->productRepository->getById($this->request->getParam('id'))->getFinalPrice();
        }

        $price = $product->getFinalPrice();
        $specialPrice = $product->getSpecialPrice();

        if (!$specialPrice) {
            if ($packOption->getDiscountType() === 'fixed') {
                return $price - $packOption->getDiscountValue();
            }

            return (1 - ($packOption->getDiscountValue() / 100)) * $price;
        } else {
            switch ($this->scopeConfig->getValue(
                Price::XPATH_PRODUCT_PACK_CONFIG_SPECIAL_PRICE,
                StoreScopeInterface::SCOPE_STORE
            )) {
                case SpecialPriceCalculationType::USE_MIN_PRICE: {
                    return min(
                        (1 - ($packOption->getDiscountValue() / 100)) * $product->getPrice(),
                        (float)$specialPrice
                    );
                }
                case SpecialPriceCalculationType::USE_SPECIAL_PRICE:
                default: {
                    return (float)$product->getSpecialPrice();
                }
            }
        }
    }

    /**
     * Get Current Product From Registry
     */
    public function getProduct(): ?Product
    {
        return $this->coreRegistry->registry('product');
    }

    /**
     * Can Show an Option
     */
    public function canShowOption(PackOptionInterface $packOption): bool
    {
        $product = $this->getProduct();
        if ($product->isSaleable()) {
            $qty = $product->getQty();

            if ($qty === 0.0) {
                return true;
            }

            return $packOption->getPackSize() <= $qty;
        }

        return false;
    }

    /**
     * Get Product Item Options
     */
    public function getProductItemOptions(int $itemId): Collection
    {
        $collection = $this->itemOptionCollectionFactory->create();
        $collection->addFieldToFilter('item_id', ['eq' => $itemId]);
        return $collection;
    }

}
