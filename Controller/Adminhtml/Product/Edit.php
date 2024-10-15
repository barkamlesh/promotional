<?php

namespace Kamlesh\Promotional\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Kamlesh\Promotional\Model\PromotionalFactory;

class Edit extends Action {

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var PromotionalFactory
     */
    protected $promotionalFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;
    /**
     * Constructor
     * 
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PromotionalFactory $promotionalFactory
     * @param Registry $coreRegistry
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        PromotionalFactory $promotionalFactory,
        Registry $coreRegistry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->promotionalFactory = $promotionalFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Kamlesh_Promotional::promotional');
    }

    /**
     * Edit action
     * 
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $model = $this->promotionalFactory->create();
        
        if ($id) {
            $model->load($id);
            
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This promotional product no longer exists.'));
                return $this->_redirect('promotion/index/index');
            }
        }

        $this->coreRegistry->register('promotional_product', $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Kamlesh_Promotional::promotional');
        $resultPage->addBreadcrumb(__('Promotional'), __('Promotional'));
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Promotional Product'));
        return $resultPage;
    }

}
