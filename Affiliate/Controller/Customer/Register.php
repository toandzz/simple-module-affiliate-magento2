<?php
namespace Mageplaza\Affiliate\Controller\Customer;

use Magento\Framework\App\Action\Context;

class Register extends \Magento\Framework\App\Action\Action
{
    protected $customerSession;
    protected $accountFactory;
    protected $helperData;
    protected $helperEmail;
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
        \Mageplaza\Affiliate\Helper\Data $helperData,
        \Mageplaza\Affiliate\Helper\Email $helperEmail

    )
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->accountFactory = $accountFactory;
        $this->helperData = $helperData;
        $this->helperEmail = $helperEmail;
    }

    public function execute()
    {
        $enableAffiliate = $this->helperData->getEnableAffiliate();
        if($enableAffiliate)
        {
            $customerId = $this->customerSession->getCustomerId();
            if(!$customerId){
                return $this->_redirect('customer/account/login');
            }
            $account = $this->accountFactory->create();
            $checkAffiliate = $account->load($customerId,'customer_id')->getData();
            if($checkAffiliate){
                $this->messageManager->addErrorMessage(__('You are already a Affiliate'));
                return $this->_redirect('affiliate/customer/index');
            }else{
                $codeLength = $this->helperData->getCodeLength();
                $code = $this->helperData->randomCode($codeLength);
                $data = [
                    'customer_id' => $customerId,
                    'code' => $code,
                    'status' => 1,
                    'balance' => 0
                ];
                $account->addData($data)->save();
                $this->messageManager->addSuccessMessage(__('You have become a Affiliate'));
                $this->helperEmail->sendEmailNewAccountCustomer();
                return $this->_redirect('affiliate/customer/index');
            }
        }else{
            return $this->_redirect('customer/account/');
        }
    }
}
