<?php

namespace Oplati\Oplati\Controller\Payment;

use Magento\Framework\App\Action\Action;

class Processing extends Action
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Oplati\Oplati\Model\Oplati
     */
    protected $oplatiModel;

    /**
     * @var \Oplati\Oplati\Helper\Oplati
     */
    protected $oplatiHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Oplati\Oplati\Model\Oplati $oplatiModel,
        \Oplati\Oplati\Helper\Oplati $oplatiHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->oplatiModel     = $oplatiModel;
        $this->oplatiHelper    = $oplatiHelper;
        $this->pageFactory     = $pageFactory;
        parent::__construct($context);
    }


    public function execute()
    {
        try {
            $order = $this->checkoutSession->getLastRealOrder();
            $this->oplatiHelper->createOplatiTransaction($order);
            $oplatiTransaction = $this->oplatiHelper->getOplatiTransaction();
            $this->checkoutSession->setOplatiTransaction($oplatiTransaction);
            $order->addStatusHistoryComment('Create Oplati transaction '.print_r($oplatiTransaction, true));
            $order->save();
            $page = $this->pageFactory->create();

            return $page;
        } catch (\Exception $e) {
            $this->messageManager->addException($e);
            $this->_redirect('checkout/cart');
        }
    }

}
