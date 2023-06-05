<?php
namespace Mageplaza\Affiliate\Model\Config\Source;

class SelectApply implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'no', 'label' => __('No')],
            ['value' => 'fixed', 'label' => __('Fixed Value')],
            ['value' => 'percentage', 'label' => __('Percentage')]
        ];
    }
}
