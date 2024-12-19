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

namespace Qoliber\ProductPack\Model\PackOptionType;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /** @var  */
    protected $collection;

    protected DataPersistorInterface $dataPersistor;

    /** @var  */
    protected $loadedData;

    /** @var  */
    protected $type;

    protected StoreManagerInterface $storeManager;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $_data = $model->getData();
            $this->loadedData[$model->getId()] = $_data;
        }

        return $this->loadedData;
    }
}
