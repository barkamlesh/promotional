<?php

namespace Kamlesh\Promotional\Ui\DataProvider\Promotional;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Kamlesh\Promotional\Model\ResourceModel\Promotional\CollectionFactory as PromotionalCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;

class ProductDataProvider extends AbstractDataProvider
{
    /**
     * @var \Kamlesh\Promotional\Model\ResourceModel\Promotional\Collection
     */
    protected $loadedData;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param PromotionalCollectionFactory $collectionFactory
     * @param Registry $coreRegistry
     * @param ProductCollectionFactory $productCollectionFactory
     * @param LoggerInterface $logger
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Kamlesh\Promotional\Model\ResourceModel\Promotional\CollectionFactory $collectionFactory,
        Registry $coreRegistry,
        ProductCollectionFactory $productCollectionFactory,
        LoggerInterface $logger,
        array $meta = [],
        array $data = []
    ) {
        
        $this->collection = $collectionFactory->create();
        $this->coreRegistry = $coreRegistry;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->logger = $logger;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $this->logger->info('ProductDataProvider getData called');
        $model = $this->coreRegistry->registry('promotional_product');

        if ($model) {
            $data = [];
            $data[$model->getId()] = $model->getData();
            $this->logger->info('ProductDataProvider getData edit mode: ' . json_encode($data));
            return $data;
        }

        // Fetch data for the grid
        $collection = $this->getCollection();
        
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
        );


        $items = $collection->getItems();
        $data = [];
        foreach ($items as $item) {
            $data[$item->getId()] = $item->getData();
        }
        
        $this->logger->info('ProductDataProvider getData grid mode: ' . json_encode($data));

        return [
            'totalRecords' => $collection->getSize(),
            'items' => array_values($data),
        ];
    }

    
}
