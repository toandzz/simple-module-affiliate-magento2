define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils'
    ],
    function ($,Component,totals,quote,priceUtils) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Mageplaza_Affiliate/checkout/summary/customdiscount'
            },
            isDisplayedCustomdiscount : function(){
                let valueDiscount = totals.getSegment('affiliate_discount').value;
                if(!valueDiscount){
                    return false;
                }
                return true;
            },
            getValueDiscount : function(){
                let valueDiscount = totals.getSegment('affiliate_discount').value;
                return priceUtils.formatPrice(-valueDiscount);
            },
            checkDiscountTitle : function () {
                return totals.getSegment('affiliate_discount').title;
            }
        });
    }
);
