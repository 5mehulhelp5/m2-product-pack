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

namespace Qoliber\ProductPack\Plugin\GetItemsToCancelFromOrderItem;

use Magento\InventorySales\Model\GetItemsToCancelFromOrderItem;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterface;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory;
use Magento\Sales\Model\Order\Item as OrderItem;
use Qoliber\ProductPack\Model\Product\Type\Pack;

class Execute
{
    private readonly ItemToSellInterfaceFactory $itemToSell;

    /**
     * @param ItemToSellInterfaceFactory $itemToSell
     */
    public function __construct(ItemToSellInterfaceFactory $itemToSell)
    {
        $this->itemToSell = $itemToSell;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(GetItemsToCancelFromOrderItem $subject, callable $proceed, OrderItem $orderItem): array
    {
        $itemsToCancel = $proceed($orderItem);

        if ($orderItem->getProductType() === Pack::TYPE_CODE) {
            $packOption = $orderItem->getBuyRequest()->getData('pack_option');
            $packSize = $packOption['pack_size'];
            if ($packSize > 1) {
                /** @var ItemToSellInterface $item */
                foreach ($itemsToCancel as $i => $item) {
                    if ($item->getSku() === $orderItem->getSku()) {
                        $itemsToCancel[$i] = $this->itemToSell->create([
                            'sku' => $item->getSku(),
                            'qty' => $item->getQuantity() * $packSize
                        ]);
                    }
                }
            }
        }

        return $itemsToCancel;
    }
}
