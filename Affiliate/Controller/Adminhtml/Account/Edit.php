<?php

namespace Mageplaza\Affiliate\Controller\Adminhtml\Account;

use Magento\Backend\App\Action;

class Edit extends Action
{
    protected $_resultPageFactory;
    protected $_accountFactory;
    protected $_giftRegistry;
    const ADMIN_RESOURCE = 'Mageplaza_Affiliate::manageaccount_edit';

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_giftRegistry = $registry;
        $this->_accountFactory = $accountFactory;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('account_id');
        $account = $this->_accountFactory->create();
        $account->load($id);
        $this->_giftRegistry->register('account', $account);
        $_resultPage = $this->_resultPageFactory->create();
        if($id){
            $_resultPage->getConfig()->getTitle()->prepend(__('Edit Account'));
        }else{
            $_resultPage->getConfig()->getTitle()->prepend(__('New Account'));
        }
        return $_resultPage;
    }
}
