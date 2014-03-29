<?php

use RESTful\RESTful;
use Assert\Assertion;
use Utils as u;

Class Customer 
{
    private $resource;
    private $id = null;
    private $email = null;
    private $firstName = null;
    private $lastName = null;
    private $password = null;

    function __construct($token, $customerId, $customerArray=null) 
    {
        Assertion::integer($customerId);
        $this->resource = new RESTful('http://api.zenprint.com/api/rest/v1.0', $token);
        $this->parseCustomer($customerId, $customerArray);
    }

    public function getId() {
        return $this->id;
    }

    public function setId($customerId) {
        $this->id = $customerId;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        Assertion::email($email);
        Assertion::notEmpty($email);
        $this->email = $email;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName($firstName) {
        Assertion::string($firstName);
        Assertion::notEmpty($firstName);
        $this->firstName = $firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName) {
        Assertion::string($lastName);
        Assertion::notEmpty($lastName);
        $this->lastName = $lastName;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        Assertion::string($password);
        Assertion::minLength($password, 7);
        $this->password = $password;
    }

    public function toJson() {
        return json_encode (
            $this->toArray()
        );
    }

    public function toArray() {
        return array (
            'email' => $this->getEmail(),
            'firstname' => $this->getFirstName(),
            'lastname' => $this->getLastName(),
            'password' => $this->getPassword()
        );
    }

    public function getCustomerBalance()
    {
        return new CustomerBalance (
            json_decode((string) $this->resource->get("customers/{$this->getId()}/balance"), true)
        );
    }

    public function getCustomerShareBalance()
    {
        return new CustomerShareBalance();
    }

    public function setCustomerShareBalance($customerShareBalance)
    {
        Assertion::isInstanceOf($customerShareBalance, 'CustomerShareBalance');
        $customerShareBalance->restValidation();
        $data = $customerShareBalance->toArray();
        /**
        * What does it return?
        */
        return $this->resource->put("customers/{$this->getId()}/balance", $data);
    }

    public function restValidation($isUpdate) {
        Assertion::notNull($this->getId(), "A customer must have an 'id'");
        if ($isUpdate) {
            Assertion::min($this->getId(), 1, "A customer id must be greater than 0");
        }

        Assertion::notNull($this->getFirstName(), "A customer must have a 'first name'");
        Assertion::notNull($this->getLastName(), "A customer must have a 'last name'");
        Assertion::notNull($this->getEmail(), "A customer must have an 'email'");
        Assertion::notNull($this->getPassword(), "A customer must have a 'password'");
    }

    private function parseCustomer($customerId, $customerArray) {
        $this->setId($customerId);
        if ($customerArray) {
            $this->setEmail(u::set('email', $customerArray));
            $this->setFirstName(u::set('firstname', $customerArray));
            $this->setLastName(u::set('lastname', $customerArray));
            $this->setPassword(u::set('password', $customerArray));
        }
    }
}
