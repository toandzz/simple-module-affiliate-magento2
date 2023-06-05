<?php
namespace Mageplaza\Affiliate\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mageplaza\Affiliate\Model\AccountFactory;
use Mageplaza\Affiliate\Model\CustomerEntityFactory;

abstract class InitAccount extends \Magento\Backend\App\Action
{
    public $accountFactory;
    public $customerEntityFactory;
    public $_coreRegistry;

    public function __construct(
        Context $context,
        AccountFactory $accountFactory,
        Registry $coreRegistry,
        CustomerEntityFactory $customerEntityFactory
    )
    {
        $this->accountFactory = $accountFactory;
        $this->customerEntityFactory = $customerEntityFactory;
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    protected function initAccount(){
        $accountId = $this->getRequest()->getParam('account_id');

        $account = $this->accountFactory->create();

        if($accountId){
            $account->load($accountId);

            if(!$account->getId()){
                $this->messageManager->addErrorMessage(__('This code longer exists.'));
                return false;
            }

        }

        return $account;
    }

    protected function initCustomer(){
        $customer = $this->customerEntityFactory->create();
        $id = $this->getRequest()->getParam('customer_id');
        $customerId = $customer->load($id,'entity_id')->getId();
        if($customerId){
            return $customerId;
        }
        return false;
    }

    protected function initCode(){
        $account = $this->accountFactory->create();
        $code = $this->getRequest()->getParam('code');
        $customerCode = $account->load($code,'code')->getData('code');
        if($customerCode){
            return $customerCode;
        }
        return false;
    }
    protected function initAccountAffiliate(){
        $customerId = $this->getRequest()->getParam('customer_id');
        $account = $this->accountFactory->create();
        $accountId = $account->load($customerId,'customer_id')->getId();
        if(empty($accountId)){
            return false;
        }
        return true;
    }

}
