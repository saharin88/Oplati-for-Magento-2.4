<?php


namespace Oplati\Oplati\Controller\Payment;

use Magento\Framework\App\Action\Action;

class Success extends Action
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Oplati\Oplati\Helper\Oplati
     */
    protected $oplatiHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Oplati\Oplati\Helper\Oplati $oplatiHelper
    ) {

        $this->checkoutSession   = $checkoutSession;
        $this->oplatiHelper      = $oplatiHelper;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->formKeyValidator  = $formKeyValidator;
        parent::__construct($context);
    }


    public function execute()
    {
        try {
            $order = $this->checkoutSession->getLastRealOrder();
            $order->setStatus($this->oplatiHelper->getConfigValue('payment/oplati/order_status'));
            $order->save();
            $this->_redirect('checkout/onepage/success');
        } catch (\Exception $e) {
            $this->messageManager->addException($e);
            $this->_redirect('checkout/cart');
        }
    }

}
