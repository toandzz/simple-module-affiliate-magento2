<?php
namespace Mageplaza\Affiliate\Model\Config\Source;

class SelectCommissionType implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'fixed', 'label' => __('Fixed Value')],
            ['value' => 'percentage', 'label' => __('Percentage')]
        ];
    }
}
