<?php

namespace Kamlesh\Promotional\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Kamlesh\Promotional\Model\PromotionalFactory;

class Delete extends Action
{
    /**
     * @var PromotionalFactory
     */
    protected $promotionalFactory;

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param PromotionalFactory $promotionalFactory
     */
    public function __construct(
        Action\Context $context,
        PromotionalFactory $promotionalFactory
    ) {
        parent::__construct($context);
        $this->promotionalFactory = $promotionalFactory;
    }

    /**
     * Check if user has permissions to access this controller
     *
     * @return bool
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->promotionalFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('The promotional product has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a promotional product to delete.'));
        return $resultRedirect->setPath('*/*/');
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
}