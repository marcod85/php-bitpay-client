<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Resource;

/**
 * Class PayoutTransaction
 * @package Bitpay
 */
class PayoutTransaction extends Resource
{
    /**
     * @var string
     */
    protected $txid;

    /**
     * @var string
     */
    protected $date;

    /**
     * Returns transaction ID for this payout
     * @return string
     */
    public function getTransactionId()
    {
        return $this->txid;
    }

    /**
     * Set transaction ID for payout.
     * @param $txid
     * @return $this
     */
    public function setTransactionId($txid)
    {
        if (true === isset($txid) && false === empty($txid)) {
            $this->txid = $txid;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the date and time of when the payment was sent.
     * @param $date
     * @return $this
     */
    public function setDate($date)
    {
        if (true === isset($date) && false === empty($date)) {
            $this->date = $date;
        }

        return $this;
    }
}
