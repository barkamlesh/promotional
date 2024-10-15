<?php

namespace Kamlesh\Promotional\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Kamlesh\Promotional\Model\ResourceModel\Promotional\CollectionFactory;

class PromotionalList extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $collection = $this->collectionFactory->create();
        $resultPage->getLayout()->getBlock('promotional.list')->setCollection($collection);
        return $resultPage;
    }
}