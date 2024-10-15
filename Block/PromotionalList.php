<?php

namespace Kamlesh\Promotional\Block;

use Magento\Framework\View\Element\Template;
use Kamlesh\Promotional\Model\ResourceModel\Promotional\CollectionFactory;

class PromotionalList extends Template
{
    /**
     * @var string
     */
    protected $_template = "Kamlesh_Promotional::list.phtml";
    
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get collection
     *
     * @return \Kamlesh\Promotional\Model\ResourceModel\Promotional\Collection
     */
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

    /**
     * Get title
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return $this->getData('title') ?: __('Promotional Products');
    }

    /**
     * Get view mode
     *
     * @return string
     */
    public function getViewMode()
    {
        return $this->getData('view_mode') ?: 'grid';
    }
}