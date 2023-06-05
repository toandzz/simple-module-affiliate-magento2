<?php
namespace Mageplaza\Affiliate\Plugin;

class AffiliateCode{
    protected $accountFactory;
    protected $helperData;
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
        \Mageplaza\Affiliate\Helper\Data $helperData
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->accountFactory = $accountFactory;
        $this->helperData =$helperData;
    }
    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $subject, $result){
        $quote = $this->checkoutSession->getQuote();
        $affiliateCode = $quote->getAffiliateCode();
        $affiliateStatus = $this->accountFactory->create()->load($affiliateCode,'code')->getData('status');
        $key = $this->helperData->getUrlKey();
        if($affiliateStatus == 1){
            if($code = $affiliateCode) {
                return $code;
            }
        }
        else{
            $this->helperData->deleteCookie($key);
            $quote->setAffiliateCode('')->save();
        }
        return $result;
    }
}
