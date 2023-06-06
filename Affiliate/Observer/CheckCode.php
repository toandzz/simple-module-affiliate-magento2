<?php
namespace Mageplaza\Affiliate\Observer;

class CheckCode implements \Magento\Framework\Event\ObserverInterface
{
    protected $accountFactory;
    protected $_messageManager;
    protected $_redirect;
    protected $actionFlag;
    protected $_checkoutSession;
    protected $helperData;
    protected $customerSession;

    public function __construct(
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
        \Magento\Framework\Message\ManagerInterface $_messageManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Mageplaza\Affiliate\Helper\Data $helperData,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->accountFactory = $accountFactory;
        $this->_messageManager = $_messageManager;
        $this->_redirect = $redirect;
        $this->actionFlag = $actionFlag;
        $this->_checkoutSession = $checkoutSession;
        $this->helperData = $helperData;
        $this->customerSession = $customerSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $code = trim($controller->getRequest()->getParam('coupon_code'));

        if($code){
            $account = $this->accountFactory->create();
            $accountId = $account->load($code,'code')->getId();
            $customerId = $this->customerSession->getCustomerId();
            $affiliateCode = $account->load($customerId, 'customer_id')->getData('code');
            if($accountId && $code != $affiliateCode){
                foreach($account->getCollection()->getData() as $item){
                    if($item['code'] == $code){
                        $quote = $this->_checkoutSession->getQuote();
                        $quote->setAffiliateCode($code);
                        $quote->save();
                        $this->_messageManager->addSuccessMessage('Gift code applied successfully');
                    }
                }
            }else{
                $this->_messageManager->addErrorMessage('Gift code is not applicable to the affiliate that created it');
            }
            $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            $this->_redirect->redirect($controller->getResponse(),'checkout/cart');
        }
        if ($controller->getRequest()->getParam('remove')) {
            $this->_checkoutSession->getQuote()->setAffiliateCode('')->save();
            $key = $this->helperData->getUrlKey();
            if(empty($this->_checkoutSession->getQuote()->getAffiliateCode())){
                if(isset($_COOKIE[$key])){
                    $this->helperData->deleteCookie($key);
                }
            }
            $this->_messageManager->addSuccessMessage('You canceled the affiliate code.');
        }

    }
}

