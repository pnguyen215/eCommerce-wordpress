<?php

class LandingPage
{
    private $link;
    private $click_id;
    private $transaction_id;

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
}

?>