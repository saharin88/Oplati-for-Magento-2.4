<?php


namespace Oplati\Oplati\Controller\Payment;

use Magento\Framework\App\Action\Action;

class Cancel extends Action
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }


    public function execute()
    {
        try {
            $order = $this->checkoutSession->getLastRealOrder();
            $order->cancel();
            $order->save();
        } catch (\Exception $e) {
            $this->messageManager->addException($e);
        }

        $this->_redirect('checkout/cart');
    }

}
