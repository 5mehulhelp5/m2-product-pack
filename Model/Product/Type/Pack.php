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

namespace Qoliber\ProductPack\Model\Product\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Configuration\Item\Option\OptionInterface;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Type\Simple;
use Magento\Eav\Model\Config;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Psr\Log\LoggerInterface;
use Qoliber\ProductPack\Api\Data\PackOptionInterface;
use Qoliber\ProductPack\Api\PackOptionRepositoryInterface;
use Qoliber\ProductPack\Model\ResourceModel\PackOption as PackOptionResource;

class Pack extends Simple
{
    /** @var string  */
    public const TYPE_CODE = 'pack';

    protected PackOptionRepositoryInterface $packOptionRepository;

    protected PackOptionResource $packOptionResource;

    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param Json|null $serializer
     * @param UploaderFactory|null $uploaderFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Option                        $catalogProductOption,
        Config                        $eavConfig,
        Type                          $catalogProductType,
        ManagerInterface              $eventManager,
        Database                      $fileStorageDb,
        Filesystem                    $filesystem,
        Registry                      $coreRegistry,
        LoggerInterface               $logger,
        ProductRepositoryInterface    $productRepository,
        PackOptionRepositoryInterface $packOptionRepository,
        PackOptionResource            $packOptionResource,
        SearchCriteriaBuilder         $searchCriteriaBuilder,
        Json                          $serializer = null,
        UploaderFactory               $uploaderFactory = null
    ) {
        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository,
            $serializer,
            $uploaderFactory
        );
        $this->packOptionRepository = $packOptionRepository;
        $this->packOptionResource = $packOptionResource;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTypeSpecificData(Product $product)
    {
        $this->searchCriteriaBuilder->addFilter(PackOptionInterface::PRODUCT_ID, $product->getId());
        $packOptions = $this->packOptionRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        foreach ($packOptions as $packOption) {
            $this->packOptionResource->delete($packOption);
        }
    }

    /**
     * Default action to get weight of product
     *
     * @param Product $product
     * @return float
     */
    public function getWeight($product)
    {
        if ($product->hasCustomOptions()) {
            $packOption = $product->getCustomOption('pack_option');
            if ($packOption instanceof OptionInterface) {
                $packOption = $this->serializer->unserialize($packOption->getValue());
                $qty = $packOption['pack_size'];
                return ($product->getData('weight') * $qty) + $packOption['extra_weight'];
            }
        }

        return parent::getWeight($product);
    }

    /**
     * Prepare Product
     *
     * @param $product
     * @param $processMode
     */
    protected function _prepareProduct(DataObject $buyRequest, $product, $processMode): array
    {
        $product = parent::_prepareProduct($buyRequest, $product, $processMode);
        $product = array_shift($product);

        if ($buyRequest->getData('pack_option_hash')) {
            $product->addCustomOption('pack_option_hash', $buyRequest->getData('pack_option_hash'));
        }

        if ($buyRequest->getData('pack_option')) {
            $product->addCustomOption('pack_option', json_encode($buyRequest->getData('pack_option')));
        }

        return [$product];
    }

    /**
     * @param $product
     * @return true
     */
    public function isPossibleBuyFromList($product)
    {
        return true;
    }
}
