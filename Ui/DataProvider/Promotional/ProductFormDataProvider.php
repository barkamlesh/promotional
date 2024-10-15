<?php

namespace Kamlesh\Promotional\Ui\DataProvider\Promotional;

use Kamlesh\Promotional\Model\ResourceModel\Promotional\CollectionFactory as PromotionalCollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class ProductFormDataProvider extends AbstractDataProvider
{
    /**
     * @var \Kamlesh\Promotional\Model\ResourceModel\Promotional\Collection
     */
    protected $collection;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param PromotionalCollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PromotionalCollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $items = $this->collection->getItems();
        $data = [];
        foreach ($items as $item) {
            $data[$item->getId()] = $item->getData();
        }

        return $data;
    }
}