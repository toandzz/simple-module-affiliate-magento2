<?php
namespace Mageplaza\Affiliate\Ui\Component\Listing\Grid\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Increment extends \Magento\Ui\Component\Listing\Columns\Column{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if($item['is_admin_change'] == 1){
                    $item['is_admin_change'] = 'Admin change';
                }else{
                    $item['is_admin_change'] = 'Create from order #' . $item['order_increment_id'];
                }
            }
        }

        return $dataSource;
    }
}

