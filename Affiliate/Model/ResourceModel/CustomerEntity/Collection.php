<?php
namespace Mageplaza\Affiliate\Model\ResourceModel\CustomerEntity;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected function _construct()
    {
        $this->_init('Mageplaza\Affiliate\Model\CustomerEntity', 'Mageplaza\Affiliate\Model\ResourceModel\CustomerEntity');
    }

}
