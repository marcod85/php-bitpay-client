<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Resource;

/**
 * Class Payout
 *
 * @package Bitpay
 */
class Payout extends Resource
{
    /**
     * @var string
     */
    protected $account_id;

    /**
     * @var string
     */
    protected $effectiveDate;

    /**
     * @var string
     */
    protected $requestDate;

    /**
     * @var array
     */
    protected $instructions = array();

    /**
     * @var string
     */
    protected $pricingMethod;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var string
     */
    protected $responseToken;

    /**
     * @inheritdoc
     */
    public function getAccountId()
    {
        return $this->account_id;
    }

    /**
     * Set Account Id - Bitpays account ID for the payout.
     *
     * @param $id
     * @return $this
     */
    public function setAccountId($id)
    {
        if (true === isset($id) && false === empty($id)) {
            $this->account_id = $id;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }

    /**
     * Set Effective date - date payout should be given to employees.
     * @param $effectiveDate
     * @return $this
     */
    public function setEffectiveDate($effectiveDate)
    {
        if (true === isset($effectiveDate) && false === empty($effectiveDate)) {
            $this->effectiveDate = $effectiveDate;
        }

        return $this;
    }



    /**
     * @inheritdoc
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * Set
     */
    public function setRequestDate($requestDate)
    {
        if (true === isset($requestDate) && false === empty($requestDate)) {
            $this->requestDate = $requestDate;
        }

        return $this;
    }
    /**
     * @inheritdoc
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * Add Instruction of PayoutInstructionInterface type
     * Increases $this->amount by value.
     *
     * @param PayoutInstructionInterface $instruction
     * @return $this
     */
    public function addInstruction(PayoutInstructionInterface $instruction)
    {
        if (true === isset($instruction) && false === empty($instruction)) {
            $this->instructions[] = $instruction;
        }

        return $this;
    }

    /**
     * Update Instruction - Supply an index of the instruction to update,
     * plus the function and single argument, to do something to an instruction.
     *
     * @param $index
     * @param $function
     * @param $argument
     * @return $this
     */
    public function updateInstruction($index, $function, $argument)
    {
        if (true === isset($index) && true === isset($function)) {
            $this->instructions[$index]->$function($argument);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getResponseToken()
    {
        return $this->responseToken;
    }

    /**
     * Set Response Token - returned by Bitpay when payout request is created
     *
     * @param $responseToken
     * @return $this
     */
    public function setResponseToken($responseToken)
    {
        if (true === isset($responseToken) && false === empty($responseToken)) {
            $this->responseToken = trim($responseToken);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPricingMethod()
    {
        return $this->pricingMethod;
    }

    /**
     * Set the pricing method for this payout request
     * @param $pricingMethod
     * @return $this
     */
    public function setPricingMethod($pricingMethod)
    {
        if (true === isset($pricingMethod) && false === empty($pricingMethod)) {
            $this->pricingMethod = trim($pricingMethod);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set the payroll providers reference for this payout
     *
     * @param $reference
     * @return $this
     */
    public function setReference($reference)
    {
        if (true === isset($reference) && false === empty($reference)) {
            $this->reference = trim($reference);
        }

        return $this;
    }


}
