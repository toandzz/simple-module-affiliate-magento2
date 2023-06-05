<?php
namespace Mageplaza\Affiliate\Model\Total\Quote;

class Custom extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    protected $_priceCurrency;
    protected $helperData;
    protected $accountFactory;

    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Mageplaza\Affiliate\Helper\Data $helperData,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory
    )
    {
        $this->_priceCurrency = $priceCurrency;
        $this->helperData = $helperData;
        $this->accountFactory = $accountFactory;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);
        $applyDiscount = $this->helperData->getApplyDiscount();
        $affiliateCode = $quote->getAffiliateCode();
        $checkCode = $this->accountFactory->create()->load($affiliateCode,'code')->getId();

        if($applyDiscount != 'no' && $checkCode){
            $valueDiscount = $this->helperData->getDiscountValue();
            $subTotal = $total->getSubtotal();
            if($affiliateCode){
                $code = $affiliateCode;
            }
            if($code){
                if($applyDiscount == 'fixed'){
                    $baseDiscount = $valueDiscount;
                }
                else if($applyDiscount == 'percentage'){
                    $baseDiscount = $subTotal * $valueDiscount/100;
                }else{
                    $baseDiscount = 0;
                }
            }

            if($subTotal<$baseDiscount){
                $baseDiscount = $subTotal;
            }
            $discount =  $this->_priceCurrency->convert($baseDiscount);
            $total->addTotalAmount($this->getCode(), -$discount);
            $total->addBaseTotalAmount($this->getCode(), -$baseDiscount);
            $quote->setDiscount($discount);
            $total->setDiscount($discount);
            return $this;
        }
    }
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return [
            'code' => $this->getCode(),
            'title' => __('Affiliate Discount'),
            'value' => $total->getDiscount()
        ];
    }
}
