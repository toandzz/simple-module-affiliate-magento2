<?php
namespace Mageplaza\Affiliate\Controller\Customer;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action {
    protected $resultPageFactory;
    protected $customer;
    protected $helperData;
    protected $accountFactory;
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customer,
        \Mageplaza\Affiliate\Helper\Data $helperData,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->customer = $customer;
        $this->helperData = $helperData;
        $this->accountFactory = $accountFactory;
        parent::__construct($context);
    }

    public function execute() {
        $enable = $this->helperData->getEnableAffiliate();
        $account = $this->accountFactory->create();
        $customerId = $this->customer->getCustomerId();
        $status = $account->load($customerId,'customer_id')->getData('status');
        $accountId = $account->load($customerId,'customer_id')->getData('account_id');
        if($enable == 1){
            if(!$customerId){
                return $this->_redirect('customer/account/login');
            }
            if($status == 0 && $accountId){
                $this->messageManager->addErrorMessage(__('Affiliate inactive'));
                return $this->_redirect('customer/account/');
            }
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(__('My Affiliate'));
            return $resultPage;
        }else{
            return $this->_redirect('customer/account/');
        }
    }
}
