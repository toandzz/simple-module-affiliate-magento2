<?php

namespace Mageplaza\Affiliate\Ui\Component\Listing\Grid\Column;

class Status implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => ('Active')],
            ['value' => 0, 'label' => ('InActive')]
        ];
    }
}
