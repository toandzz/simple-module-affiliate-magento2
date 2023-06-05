<?php
namespace Mageplaza\Affiliate\Model\ResourceModel;


use Magento\Framework\Exception\LocalizedException;

class Account extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $accountFactory;
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory
    )
    {
        $this->accountFactory = $accountFactory;
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('affiliate_account', 'account_id');
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object){
        $account = $this->accountFactory->create();
        $code = $object->getCode();
        $id = $object->getId();
        $checkCode = $account->load($code,'code')->getData('code');
        if(empty($id)){
            if($checkCode){
                throw new LocalizedException(__('This code already exist'));
            }
        }

    }
}
