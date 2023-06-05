<?php
namespace Mageplaza\Affiliate\Controller\Adminhtml\Account;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\Affiliate\Model\ResourceModel\Account\CollectionFactory;

class MassInactive extends Action
{
    protected $collectionFactory;
    protected $filter;
    const ADMIN_RESOURCE = 'Mageplaza_Affiliate::manageaccount_massinactive';
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $count = 0;
        foreach ($collection as $model) {
            $model->addData(['status' => 0])->save();
            $count++;
        }
        $this->messageManager->addSuccess(__('A total of %1 Account have been in active.', $count));

        $this->_redirect('affiliate/account/index');
    }
}
