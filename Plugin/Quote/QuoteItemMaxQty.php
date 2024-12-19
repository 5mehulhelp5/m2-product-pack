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

declare(strict_types = 1);

namespace Qoliber\ProductPack\Plugin\Quote;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem as Subject;
use Magento\Quote\Model\Quote\Item;
use Qoliber\ProductPack\Model\Product\Type\Pack;

class QuoteItemMaxQty
{
    public function beforeInitialize(
        Subject            $subject,
        StockItemInterface $stockItem,
        Item               $quoteItem,
        $qty
    ): array {
        if ($quoteItem->getProduct()->getTypeInstance() instanceof Pack) {
            $multiplier = max(1, (int)$quoteItem->getBuyRequest()
                ->getDataByPath('pack_option/pack_size'));
            $qty = $multiplier * $qty;
        }

        return [$stockItem, $quoteItem, $qty];
    }
}
