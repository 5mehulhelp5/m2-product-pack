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

namespace Qoliber\ProductPack\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class OptionDisplayType implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'discount', 'label' => __('Discount')],
            ['value' => 'price', 'label' => __('Price')]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'discount' => __('Discount'),
            'price' => __('Price')
        ];
    }
}
