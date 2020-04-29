<?php


namespace PaypalAddons\classes\API\Response;


class ResponseOrderGet extends Response
{
    /** @var Client*/
    protected $client;

    /** @var Address*/
    protected $address;

    public function __construct()
    {
        $this->setClient(new Client());
        $this->setAddress(new Address());
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }
}
