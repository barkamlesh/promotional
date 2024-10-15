<?php

namespace Kamlesh\Promotional\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Kamlesh\Promotional\Model\PromotionalFactory;
use Magento\Framework\Exception\LocalizedException;
use Kamlesh\Promotional\Model\Producer\ProductSaveAfterPublisher;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use \Magento\Framework\Pricing\PriceCurrencyInterface;
use Psr\Log\LoggerInterface;

class Save extends Action
{    
    /**
     * @var PromotionalFactory
     */
    protected $promotionalFactory;

    /**
     * @var ProductSaveAfterPublisher
     */
    protected $publisher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;
    
    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param PromotionalFactory $promotionalFactory
     * @param ProductSaveAfterPublisher $publisher
     * @param ProductRepositoryInterface $productRepository
     * @param StockRegistryInterface $stockRegistry
     * @param PriceCurrencyInterface $priceCurrency
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        PromotionalFactory $promotionalFactory,
        ProductSaveAfterPublisher $publisher,
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        PriceCurrencyInterface $priceCurrency,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->promotionalFactory = $promotionalFactory;
        $this->publisher = $publisher;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->priceCurrency = $priceCurrency;
        $this->logger = $logger;
    }

    /**
     * Check if user has permissions to access this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Kamlesh_Promotional::promotional');
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $id = $this->getRequest()->getParam('id');
            $model = $this->promotionalFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This promotional product no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();

                // Prepare message data
                $messageData = $this->prepareMessageData($model);
                
                // Publish message data and log to specific log file
                
                $this->logger->info('Publishing message data:', $messageData);
                $this->publisher->publish($messageData);

                $this->messageManager->addSuccessMessage(__('You saved the promotional product.'));
                return $resultRedirect->setPath('*/*/index'); // Redirect to the list page
                //return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId()]);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }


    /**
     * Prepare message data
     *
     * @param \Vendor\Module\Model\Promotional $model
     * @return array
     */
    private function prepareMessageData($model)
    {
        try {
            // Fetch product data
            $productId = $model->getProductId();
            $product = $this->productRepository->getById($productId);

            // Fetch stock data
            $stockItem = $this->stockRegistry->getStockItem($productId);

            // Fetch original price and discount percentage
            $originalPrice = $product->getPrice();
            $discountPercentage = $model->getDiscountPercentage();

            // Calculate discounted price
            $discountedPrice = $originalPrice - ($originalPrice * ($discountPercentage / 100));

            // Prepare message data
            return [
                'product_id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'price' => $this->priceCurrency->convertAndFormat($originalPrice, false, 2),
                'discounted_price' => $this->priceCurrency->convertAndFormat($discountedPrice, false, 2),
                'qty' => $stockItem->getQty(),
                'promotion' => [
                    'id' => $model->getId(),
                    'name' => $model->getName(),
                    'type' => 'discount',
                    'value' => $discountPercentage,
                    'start_date' => $model->getStartDate(),
                    'end_date' => $model->getEndDate(),
                    'status' => $model->getStatus(),
                ],
                'categories' => $product->getCategoryIds(),
                'attributes' => [
                    'color' => $product->getCustomAttribute('color') ? $product->getCustomAttribute('color')->getValue() : null,
                    'size' => $product->getCustomAttribute('size') ? $product->getCustomAttribute('size')->getValue() : null,
                ],
                'visibility' => $this->getVisibilityLabels($product->getVisibility()),
                'is_in_stock' => $stockItem->getIsInStock(),
                'updated_at' => $product->getUpdatedAt(),
            ];

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->messageManager->addErrorMessage(__('An error occurred while saving the promotion.'));
        }
    }


    /**
     * Get visibility labels
     *
     * @param int $visibility
     * @return array
     */
    private function getVisibilityLabels($visibility)
    {
        $visibilityLabels = [];
        switch ($visibility) {
            case \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH:
                $visibilityLabels = ['catalog', 'search'];
                break;
            case \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG:
                $visibilityLabels = ['catalog'];
                break;
            case \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH:
                $visibilityLabels = ['search'];
                break;
            case \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE:
                $visibilityLabels = [];
                break;
        }
        return $visibilityLabels;
    }

    
}
