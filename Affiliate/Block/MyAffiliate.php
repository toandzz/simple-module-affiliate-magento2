<?php
namespace Mageplaza\Affiliate\Block;

use Magento\Framework\App\ScopeInterface;

class MyAffiliate extends \Magento\Framework\View\Element\Template
{
    protected $customerSession;
    protected $scopeConfig;
    protected $accountFactory;
    protected $historyCollectionFactory;
    protected $priceCurrency;
    protected $helperData;
    protected $_timeZone;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
        \Mageplaza\Affiliate\Model\ResourceModel\History\Collection $historyCollectionFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Mageplaza\Affiliate\Helper\Data $helperData,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timeZone,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->accountFactory = $accountFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->priceCurrency = $priceCurrency;
        $this->helperData = $helperData;
        $this->_timeZone = $timeZone;
        parent::__construct($context, $data);
    }
    public function getCustomer(){
        $account = $this->accountFactory->create();
        $customerId = $this->customerSession->getCustomerId();
        $customerData = $account->load($customerId,'customer_id')->getData();
        if($customerData){
            return true;
        }else{
            return false;
        }
    }
    public function getBalance(){
        $customerId = $this->customerSession->getCustomerId();
        $account = $this->accountFactory->create();
        $balance = $account->load($customerId,'customer_id')->getData('balance');
        return $this->formatCurrency($balance);
    }
    public function getCode(){
        $customerId = $this->customerSession->getCustomerId();
        $account = $this->accountFactory->create();
        $code = $account->load($customerId,'customer_id')->getData('code');
        return $code;
    }
    public function getHistory(){
        $customerId = $this->customerSession->getCustomerId();
        $collection = $this->historyCollectionFactory->addFieldToFilter('customer_id',$customerId)->setOrder('create_at', 'DESC');
        return $collection->getItems();
    }
    public function referLink(){
        $urlRefer = 'http://newprj.loc/affiliate/refer/index/';
        $key = $this->helperData->getUrlKey();
        return $urlRefer . $key . '/';
    }
    public function getFormAction()
    {
        return $this->getUrl('affiliate/customer/register');
    }
    public function getStaticBlock()
    {
        return $this->helperData->getRegisterBlock();
    }
    public function formatCurrency($price){
        return $this->priceCurrency->convertAndFormat($price);
    }
    public function formatDateTime($time){
        $date = $this->_scopeConfig->getValue('catalog/custom_options/date_fields_order', ScopeInterface::SCOPE_DEFAULT);
        $format = str_replace(',','/', $date);
        $customFormat = str_replace(['d', 'm'], ['j', 'n'], $format);
        return $this->_timeZone->date(new \DateTime($time))->format($customFormat);
    }
}
