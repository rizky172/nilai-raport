<?php

namespace App\WevelopeLibs;

class WeXenditCheckout
{
    private $items = [];
    private $customer = [];
    private $externalId;
    private $invoiceDuration = 86400;
    private $currency = 'IDR';

    public function addItem($id, $name, $price, $quantity, $category)
    {
        $this->items[] = [
            'id' => $id,
            'name' => $name,
            'quantity' => $quantity,
            'price' => $price,
            'category' => $category,
        ];
    }

    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    public function setCustomer($name, $email, $phone)
    {
        $this->customer = [
            'given_names' => $name,
            'email' => $email,
            'mobile_number' => $phone,
        ];
    }

    private function getTotal()
    {
        $total = 0;

        foreach ($this->items as $item)
            $total += $item['price'] * $item['quantity'];

        return $total;
    }

    private function invoiceParams()
    {
        return [
            'external_id' => $this->externalId,
            'amount' => $this->getTotal(),
            'invoice_duration' => $this->invoiceDuration,
            'customer' => $this->customer,
            'currency' => $this->currency,
            'items' => $this->items,
        ];
    }

    public function createInvoice()
    {
        $xenditInvoice = \Xendit\Invoice::create($this->invoiceParams());

        return $xenditInvoice;
    }
}