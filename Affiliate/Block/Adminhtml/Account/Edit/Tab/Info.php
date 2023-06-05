<?php
namespace Mageplaza\Affiliate\Block\Adminhtml\Account\Edit\Tab;


use Mageplaza\Affiliate\Model\CustomerEntity;

class Info extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $accountFactory;
    protected $customerEntityFactory;
    protected $helperData;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
        \Mageplaza\Affiliate\Model\CustomerEntityFactory $customerEntityFactory,
        \Mageplaza\Affiliate\Helper\Data $helperData,
        array $data = []
    )
    {
        $this->accountFactory = $accountFactory;
        $this->customerEntityFactory = $customerEntityFactory;
        $this->helperData = $helperData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('account');
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Account Information')]);

        if($model->getID()){
            $customer = $this->customerEntityFactory->create();
            $customerById = $customer->load($model->getData('customer_id'),'entity_id')->getData();
            $cName = $customerById['firstname'] . ' ' .$customerById['lastname'] . ' (' . $customerById['email'] . ')' ;
        }

        if($model->getID())
        {
            $fieldset->addField(
                'customer_text',
                'label', [
                    'name'     => 'customer',
                    'label'    => __('Customer ID'),
                    'value'    => $model->getData('customer_id')
                ]
            );

            $fieldset->addField(
                'customer_id',
                'hidden', [
                    'name'     => 'customer_id',
                    'label'    => __('Customer ID'),
                    'required' => true
                ]
            );

            $fieldset->addField(
                'account_id',
                'hidden',
                [
                    'name' => 'account_id',
                    'label' => __('ID'),
                    'disabled' => false,
                ]
            );

            $fieldset->addField(
                'customer_name',
                'label',
                [
                    'label' => __('Customer Name'),
                    'name' => 'customer_name',
                    'value' => $cName
                ]
            );
            $fieldset->addField(
                'code',
                'label',
                [
                    'label' => __('Code'),
                    'name' => 'code'
                ]
            );
        }
        else
        {
            $fieldset->addField(
                'customer_id',
                'text', [
                    'name'     => 'customer_id',
                    'label'    => __('Customer ID'),
                    'required' => true,
                    'class' => 'validate-number'
                ]
            );
        }

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => ['1' => ('Active'), '0' => ('InActive')]
            ]
        );

        $fieldset->addField(
            'balance',
            'text', [
                'name'     => 'balance',
                'label'    => __('Balance'),
                'title'    => __('balance'),
                'required' => true,
                'class' => 'validate-number'
            ]
        );
        if($model->getID()){
            $fieldset->addField(
                'create_at',
                'label', [
                    'name'     => 'created_at',
                    'label'    => __('Created At'),
                    'title'    => __('created_at')
                ]
            );
        }
        $form->addValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
    public function getTabLabel()
    {
        return __('Account information');
    }
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }
}

