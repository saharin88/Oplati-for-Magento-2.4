<?php

namespace Oplati\Oplati\Controller\Payment;

use Magento\Framework\App\Action\Action;

class Repeat extends Action
{

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonResultFactory;

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


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Oplati\Oplati\Model\Oplati $oplatiModel,
        \Oplati\Oplati\Helper\Oplati $oplatiHelper
    ) {
        $this->checkoutSession   = $checkoutSession;
        $this->oplatiModel       = $oplatiModel;
        $this->oplatiHelper      = $oplatiHelper;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->blockFactory      = $blockFactory;
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
            $order = $this->checkoutSession->getLastRealOrder();
            $this->oplatiHelper->createOplatiTransaction($order);
            $oplatiTransaction = $this->oplatiHelper->getOplatiTransaction();
            $this->checkoutSession->setOplatiTransaction($oplatiTransaction);
            $order->addStatusHistoryComment('Create Oplati transaction '.print_r($oplatiTransaction, true));
            $order->save();
            $data['data'] = $this->blockFactory->createBlock('Oplati\Oplati\Block\Oplati')->toHtml();
        } catch (\Exception $e) {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
        }

        $result = $this->jsonResultFactory->create();
        $result->setData($data);

        return $result;

    }

}
