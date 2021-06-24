<?php

namespace Oplati\Oplati\Controller\Payment;

use Magento\Framework\App\Action\Action;

class CheckStatus extends Action
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Oplati\Oplati\Model\Oplati
     */
    protected $oplatiModel;

    /**
     * @var \Oplati\Oplati\Helper\Oplati
     */
    protected $oplatiHelper;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Oplati\Oplati\Model\Oplati $oplatiModel,
        \Oplati\Oplati\Helper\Oplati $oplatiHelper
    ) {

        $this->checkoutSession   = $checkoutSession;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->formKeyValidator  = $formKeyValidator;
        $this->oplatiModel       = $oplatiModel;
        $this->oplatiHelper      = $oplatiHelper;
        parent::__construct($context);
    }


    public function execute()
    {
        $data = [
            'success' => true,
            'data'    => [],
            'message' => ''
        ];

        try {

            if ( ! $this->formKeyValidator->validate($this->getRequest())) {
                throw new \Exception('Invalid form key');
            }

            $oplatiTransaction = $this->checkoutSession->getOplatiTransaction();
            if (empty($oplatiTransaction['paymentId'])) {
                throw new \Exception('Empty transaction paymentId');
            }

            $oldStatus = $oplatiTransaction['status'];

            $status                 = $this->oplatiHelper->getOplatiTransactionStatus($oplatiTransaction['paymentId']);
            $data['data']['status'] = $status;
            $data['message']        = __('STATUS_'.$status);

            if ($oldStatus !== $status) {
                $order = $this->checkoutSession->getLastRealOrder();
                $order->addStatusHistoryComment('Change Oplati transaction status '.print_r($this->oplatiHelper->getOplatiTransaction(), true));
                $order->save();
            }

        } catch (\Exception $e) {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
        }

        $result = $this->jsonResultFactory->create();
        $result->setData($data);

        return $result;
    }

}
