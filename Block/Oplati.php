<?php

namespace Oplati\Oplati\Block;

use \Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class Oplati extends Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var string
     */
    protected $_template = 'oplati.phtml';


    /**
     * @param   Template\Context                                    $context
     * @param   \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig
     * @param   \Magento\Checkout\Model\Session                     $checkoutSession
     * @param   array                                               $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfig      = $scopeConfig;
    }

    public function getConfigValue($field)
    {
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE);
    }


    public function getDynamicQR()
    {
        $transaction = $this->checkoutSession->getOplatiTransaction();

        return $transaction['dynamicQR'];
    }

}
