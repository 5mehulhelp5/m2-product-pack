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

namespace Qoliber\ProductPack\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product\Type as CatalogType;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\DataProvider\Modifier\ModifierFactory;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Qoliber\ProductPack\Model\Product\Type\Pack;

class Composite extends AbstractModifier
{
    /** @var string  */
    const CHILDREN_PATH = 'product_attachment/children';

    /** @var string  */
    const CONTAINER_ATTACHMENTS = 'container_attachments';

    /** @var string  */
    const CONFIGURABLE_TYPE_CODE = 'configurable';

    /** @var string  */
    const GROUPED_TYPE_CODE = 'grouped';

    private readonly array $modifiers;

    private readonly LocatorInterface $locator;

    private readonly ModifierFactory $modifierFactory;

    private array $modifiersInstances = [];

    public function __construct(
        LocatorInterface $locator,
        ModifierFactory $modifierFactory,
        array $modifiers = []
    ) {
        $this->locator = $locator;
        $this->modifierFactory = $modifierFactory;
        $this->modifiers = $modifiers;
    }

    /**
     * @return array $data
     */
    public function modifyData(array $data) : array
    {
        if ($this->canShowPackPanel()) {
            foreach ($this->getModifiers() as $modifier) {
                $data = $modifier->modifyData($data);
            }
        }

        return $data;
    }

    /**
     * @return array $meta
     */
    public function modifyMeta(array $meta) : array
    {
        if ($this->canShowPackPanel()) {
            foreach ($this->getModifiers() as $modifier) {
                $meta = $modifier->modifyMeta($meta);
            }
        }

        return $meta;
    }

    /**
     * @return ModifierInterface[]
     */
    private function getModifiers() : array
    {
        if ($this->modifiersInstances === []) {
            foreach ($this->modifiers as $modifierClass) {
                $this->modifiersInstances[$modifierClass] = $this->modifierFactory->create($modifierClass);
            }
        }

        return $this->modifiersInstances;
    }

    private function canShowPackPanel() : bool
    {
        $productTypes = [
            CatalogType::TYPE_SIMPLE,
            Pack::TYPE_CODE,
        ];

        return in_array((string) $this->locator->getProduct()->getTypeId(), $productTypes, true);
    }
}
