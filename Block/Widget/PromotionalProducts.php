<?php

namespace Kamlesh\Promotional\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Kamlesh\Promotional\Model\ResourceModel\Promotional\CollectionFactory;

class PromotionalProducts extends Template implements BlockInterface
{
    protected $_template = "Kamlesh_Promotional::list.phtml";
    protected $collectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    public function getCollection()
    {
        $collection = $this->collectionFactory->create();
        $collection->getSelect()->joinLeft(
            ['cp' => $collection->getTable('catalog_product_entity')],
            'main_table.product_id = cp.entity_id',
            ['sku']
        )->joinLeft(
            ['cpv' => $collection->getTable('catalog_product_entity_varchar')],
            'main_table.product_id = cpv.entity_id AND cpv.attribute_id = (SELECT attribute_id FROM ' . $collection->getTable('eav_attribute') . ' WHERE attribute_code = "name" AND entity_type_id = (SELECT entity_type_id FROM ' . $collection->getTable('eav_entity_type') . ' WHERE entity_type_code = "catalog_product"))',
            ['name' => 'value']
        )->joinLeft(
            ['cpd' => $collection->getTable('catalog_product_entity_decimal')],
            'main_table.product_id = cpd.entity_id AND cpd.attribute_id = (SELECT attribute_id FROM ' . $collection->getTable('eav_attribute') . ' WHERE attribute_code = "price" AND entity_type_id = (SELECT entity_type_id FROM ' . $collection->getTable('eav_entity_type') . ' WHERE entity_type_code = "catalog_product"))',
            ['price' => 'value']
        )
        ->where('main_table.status = ?', 1);;
        return $collection;
    }

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function getViewMode()
    {
        return $this->getData('view_mode');
    }
}