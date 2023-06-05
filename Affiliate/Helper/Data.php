<?php

namespace Mageplaza\Affiliate\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_AFFILIATE = 'affiliate/';

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null)
    {

        return $this->getConfigValue(self::XML_PATH_AFFILIATE . 'general/' . $code, $storeId);
    }

    public function getEnableAffiliate(){
        return $this->getGeneralConfig('enable_affiliate');
    }

    public function getRegisterBlock(){
        return $this->getGeneralConfig('select_register_static_block');
    }

    public function getCodeLength(){
        return $this->getGeneralConfig('code_length');
    }
    public function getUrlKey(){
        return $this->getGeneralConfig('url_key');
    }

    public function getAffiliateRuleConfig($code, $storeId = null)
    {

        return $this->getConfigValue(self::XML_PATH_AFFILIATE . 'affiliate_rule/' . $code, $storeId);
    }

    public function getApplyDiscount(){
        return $this->getAffiliateRuleConfig('apply_discount_to_customer');
    }
    public function getDiscountValue(){
        return $this->getAffiliateRuleConfig('discount_value');
    }
    public function getCommissionType(){
        return $this->getAffiliateRuleConfig('commission_type');
    }
    public function getCommissionValue(){
        return $this->getAffiliateRuleConfig('commission_value');
    }

    public function randomCode($length){
        $char = "abcdefghijklmlopqrstuvxyz0123456789";
        $size = strlen($char);
        $code = "";
        for($i = 0; $i<$length; $i++){
            $code .= $char[rand(0, $size - 1)];
        }
        return $code;
    }

    public function deleteCookie($key){
        unset($_COOKIE[$key]);
        setcookie($key, null, -1, '/');
    }
}
