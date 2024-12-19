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

namespace Qoliber\ProductPack\Model\ResourceModel\PackOption;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Qoliber\ProductPack\Model\ResourceModel\PackOption;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Qoliber\ProductPack\Model\PackOption::class,
            PackOption::class
        );
    }
}
