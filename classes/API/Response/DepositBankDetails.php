<?php

namespace PaypalAddons\classes\API\Response;

class DepositBankDetails
{
    /** @var string */
    protected $bic;

    /** @var string */
    protected $bankName;

    /** @var string */
    protected $iban;

    /** @var string */
    protected $accountHolderName;

    /**
     * @return string
     */
    public function getBic()
    {
        return (string) $this->bic;
    }

    /**
     * @param string $bic
     *
     * @return DepositBankDetails
     */
    public function setBic($bic)
    {
        $this->bic = $bic;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankName()
    {
        return (string) $this->bankName;
    }

    /**
     * @param string $bankName
     *
     * @return DepositBankDetails
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return (string) $this->iban;
    }

    /**
     * @param string $iban
     *
     * @return DepositBankDetails
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountHolderName()
    {
        return (string) $this->accountHolderName;
    }

    /**
     * @param string $accountHolderName
     *
     * @return DepositBankDetails
     */
    public function setAccountHolderName($accountHolderName)
    {
        $this->accountHolderName = $accountHolderName;

        return $this;
    }
}
