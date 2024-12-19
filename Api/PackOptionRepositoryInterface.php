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

namespace Qoliber\ProductPack\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Qoliber\ProductPack\Api\Data\PackOptionInterface;
use Qoliber\ProductPack\Api\Data\PackOptionSearchResultsInterface;

interface PackOptionRepositoryInterface
{

    /**
     * Save PackOption
     * @return PackOptionInterface
     * @throws LocalizedException
     */
    public function save(
        PackOptionInterface $packOption
    );

    /**
     * Retrieve PackOption
     * @param string $packoptionId
     * @return PackOptionInterface
     * @throws LocalizedException
     */
    public function get($packOptionId);

    /**
     * Retrieve PackOption matching the specified criteria.
     * @return PackOptionSearchResultsInterface
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete PackOption
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        PackOptionInterface $packOption
    );

    /**
     * Delete PackOption by ID
     * @param string $packoptionId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($packoptionId);
}
