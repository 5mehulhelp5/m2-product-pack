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

class SpecialPriceCalculationType implements OptionSourceInterface
{
    public const USE_SPECIAL_PRICE = 'special';

    public const USE_MIN_PRICE = 'min';

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::USE_SPECIAL_PRICE, 'label' => __('Always use Special price')],
            ['value' => self::USE_MIN_PRICE, 'label' => __('Use Lower Price')]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::USE_SPECIAL_PRICE => __('Always use Special price'),
            self::USE_MIN_PRICE => __('Use Lower Price')
        ];
    }
}
