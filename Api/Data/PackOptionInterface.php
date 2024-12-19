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

use Magento\Framework\Api\ExtensibleDataInterface;

interface PackOptionInterface extends ExtensibleDataInterface
{
    public const PACKOPTION_ID = 'packoption_id';

    public const PRODUCT_ID = 'product_id';

    public const PACKAGE_NAME = 'package_name';

    public const DISCOUNT_TYPE = 'discount_type';

    public const DISCOUNT_VALUE = 'discount_value';

    public const EXTRA_WEIGHT = 'extra_weight';

    public const PACK_SIZE = 'pack_size';

    public const SORT_ORDER = 'sort_order';

    public const IS_DELETE = 'is_delete';

    public const HASH = 'hash';

    /**
     * Get packoption_id
     * @return int
     */
    public function getPackoptionId();

    /**
     * @param int $packoptionId
     * @return $this
     */
    public function setPackoptionId($packoptionId);

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * @return string
     */
    public function getFrontendTitle(string $localeCode = 'pl_PL');

    /**
     * @return int
     */
    public function getPackageName();

    /**
     * @param int $typeId
     * @return $this
     */
    public function setPackageName($packageName);

    /**
     * @return string
     */
    public function getDiscountType();

    /**
     * @param string $discountType
     * @return $this
     */
    public function setDiscountType($discountType);

    /**
     * @return float
     */
    public function getDiscountValue();

    /**
     * @param float $discountValue
     * @return $this
     */
    public function setDiscountValue($discountValue);

    /**
     * @return float
     */
    public function getExtraWeight();

    /**
     * @param float $extraWeight
     * @return $this
     */
    public function setExtraWeight($extraWeight);

    /**
     * @return int
     */
    public function getPackSize();

    /**
     * @param int $packSize
     * @return $this
     */
    public function setPackSize($packSize);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * @return bool
     */
    public function isDelete();

    /**
     * @param bool $isDelete
     * @return $this
     */
    public function setIsDelete($isDelete);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Qoliber\ProductPack\Api\Data\PackOptionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @return $this
     */
    public function setExtensionAttributes(
        PackOptionExtensionInterface $extensionAttributes
    );
}
