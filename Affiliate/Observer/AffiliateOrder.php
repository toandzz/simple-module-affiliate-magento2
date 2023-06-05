<?php
namespace Mageplaza\Affiliate\Observer;

use Magento\Framework\Event\Observer;

class AffiliateOrder implements \Magento\Framework\Event\ObserverInterface
{
    protected $customerSession;
    protected $historyFactory;
    protected $accountFactory;
    protected $helperData;
    protected $helperEmail;
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Mageplaza\Affiliate\Model\HistoryFactory $historyFactory,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
        \Mageplaza\Affiliate\Helper\Data $helperData,
        \Mageplaza\Affiliate\Helper\Email $helperEmail
    )
    {
        $this->customerSession = $customerSession;
        $this->historyFactory = $historyFactory;
        $this->accountFactory = $accountFactory;
        $this->helperData = $helperData;
        $this->helperEmail = $helperEmail;
    }
    public function execute(Observer $observer)
    {
        $key = $this->helperData->getUrlKey();
        $quote = $observer->getQuote();
        $affiliateCode = $quote->getAffiliateCode();
        $history = $this->historyFactory->create();
        $account = $this->accountFactory->create();

            $order = $observer->getData('order');
            $subTotal = $order['subtotal'];
            $commissionValue = $this->helperData->getCommissionValue();
            $commissionType = $this->helperData->getCommissionType();
            switch ($commissionType){
                case 'fixed':
                    $per = $commissionValue;
                    break;
                case 'percentage':
                    $per = $subTotal / 100 * $commissionValue;
                    break;
                default:
                    $per = 0;
                }
            if(isset($_COOKIE[$key])){
                $newBalance = $account->load($_COOKIE[$key],'code')->getData('balance') + $per;
                $addBalance = $account->load($_COOKIE[$key],'code')->addData([
                    'balance' => $newBalance
                ])->save();
                $affiliateId = $account->load($_COOKIE[$key],'code')->getData('customer_id');
                if($addBalance){
                    $data = [
                        'order_id' => $order['entity_id'],
                        'order_increment_id' => $order['increment_id'],
                        'customer_id' => $affiliateId,
                        'is_admin_change' => 0,
                        'amount' => $per,
                        'status' => 1
                    ];
                    $this->helperEmail->sendEmail($per,$_COOKIE[$key]);
                    $history->addData($data)->save();
                }
                unset($_COOKIE[$key]);
                setcookie($key, null, -1, '/');

            }

            else if($affiliateCode && empty($_COOKIE[$key])){
                $balance = $account->load($affiliateCode,'code')->getData('balance') + $per;
                $addBalance = $account->load($affiliateCode,'code')->addData([
                    'balance' => $balance
                ])->save();
                $affiliateId = $account->load($affiliateCode,'code')->getData('customer_id');
                if($addBalance){
                    $data = [
                        'order_id' => $order['entity_id'],
                        'order_increment_id' => $order['increment_id'],
                        'customer_id' => $affiliateId,
                        'is_admin_change' => 0,
                        'amount' => $per,
                        'status' => 1
                    ];
                    $history->addData($data)->save();
                }
                $this->helperEmail->sendEmail($per,$affiliateCode);
            }
    }
}
