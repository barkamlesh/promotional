<?php

namespace Kamlesh\Promotional\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Promotional extends AbstractDb
{
    /**
     * Define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('promotional_products', 'entity_id');
    }
}