<?php

class LandingPage implements \JsonSerializable
{
    private $link;
    private $click_id;
    private $transaction_id;
    private $pid;
    private $affiliate_id;
    private $sub_id1;
    private $tracker_id;

    public function setClickId(string $click_id): LandingPage
    {
        $this->click_id = $click_id;
        return $this;
    }

    public function getClickId(): string|null
    {
        return $this->click_id;
    }

    public function setTransactionId(string $transaction_id): LandingPage
    {
        $this->transaction_id = $transaction_id;
        return $this;
    }

    public function getTransactionId(): string|null
    {
        return $this->transaction_id;
    }

    public function setLink(string $link): LandingPage
    {
        $this->link = $link;
        return $this;
    }

    public function getLink(): string|null
    {
        return $this->link;
    }

    public function setPid(string $pid): LandingPage
    {
        $this->pid = $pid;
        return $this;
    }

    public function getPid(): string|null
    {
        return $this->pid;
    }

    public function setAffiliateId(string $affiliate_id): LandingPage
    {
        $this->affiliate_id = $affiliate_id;
        return $this;
    }

    public function getAffiliateId(): string
    {
        return $this->affiliate_id;
    }

    public function setTrackerId(int $tracker_id): LandingPage
    {
        $this->tracker_id = $tracker_id;
        return $this;
    }

    public function getTrackerId(): int|null
    {
        return $this->tracker_id;
    }

    public function setSubId1(string $sub_id1): LandingPage
    {
        $this->sub_id1 = $sub_id1;
        return $this;
    }

    public function getSubId1(): string|null
    {
        return $this->sub_id1;
    }

    public function isClickIdTrue(): bool
    {
        return !empty($this->getClickId());
    }

    public function isClickIdTrueWith(LandingPage $landing): bool
    {
        return is_null($landing) ? false : !empty($landing->click_id);
    }

    public function isTransactionIdTrue(): bool
    {
        return !empty($this->getTransactionId());
    }

    public function isTransactionIdTrueWith(LandingPage $landing): bool
    {
        return is_null($landing) ? false : !empty($landing->transaction_id);
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