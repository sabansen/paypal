<?php


namespace PaypalAddons\classes\API\Response;


class Address
{
    /** @var string*/
    protected $city;

    /** @var string*/
    protected $countryCode;

    /** @var string*/
    protected $address1;

    /** @var string*/
    protected $address2;

    /** @var string*/
    protected $stateCode;

    /** @var string*/
    protected $phone;

    /** @var string*/
    protected $postCode;

    /** @var string*/
    protected $fullName;

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getStateCode()
    {
        return $this->stateCode;
    }

    /**
     * @param string $stateCode
     */
    public function setStateCode($stateCode)
    {
        $this->stateCode = $stateCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @param string $postCode
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @param string $address1
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }
}
