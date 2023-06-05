<?php
namespace Mageplaza\Affiliate\Controller\Adminhtml\Account;

use Magento\Backend\App\Action;

class Delete extends Action
{
    protected $_accountFactory;
    const ADMIN_RESOURCE = 'Mageplaza_Affiliate::manageaccount_delete';
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory
    )
    {
        $this->_accountFactory = $accountFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $account = $this->_accountFactory->create();
        $param = $this->getRequest()->getParam('account_id');
        $account->load($param);
        if ($account->getId()){
            $account->delete();
        }
        $this->_redirect('affiliate/account/index');

    }
}
