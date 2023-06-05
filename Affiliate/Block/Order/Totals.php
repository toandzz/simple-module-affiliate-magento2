<?php
namespace Mageplaza\Affiliate\Block\Order;

class Totals extends \Magento\Framework\View\Element\AbstractBlock
{
    protected $historyFactory;
    protected $orderFactory;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageplaza\Affiliate\Model\HistoryFactory $historyFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->historyFactory = $historyFactory;
        $this->orderFactory = $orderFactory;
    }
    public function initTotals()
    {
        $history = $this->historyFactory->create();
        $orderBlock = $this->getParentBlock();
        $orderId = $orderBlock->getOrder()->getId();
        $order_model = $this->orderFactory->create()->load($orderId,'entity_id');
        $subTotal = $order_model->getSubtotal();
        $grandTotal = $order_model->getGrandTotal();
        $affiliateDiscount = $subTotal - $grandTotal;
        if($historyCms = $history->load($orderId, 'order_id')->getData()){
            $valueCommisstion = $historyCms['amount'];
        }
        $orderBlock->addTotal(new \Magento\Framework\DataObject([
            'code'       => 'affiliate_discount',
            'label'      => __('Affiliate Discount'),
            'value'      => $affiliateDiscount ?? 0,
        ]), 'subtotal');

        $orderBlock->addTotal(new \Magento\Framework\DataObject([
            'code'       => 'commisstion',
            'label'      => __('Commisstion'),
            'value'      => $valueCommisstion ?? 0,
        ]), 'subtotal');
    }

}
