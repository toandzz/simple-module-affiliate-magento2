<?php
namespace Mageplaza\Affiliate\Block\Adminhtml\Account;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }
    protected function _construct()
    {
        $id = $this->getRequest()->getParam('account_id');
        if($id){
            $this->_objectId = 'account_id';
            $this->_blockGroup = 'Mageplaza_Affiliate';
            $this->_controller = 'adminhtml_account';
            $this->buttonList->update('save', 'label', __('Save Account'), 'id');
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'saveAndContinueEdit',
                                'target' => '#edit_form'
                            ]
                        ]
                    ]
                ],
                -100
            );
            $this->addButton(
                'delete',
                [
                    'label' => __('Delete'),
                    'onclick' => 'deleteConfirm(' . json_encode(__('Are you sure you want to do this?'))
                        . ','
                        . json_encode($this->getDeleteUrl()
                        )
                        . ')',
                    'class' => 'scalable delete',
                    'level' => -1
                ]
            );
        }else{
            $this->_objectId = 'account_id';
            $this->_blockGroup = 'Mageplaza_Affiliate';
            $this->_controller = 'adminhtml_account';
            $this->buttonList->update('save', 'label', __('Save Account'), 'id');
        }

        parent::_construct();
    }
}

