<?php

namespace Kamlesh\Promotional\Model;

use Magento\Framework\Model\AbstractModel;
use Kamlesh\Promotional\Model\ResourceModel\Promotional as ResourceModel;

class Promotional extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}