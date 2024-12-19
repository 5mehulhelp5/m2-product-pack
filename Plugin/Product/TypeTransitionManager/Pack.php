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

namespace Qoliber\ProductPack\Plugin\Product\TypeTransitionManager;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\TypeTransitionManager;

class Pack
{
    /**
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundProcessProduct(
        TypeTransitionManager $subject,
        callable $proceed,
        Product $product
    ) {
        $packOptions = $product->getPackOptions();
        if (!empty($packOptions)) {
            $product->setTypeId(\Qoliber\ProductPack\Model\Product\Type\Pack::TYPE_CODE);
            return;
        }

        $proceed($product);
    }
}
