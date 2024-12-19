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

namespace Qoliber\ProductPack\Plugin\GetItemsToDeductFromShipment;

use Magento\InventoryShipping\Model\GetItemsToDeductFromShipment;
use Magento\InventorySourceDeductionApi\Model\ItemToDeductInterface;
use Magento\InventorySourceDeductionApi\Model\ItemToDeductInterfaceFactory;
use Magento\Sales\Model\Order\Shipment;
use Qoliber\ProductPack\Model\Product\Type\Pack;

class Execute
{
    private readonly ItemToDeductInterfaceFactory $itemToDeduct;

    /**
     * @param ItemToDeductInterfaceFactory $itemToDeduct
     */
    public function __construct(ItemToDeductInterfaceFactory $itemToDeduct)
    {
        $this->itemToDeduct = $itemToDeduct;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(GetItemsToDeductFromShipment $subject, callable $proceed, Shipment $shipment): array
    {
        $itemsToDeduct = $proceed($shipment);
        $packSizes = [];

        /** @var Shipment\Item $shipmentItem */
        foreach ($shipment->getAllItems() as $shipmentItem) {
            $orderItem = $shipmentItem->getOrderItem();
            if ($orderItem->getProductType() === Pack::TYPE_CODE) {
                $packOption = $orderItem->getBuyRequest()->getData('pack_option');
                $packSize = $packOption['pack_size'];
                if ($packSize > 1) {
                    $packSizes[$orderItem->getSku()] = $packSize;
                }
            }
        }

        foreach ($packSizes as $sku => $qty) {
            /** @var ItemToDeductInterface $item */
            foreach ($itemsToDeduct as $i => $item) {
                if ($item->getSku() === $sku) {
                    $itemsToDeduct[$i] = $this->itemToDeduct->create([
                        'sku' => $sku,
                        'qty' => $item->getQty() * $qty
                    ]);
                }
            }
        }

        return $itemsToDeduct;
    }
}
