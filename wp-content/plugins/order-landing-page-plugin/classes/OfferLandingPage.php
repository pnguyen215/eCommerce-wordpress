<?php

class OfferLandingPage
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
}
?>