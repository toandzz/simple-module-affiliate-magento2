<?php
namespace Mageplaza\Affiliate\Plugin;

class AffiliateCode{
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->checkoutSession = $checkoutSession;
    }
    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $subject, $result){
        $quote = $this->checkoutSession->getQuote();
        $affiliateCode = $quote->getAffiliateCode();
        if($code = $affiliateCode) {
            return $code;
        }
        return $result;
    }
}
