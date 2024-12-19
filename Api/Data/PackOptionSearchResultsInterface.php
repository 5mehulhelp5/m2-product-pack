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

namespace Qoliber\ProductPack\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface PackOptionSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get PackOption list.
     * @return PackOptionInterface[]
     */
    public function getItems();

    /**
     * Set product_id list.
     * @param PackOptionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
