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

namespace Qoliber\ProductPack\Plugin\Quote;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\Manager as MessageManager;
use Magento\InventorySales\Model\GetProductSalableQty;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManager;
use Qoliber\ProductPack\Model\Product\Type\Pack;

class AddProduct
{
    private readonly MessageManager $messageManager;

    private readonly StoreManager $storeManager;

    private readonly StockResolverInterface $stockResolver;

    private readonly GetProductSalableQty $productSalableQty;

    public function __construct(
        MessageManager $messageManager,
        StoreManager $storeManager,
        StockResolverInterface $stockResolver,
        GetProductSalableQty $productSalableQty
    ) {
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
        $this->stockResolver = $stockResolver;
        $this->productSalableQty = $productSalableQty;
    }

    /**
     * @param null $request
     * @param string $processMode
     * @return array
     * @throws LocalizedException
     */
    public function aroundAddProduct(
        Quote $subject,
        callable $proceed,
        Product $product,
        $request = null,
        $processMode = AbstractType::PROCESS_MODE_FULL
    ) {
        if ($product->getTypeId() === Pack::TYPE_CODE && $salableQty = $this->getSalableQty($product)) {
            $packSize = $this->getPackSize($request);
            if ($packSize > 1) {
                $qty = $request->getData('qty') ?? 1;
                if (!$this->canAddPackToCart($subject, $product, $packSize, $qty, $salableQty)) {
                    $errorMessage = __('The requested qty is not available');
                    $this->messageManager->addErrorMessage($errorMessage);
                    throw new LocalizedException($errorMessage);
                }
            }
        }

        return $proceed($product, $request, $processMode);
    }

    /**
     * @param $packSize
     * @param $qty
     * @param $salableQty
     */
    protected function canAddPackToCart(Quote $quote, Product $product, $packSize, $qty, $salableQty): bool
    {
        // temporary fix for dropshippers;
        return true;
    }

    /**
     * @throws LocalizedException|InputException|NoSuchEntityException
     */
    protected function getSalableQty(Product $product): float
    {
        $websiteCode = $this->storeManager->getWebsite()->getCode();
        $stock = $this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
        $stockId = $stock->getStockId();

        return $this->productSalableQty->execute($product->getSku(), $stockId);
    }

    /**
     * @param DataObject|null $request
     * @return int|mixed
     */
    protected function getPackSize(?DataObject $request)
    {
        if ($request instanceof DataObject) {
            $packOption = $request->getData('pack_option') ?? [];
            return $packOption['pack_size'] ?? 1;
        }

        return 1;
    }
}
