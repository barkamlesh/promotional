<?php

namespace Kamlesh\Promotional\Model\Indexer;

use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Elasticsearch\ClientBuilder;
use Kamlesh\Promotional\Model\ResourceModel\Promotional\CollectionFactory as PromotionalProductCollectionFactory;


class PromotionalProductIndexer implements IndexerActionInterface, MviewActionInterface
{
    protected $collectionFactory;
    protected $client;

    public function __construct(
        PromotionalProductCollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->client = ClientBuilder::create()->build(); // Initialize Elasticsearch client
    }

    /**
     * Execute full reindex (for Magento's indexing system)
     */
    public function executeFull()
    {
        $this->reindexAll();
    }

    /**
     * Execute full index for all rows (For 'execute' method compatibility)
     */
    public function execute($ids = null)
    {
        // If no specific IDs are passed, reindex everything
        if ($ids === null) {
            $this->executeFull();
        } else {
            $this->executeList($ids);
        }
    }

    /**
     * Execute partial index for single product
     */
    public function executeRow($productId)
    {
        $this->reindexProduct($productId);
    }

    /**
     * Execute partial index for multiple products
     */
    public function executeList(array $ids)
    {
        foreach ($ids as $id) {
            $this->reindexProduct($id);
        }
    }

    /**
     * Reindex all promotional products
     */
    protected function reindexAll()
    {
        $collection = $this->collectionFactory->create();
        foreach ($collection as $product) {
            $this->indexProduct($product);
        }
    }

    /**
     * Reindex a single product
     */
    protected function reindexProduct($productId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('product_id', $productId);

        $product = $collection->getFirstItem();
        if ($product->getId()) {
            $this->indexProduct($product);
        }
    }

    /**
     * Index a product to Elasticsearch
     */
    protected function indexProduct($product)
    {
        $params = [
            'index' => 'promotional_products',  // Elasticsearch index name
            'id'    => $product->getProductId(), // Document ID in Elasticsearch
            'body'  => [
                'product_id'          => $product->getProductId(),
                'discount_percentage' => $product->getDiscountPercentage(),
                'start_date'          => $product->getStartDate(),
                'end_date'            => $product->getEndDate(),
                'status'              => $product->getStatus(),
            ]
        ];

        $response = $this->client->index($params);

        if ($response['result'] !== 'created' && $response['result'] !== 'updated') {
            // Handle failure if not created or updated
            throw new \Exception('Failed to index product: ' . $product->getProductId());
        }
    }
}