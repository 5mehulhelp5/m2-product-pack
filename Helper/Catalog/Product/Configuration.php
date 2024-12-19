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

namespace Qoliber\ProductPack\Helper\Catalog\Product;

use Magento\Catalog\Helper\Product\Configuration\ConfigurationInterface;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

class Configuration implements ConfigurationInterface
{
    protected \Magento\Catalog\Helper\Product\Configuration $productConfiguration;

    public function __construct(
        \Magento\Catalog\Helper\Product\Configuration $productConfiguration
    ) {
        $this->productConfiguration = $productConfiguration;
    }

    /**
     * Get Options
     *
     * @return string[]
     */
    public function getOptions(ItemInterface $item): array
    {
        $buyRequest = $item->getOptionByCode('info_buyRequest');
        $packOption = json_decode((string) $buyRequest->getValue(), true);

        if ($packOption['pack_option'] ?? []) {
            return array_merge(
                [
                    [
                        'label' => __('Package'),
                        'value' => $packOption['pack_option']['title']
                    ],
                    [
                        'label' => __('Units'),
                        'value' => $packOption['pack_option']['pack_size']
                    ]
                ],
                $this->productConfiguration->getCustomOptions($item)
            );
        } else {
            return [];
        }
    }
}
