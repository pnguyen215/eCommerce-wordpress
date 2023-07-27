<?php

class AddressLandingPage
{
    private $province_id;
    private $province_name;
    private $district_id;
    private $district_name;
    private $ward_id;
    private $ward_name;
    private $shipping_address;

    public function setProvinceId(int $province_id): AddressLandingPage
    {
        $this->province_id = $province_id;
        return $this;
    }

    public function getProvinceId(): int|null
    {
        return $this->province_id;
    }

    public function setProvinceName(string $province_name): AddressLandingPage
    {
        $this->province_name = $province_name;
        return $this;
    }

    public function getProvinceName(): string|null
    {
        return $this->province_name;
    }

    public function setDistrictId(int $district_id): AddressLandingPage
    {
        $this->district_id = $district_id;
        return $this;
    }

    public function getDistrictId(): int|null
    {
        return $this->district_id;
    }

    public function setDistrictName(string $district_name): AddressLandingPage
    {
        $this->district_name = $district_name;
        return $this;
    }

    public function getDistrictName(): string|null
    {
        return $this->district_name;
    }

    public function setWardId(int $ward_id): AddressLandingPage
    {
        $this->ward_id = $ward_id;
        return $this;
    }

    public function getWardId(): int|null
    {
        return $this->ward_id;
    }

    public function setWardName(string $ward_name): AddressLandingPage
    {
        $this->ward_name = $ward_name;
        return $this;
    }

    public function getWardName(): string|null
    {
        return $this->ward_name;
    }

    public function setShippingAddress(string $shipping_address): AddressLandingPage
    {
        $this->shipping_address = $shipping_address;
        return $this;
    }

    public function getShippingAddress(): string|null
    {
        return $this->shipping_address;
    }
}
?>