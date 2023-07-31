<?php

class OfferLandingPage implements \JsonSerializable
{
    private $offer_id;
    private $product_id;
    private $product_name;

    public function setOfferId(int $offer_id): OfferLandingPage
    {
        $this->offer_id = $offer_id;
        return $this;
    }

    public function getOfferId(): int|null
    {
        return $this->offer_id;
    }

    public function setProductId(int $product_id): OfferLandingPage
    {
        $this->product_id = $product_id;
        return $this;
    }

    public function getProductId(): int|null
    {
        return $this->product_id;
    }

    public function setProductName(string $product_name): OfferLandingPage
    {
        $this->product_name = $product_name;
        return $this;
    }

    public function getProductName(): string|null
    {
        return $this->product_name;
    }

    public function isOfferIdTrue(): bool
    {
        return !is_null($this->getOfferId()) && is_numeric($this->getOfferId()) && $this->getOfferId() > 0;
    }

    public function isOfferIdTrueWith(OfferLandingPage $order): bool
    {
        return is_null($order) ? false : is_numeric($order->offer_id) && $order->offer_id > 0;
    }

    public function isProductIdTrue(): bool
    {
        return !is_null($this->getProductId()) && is_numeric($this->getProductId()) && $this->getProductId() > 0;
    }

    public function isProductIdTrueWith(OfferLandingPage $order): bool
    {
        return is_null($order) ? false : is_numeric($order->product_id) && $order->product_id > 0;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function toJson()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}
?>