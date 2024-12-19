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

namespace Qoliber\ProductPack\Observer\Product;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Qoliber\ProductPack\Api\Data\PackOptionInterface;
use Qoliber\ProductPack\Api\PackOptionRepositoryInterface;
use Qoliber\ProductPack\Model\PackOptionFactory;
use Qoliber\ProductPack\Model\ResourceModel\PackOption as PackOptionResource;

class SaveCommitAfter implements ObserverInterface
{
    protected PackOptionFactory $packOptionFactory;

    protected PackOptionResource $packOptionResource;

    protected PackOptionRepositoryInterface $packOptionRepository;

    private readonly SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param PackOptionFactory $packOptionFactory
     */
    public function __construct(
        PackOptionFactory $packOptionFactory,
        PackOptionResource $packOptionResource,
        PackOptionRepositoryInterface $packOptionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {

        $this->packOptionFactory = $packOptionFactory;
        $this->packOptionResource = $packOptionResource;
        $this->packOptionRepository = $packOptionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var $product Product */
        $product = $observer->getEvent()->getProduct();

        $packOptions = $product->getPackOptions();

        if (empty($packOptions)) {
            $packOptions = $product->getExtensionAttributes()->getPackOptions();
        }

        $savedIds = [];

        if (!empty($packOptions)) {
            foreach ($packOptions as $packOption) {
                unset($packOption['record_id']);
                if ($packOption[PackOptionInterface::PACKOPTION_ID] === '') {
                    $packOption[PackOptionInterface::PACKOPTION_ID] = null;
                }

                $packOption[PackOptionInterface::PRODUCT_ID] = $product->getId();
                $packOptionModel = $this->packOptionFactory->create();
                $packOptionModel->setData($packOption);
                $this->packOptionResource->save($packOptionModel);
                $savedIds[] = $packOptionModel->getId();
            }

            $this->searchCriteriaBuilder
                ->addFilter(PackOptionInterface::PRODUCT_ID, $product->getId())
                ->addFilter(PackOptionInterface::PACKOPTION_ID, $savedIds, 'nin');
            $optionsToRemove = $this->packOptionRepository->getList($this->searchCriteriaBuilder->create())->getItems();

            foreach ($optionsToRemove as $option) {
                $this->packOptionResource->delete($option);
            }
        }
    }
}
