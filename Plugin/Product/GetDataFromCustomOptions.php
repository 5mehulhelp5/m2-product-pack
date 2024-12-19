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

namespace Qoliber\ProductPack\Plugin\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\Serialize\Serializer\Json;
use Qoliber\ProductPack\Model\Product\Type\Pack;

class GetDataFromCustomOptions
{
    protected Json $serializer;

    public function __construct(Json $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param $result
     * @param $key
     * @param $index
     * @return mixed
     */
    public function afterGetData(
        Product $product,
        $result,
        $key = '',
        $index = null
    ) {
        if ($product->getTypeId() !== Pack::TYPE_CODE
            || in_array($key, ['value', 'title'])) {
            return $result;
        }

        $customOption = $product->getCustomOption('pack_option');
        if (empty($customOption) || empty($customOption->getValue())) {
            return $result;
        }

        $packOption = $this->serializer->unserialize($customOption->getValue());

        return isset($packOption[$key]) && !empty($packOption[$key]) ?
            $packOption[$key] : $result;
    }
}
