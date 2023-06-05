<?php
namespace Mageplaza\Affiliate\Helper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $inlineTranslation;
    protected $escaper;
    protected $transportBuilder;
    protected $logger;
    protected $customerEntityFactory;
    protected $customerSession;
    protected $accountFactory;
    protected $helperData;

    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        \Mageplaza\Affiliate\Model\CustomerEntityFactory $customerEntityFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
        \Mageplaza\Affiliate\Helper\Data $helperData,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->customerEntityFactory = $customerEntityFactory;
        $this->customerSession = $customerSession;
        $this->accountFactory = $accountFactory;
        $this->helperData = $helperData;
    }
    public function sendEmail($per, $code)
    {
        $customer = $this->customerEntityFactory->create();
        $account = $this->accountFactory->create();
        $customerId = $this->accountFactory->create()->load($code, 'code')->getData('customer_id');
        $customerEmail =$customer->load($customerId,'entity_id')->getData('email');

        $newBalance = $account->load($customerId,'customer_id')->getData('balance');
        $oldBalance = $newBalance - $per;
        try {
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml('Nguyen Toan'),
                'email' => $this->escaper->escapeHtml('toannd@mageplaza.com'),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('affiliate_sendmail_template')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'newBalance'  => $newBalance.'$',
                    'oldBalance' => $oldBalance.'$',
                    'change' => '+'.$per.'$'
                ])
                ->setFrom($sender)
                ->addTo($customerEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    public function sendEmailEdit($oldBalance){
        $param = $this->_getRequest()->getParams();
        $customerId = $param['customer_id'];
        $customer = $this->customerEntityFactory->create();
        $customerEmail =$customer->load($customerId,'entity_id')->getData('email');
        $newBalance = $param['balance'];
        $change = $newBalance - $oldBalance;
        try {
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml('Nguyen Toan'),
                'email' => $this->escaper->escapeHtml('toannd@mageplaza.com'),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('affiliate_sendmail_template')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'newBalance'  => $newBalance.'$',
                    'oldBalance' => $oldBalance .'$',
                    'change' => $change.'$'
                ])
                ->setFrom($sender)
                ->addTo($customerEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
    public function sendEmailNewAccountAdmin(){
        $customerId = $this->_getRequest()->getParam('customer_id');
        $customer = $this->customerEntityFactory->create();
        $customerEmail =$customer->load($customerId,'entity_id')->getData('email');
        $newBalance = $this->_getRequest()->getParam('balance');
        $change = $newBalance;
        try {
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml('Nguyen Toan'),
                'email' => $this->escaper->escapeHtml('toannd@mageplaza.com'),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('affiliate_sendmail_create_template')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'newBalance'  => $newBalance.'$',
                    'oldBalance' => 0 .'$',
                    'change' => '+'.$change.'$'
                ])
                ->setFrom($sender)
                ->addTo($customerEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    public function sendEmailNewAccountCustomer(){
        $customerId = $this->customerSession->getCustomerId();
        $customer = $this->customerEntityFactory->create();
        $customerEmail =$customer->load($customerId,'entity_id')->getData('email');
        try{
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml('Nguyen Toan'),
                'email' => $this->escaper->escapeHtml('toannd@mageplaza.com'),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('affiliate_sendmail_create_template')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'newBalance'  => 0 .'$',
                    'oldBalance' => 0 .'$',
                    'change' => 0 .'$'
                ])
                ->setFrom($sender)
                ->addTo($customerEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

}
