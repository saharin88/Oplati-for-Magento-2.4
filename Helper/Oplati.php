<?php

namespace Oplati\Oplati\Helper;

use Magento\Store\Model\ScopeInterface;

class Oplati
{

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var array
     */
    protected $transaction;

    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->curl        = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->curlAddHeadersAndSetOptions();
    }

    protected function curlAddHeadersAndSetOptions()
    {
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("regNum", $this->getConfigValue('payment/oplati/regnum'));
        $this->curl->addHeader("password", $this->getConfigValue('payment/oplati/password'));
        $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->setOption(CURLOPT_SSL_VERIFYHOST, false);
    }

    /**
     * @param   \Magento\Sales\Model\Order  $order
     *
     * @throws \Exception
     */
    public function createOplatiTransaction($order)
    {
        $this->curl->post($this->getServer().'pos/webPayments', $this->prepareData($order));
        $this->transaction = json_decode($this->curl->getBody(), true);
        if (empty($this->transaction['paymentId'])) {
            throw new \Exception((empty($this->transaction['devMessage']) ? 'Error create transaction' : $this->transaction['devMessage']));
        }
    }

    public function getOplatiTransactionStatus($paymentId)
    {
        $this->curl->get($this->getServer().'pos/payments/'.$paymentId);
        $this->transaction = json_decode($this->curl->getBody(), true);
        if (isset($this->transaction['status']) === false) {
            throw new \Exception((empty($this->transaction['devMessage']) ? 'Error get payment status' : $this->transaction['devMessage']));
        }

        return $this->transaction['status'];
    }

    public function getOplatiTransaction()
    {
        return $this->transaction;
    }


    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }


    protected function getServer()
    {
        if ($this->scopeConfig->getValue('payment/oplati/test', ScopeInterface::SCOPE_STORE) === '1') {
            return 'https://bpay-testcashdesk.lwo.by/ms-pay/';
        } else {
            return 'https://cashboxapi.o-plati.by/ms-pay/';
        }
    }

    /**
     * @param   \Magento\Sales\Model\Order  $order
     *
     * @return string
     */
    protected function prepareData($order)
    {
        $data = [
            'sum'         => $order->getGrandTotal(),
            'shift'       => 'smena 1',
            'orderNumber' => $order->getIncrementId(),
            'regNum'      => $this->getConfigValue('payment/oplati/regnum'),
            'details'     => [
                'amountTotal' => $order->getGrandTotal(),
                'items'       => []
            ],
            'successUrl'  => '',
            'failureUrl'  => ''
        ];

        $items = $order->getAllVisibleItems();

        foreach ($items as $i => $item) {
            $data['details']['items'][] = [
                'type'     => 1,
                'name'     => $item->getName(),
                'price'    => $item->getPrice(),
                'quantity' => $item->getQtyOrdered(),
                'cost'     => $item->getRowTotal(),
            ];
        }

        if ($order->getShippingAmount() > 0) {
            $data['details']['items'][] = [
                'type' => 2,
                'name' => $order->getShippingDescription(),
                'cost' => $order->getShippingAmount(),
            ];
        }

        return json_encode($data);
    }

}
