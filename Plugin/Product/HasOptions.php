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

namespace Qoliber\ProductPack\Plugin\Product;

use Magento\Catalog\Model\Product;
use Qoliber\ProductPack\Model\Product\Type\Pack;

class HasOptions
{
    /**
     * @param string $key
     * @param null $index
     * @return string
     */
    public function aroundGetData(Product $subject, callable $proceed, $key = '', $index = null)
    {
        if ($subject->getTypeId() === Pack::TYPE_CODE && ($key === 'has_options' || $key === 'required_options')) {
            return '1';
        }

        return $proceed($key, $index);
    }
}
