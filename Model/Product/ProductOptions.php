<?php

namespace Kamlesh\Promotional\Model\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class ProductOptions implements OptionSourceInterface
{
    protected $productCollectionFactory;

    public function __construct(CollectionFactory $productCollectionFactory)
    {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function toOptionArray()
    {
        $options = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('name');

        foreach ($collection as $product) {
            $options[] = [
                'value' => $product->getId(),
                'label' => $product->getName()
            ];
        }

        return $options;
    }
}