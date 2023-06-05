<?php
namespace Mageplaza\Affiliate\Controller\Refer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $customerSession;
    protected $accountFactory;
    protected $helperData;
    protected $checkoutSession;


    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
        \Mageplaza\Affiliate\Helper\Data $helperData,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->accountFactory = $accountFactory;
        $this->helperData = $helperData;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        $enableAffiliate = $this->helperData->getEnableAffiliate();
        if($enableAffiliate){
            $key = $this->helperData->getUrlKey();
            $code = $this->getRequest()->getParam($key);
            $customerId = $this->customerSession->getCustomerId();

            $account = $this->accountFactory->create()->load($customerId, 'customer_id')->getData();

            if(($account && $account['code'] != $code) || !$account){
                $cookieValue = $code;
                setcookie($key,$cookieValue, time() + (86400 * 365), '/');
                $this->messageManager->addSuccessMessage(__('Refer Link success'));
                $this->checkoutSession->getQuote()->setAffiliateCode($code)->save();
                return $this->_redirect('checkout/cart/');
            }
            else{
                $this->messageManager->addErrorMessage('The referrer link is not applicable to the affiliate that created it');
                return $this->_redirect('customer/account/');
            }
        }else{
            return $this->_redirect('customer/account/');
        }
    }
}
