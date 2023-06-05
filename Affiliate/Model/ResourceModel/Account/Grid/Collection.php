<?php
namespace Mageplaza\Affiliate\Model\ResourceModel\Account\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mageplaza\Affiliate\Model\ResourceModel\Account;
use Psr\Log\LoggerInterface as Logger;
use Zend_Db_Expr;

class Collection extends SearchResult
{
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
                      $mainTable = 'affiliate_account',
                      $resourceModel = Account::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerName();
        return $this;
    }

    public function addCustomerName()
    {
        $result = $this->getSelect()->joinLeft(
            ['ce' => $this->getTable('customer_entity')],
            'main_table.customer_id = ce.entity_id',
            ['firstname', 'lastname']
        )->columns([
            'customer_name' => new Zend_Db_Expr("CONCAT(`ce`.`firstname`,' ',`ce`.`lastname`)")
        ]);
        return $this;
    }
}
