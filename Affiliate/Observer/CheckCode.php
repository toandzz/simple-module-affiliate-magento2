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

    public function __construct(
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
        \Magento\Framework\Message\ManagerInterface $_messageManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Mageplaza\Affiliate\Helper\Data $helperData
    )
    {
        $this->accountFactory = $accountFactory;
        $this->_messageManager = $_messageManager;
        $this->_redirect = $redirect;
        $this->actionFlag = $actionFlag;
        $this->_checkoutSession = $checkoutSession;
        $this->helperData = $helperData;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $code = trim($controller->getRequest()->getParam('coupon_code'));

        if($code){
            $account = $this->accountFactory->create();
            $accountId = $account->load($code,'code')->getId();
            if($accountId){
                foreach($account->getCollection()->getData() as $item){
                    if($item['code'] == $code){
                        $quote = $this->_checkoutSession->getQuote();
                        $quote->setAffiliateCode($code);
                        $quote->save();
                        $this->_messageManager->addSuccessMessage('Gift code applied successfully');
                    }
                }
                $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                $this->_redirect->redirect($controller->getResponse(),'checkout/cart');
            }
        }
        if ($controller->getRequest()->getParam('remove')) {
            $this->_checkoutSession->getQuote()->setAffiliateCode('')->save();
            $key = $this->helperData->getUrlKey();
            if(isset($_COOKIE[$key])){
                unset($_COOKIE[$key]);
                setcookie($key, null, -1, '/');
            }
            $this->_messageManager->addSuccessMessage('You canceled the affiliate code.');
        }

    }
}

