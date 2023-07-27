<?php
require_once __DIR__ . './../classes/LandingPage.php';
require_once __DIR__ . './../classes/OfferLandingPage.php';
require_once __DIR__ . './../classes/AddressLandingPage.php';

class OrderLandingPage
{
    private $customer_name;
    private $customer_email;
    private $customer_phone;
    private $offer;
    private $address;
    private $landing_page;

    public function setCustomerName(string $customer_name): OrderLandingPage
    {
        $this->customer_name = $customer_name;
        return $this;
    }

    public function getCustomerName(): string|null
    {
        return $this->customer_name;
    }

    public function setCustomerEmail(string $customer_email): OrderLandingPage
    {
        $this->customer_email = $customer_email;
        return $this;
    }

    public function getCustomerEmail(): string|null
    {
        return $this->customer_email;
    }

    public function setCustomerPhone(string $customer_phone): OrderLandingPage
    {
        $this->customer_phone = $customer_phone;
        return $this;
    }

    public function getCustomerPhone(): string|null
    {
        return $this->customer_phone;
    }

    public function setOffer(OfferLandingPage $offer): OrderLandingPage
    {
        $this->offer = $offer;
        return $this;
    }

    public function getOffer(): OfferLandingPage|null
    {
        return $this->offer;
    }

    public function setAddress(AddressLandingPage $address): OrderLandingPage
    {
        $this->address = $address;
        return $this;
    }

    public function getAddress(): AddressLandingPage|null
    {
        return $this->address;
    }

    public function setLandingPage(LandingPage $landing_page): OrderLandingPage
    {
        $this->landing_page = $landing_page;
        return $this;
    }

    public function getLandingPage(): LandingPage|null
    {
        return $this->landing_page;
    }
}
?>