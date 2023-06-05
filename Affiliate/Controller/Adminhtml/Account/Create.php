<?php
namespace Mageplaza\Affiliate\Controller\Adminhtml\Account;

class Create extends \Magento\Backend\App\Action
{
    protected $resultForwardFactory = false;

    const ADMIN_RESOURCE = 'Mageplaza_Affiliate::manageaccount_create';
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    )
    {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    public function execute()
    {

        $resultForwardFactory = $this->resultForwardFactory->create();
        $this->_forward('edit');

        return $resultForwardFactory;

    }
}
