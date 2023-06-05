<?php
namespace Mageplaza\Affiliate\Model;
class CustomerEntity extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Mageplaza\Affiliate\Model\ResourceModel\CustomerEntity::class);
    }
}
