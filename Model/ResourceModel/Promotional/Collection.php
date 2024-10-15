<?php

namespace Kamlesh\Promotional\Model\ResourceModel\Promotional;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Kamlesh\Promotional\Model\Promotional as Model;
use Kamlesh\Promotional\Model\ResourceModel\Promotional as ResourceModel;

class Collection extends AbstractCollection
{
    /**
     * Define model and resource model
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }

    // protected function _initSelect()
    // {
    //     parent::_initSelect();
        
    //     // Join with the foreign key table
    //     $this->getSelect()->joinLeft(
    //         ['cp' => $this->getTable('catalog_product_entity')],
    //         'main_table.product_id = cp.entity_id',
    //         ['sku']
    //     )->joinLeft(
    //         ['cpv' => $this->getTable('catalog_product_entity_varchar')],
    //         'main_table.product_id = cpv.entity_id AND cpv.attribute_id = (SELECT attribute_id FROM eav_attribute WHERE attribute_code = "name" AND entity_type_id = (SELECT entity_type_id FROM eav_entity_type WHERE entity_type_code = "catalog_product"))',
    //         ['name' => 'value']
    //     )->joinLeft(
    //         ['cpd' => $this->getTable('catalog_product_entity_decimal')],
    //         'main_table.product_id = cpd.entity_id AND cpd.attribute_id = (SELECT attribute_id FROM eav_attribute WHERE attribute_code = "price" AND entity_type_id = (SELECT entity_type_id FROM eav_entity_type WHERE entity_type_code = "catalog_product"))',
    //         ['price' => 'value']
    //     );

    //     return $this;
    // }
}
