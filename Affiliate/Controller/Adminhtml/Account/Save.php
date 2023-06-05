<?php
namespace Mageplaza\Affiliate\Controller\Adminhtml\Account;

use Magento\Framework\Registry;
use Magento\Framework\Controller\Result\RedirectFactory;
use RuntimeException;

class Save extends \Mageplaza\Affiliate\Controller\Adminhtml\InitAccount
{

    protected $registry;
    protected $resultRedirectFactory;
    protected $_helperData;
    protected $_accountFactory;
    protected $_customerEntityFactory;
    protected $historyFactory;
    protected $helperEmail;
    const ADMIN_RESOURCE = 'Mageplaza_Affiliate::manageaccount_save';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
        \Mageplaza\Affiliate\Helper\Data $helperData,
        \Mageplaza\Affiliate\Helper\Email $helperEmail,
        RedirectFactory $resultRedirect,
        \Mageplaza\Affiliate\Model\CustomerEntityFactory $customerEntityFactory,
        \Mageplaza\Affiliate\Model\HistoryFactory $historyFactory,
        Registry $registry
    )
    {
        $this->_accountFactory = $accountFactory;
        $this->registry = $registry;
        $this->_helperData = $helperData;
        $this->helperEmail = $helperEmail;
        $this->resultRedirectFactory = $resultRedirect;
        $this->_customerEntityFactory = $customerEntityFactory;
        $this->historyFactory = $historyFactory;
        parent::__construct($context,$accountFactory,$registry,$customerEntityFactory);
    }

    public function execute(){
        $param = $this->getRequest()->getParams();
        $account = $this->initAccount();
        $resultRedirect = $this->resultRedirectFactory->create();
        if($param['balance'] < 0){
            $this->messageManager->addWarning('Validate balance');
            return $resultRedirect->setPath('*/*/');
        }
        if(!$this->initCustomer()) {
            $this->messageManager->addErrorMessage(__('Customer dont exist'));
            return $this->_redirect('*/*/index');
        }
        //edit
            if($account){
                $this->prepareData($account, $param);
                if($account->getId()){
                    try{
                        $account->save();
                        $this->messageManager->addSuccessMessage(__('You saved the account.'));

                        if($this->getRequest()->getParam('back')){
                            $resultRedirect->setPath('*/*/edit', ['account_id' => $account->getId()]);
                            return $resultRedirect;
                        }else{
                            $resultRedirect->setPath('*/*/index');
                            return $resultRedirect;
                        }
                    }catch(RuntimeException $e){
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }
                    catch(\Exception $e){
                        $this->messageManager->addExceptionMessage($e, __('This code already exist.'));
                        return $this->_redirect('*/*/edit');
                    }
                    $resultRedirect->setPath('*/*/index');
                    return $resultRedirect;
                }else{
                    try{
                        if($this->initCode()){
                            $this->messageManager->addErrorMessage(__('The customer already has the code'));
                            return $this->_redirect('*/*/create');
                        }
                        else if($this->initAccountAffiliate()){
                            $this->messageManager->addErrorMessage(__('The affiliate exist'));
                            return $this->_redirect('*/*/create');
                        }
                        else{
                            $account->save();
                            $this->messageManager->addSuccessMessage(__('You saved the account.'));
                            $resultRedirect->setPath('*/*/index');
                            return $resultRedirect;
                        }
                    }catch(RuntimeException $e){
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }
                    catch(\Exception $e){
                        $this->messageManager->addExceptionMessage($e, __('This code already exist.'));
                        return $this->_redirect('*/*/create');
                    }
                }
                $resultRedirect->setPath('*/*/index');
                return $resultRedirect;
            }
        return $this->_redirect('*/*/index');
    }

    protected function prepareData($account, $param = []){

        if(isset($param['account_id'])){
            $oldBalance = $account->load($param['account_id'])->getData('balance');
            $data = [
                'account_id' => $param['account_id'],
                'status' => $param['status'],
                'balance' => $param['balance']
            ];
            $account->setData($data)->save();
            if($oldBalance != $param['balance']){
                $this->helperEmail->sendEmailEdit($oldBalance);
            }
        }else{
            $codeLength = $this->_helperData->getCodeLength();
            $code = $this->_helperData->randomCode($codeLength);
            $data = [
                'customer_id' =>$param['customer_id'],
                'code' => $code,
                'status' => $param['status'],
                'balance' => $param['balance']
            ];
            $account->setData($data);
            $this->helperEmail->sendEmailNewAccountAdmin();
        }

        if($param['balance']>0){
            $history = $this->historyFactory->create();
            $data = [
                'order_id' => 0,
                'order_increment_id' => 0,
                'customer_id' => $param['customer_id'],
                'is_admin_change' => 1,
                'amount' => $param['balance'],
                'status' => $param['status']
            ];
            $history->addData($data)->save();
        }
    }
}
