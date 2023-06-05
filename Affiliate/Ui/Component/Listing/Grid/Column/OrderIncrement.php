<?php
namespace Mageplaza\Affiliate\Ui\Component\Listing\Grid\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class OrderIncrement extends \Magento\Ui\Component\Listing\Columns\Column{
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
                if($item['order_increment_id'] == 0){
                    $item['order_increment_id'] = 'Null';
                }else{
                    $item['order_increment_id'] = '#' . $item['order_increment_id'];
                }
            }
        }

        return $dataSource;
    }
}
