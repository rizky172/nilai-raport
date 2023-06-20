<?php

namespace App\WevelopeLibs;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

use App\WevelopeLibs\Helper\DateFormat;

use App\Libs\Repository\SalesInvoice;

use App\Invoice;
use App\InvoiceDetail;
use App\Payment;
use App\Category;

class WeXenditInvoiceCallback
{
    const STATUS_PAID = 'PAID';
    const STATUS_EXPIRED = 'EXPIRED';

    private $callback;
    private $invoice;
    private $invoiceDetail;
    private $payment;

    private $paymentCategoryId;
    private $paymentAccountId;

    public function __construct($callback)
    {
        $this->callback = json_decode($callback);
        $this->invoice = $this->getInvoiceModel($this->callback->external_id);
        $this->invoiceDetail = InvoiceDetail::where('invoice_id', $this->invoice->id)->get()->toArray();
        $this->payment = Payment::where('table_name', $this->invoice->getTable())
                                ->where('fk_id', $this->invoice->id)
                                ->get();

        $this->paymentCategoryId = Category::where('group_by', 'payment_method')
                                            ->where('name', 'xendit')
                                            ->first()->id;

        $this->paymentAccountId = Category::where('group_by', 'payment_account')
                                            ->where('name', 'xendit')
                                            ->first()->id;
    }

    public function handle()
    {
        if ($this->callback->status == self::STATUS_PAID) {
            $this->setPaid();
        }

        if ($this->callback->status == self::STATUS_EXPIRED) {
            $this->setExpired();
        }
    }

    private function setPaid()
    {
        $this->updateInvoiceStatus();

        $repo = new SalesInvoice($this->invoice);
        $repo->setXenditCbData(json_encode($this->callback));

        foreach ($this->invoiceDetail as $detail)
            $repo->addDetail($detail['id'], $detail['item_id'], $detail['qty'], $detail['price'], $detail['notes']);

        //payment from database
        if ($this->payment->isNotEmpty()) {
            $payment = $this->payment->toArray();
            foreach ($payment as $x) {

                $repo->addPayment(
                    $x['id'],
                    $x['payment_category_id'],
                    $x['payment_account_id'],
                    $x['ref_no'],
                    $x['amount'],
                    $x['notes'],
                    $x['created']
                );
            }
        }

        //payment from xendit callback
        $repo->addPayment(
            null,
            $this->paymentCategoryId,
            $this->paymentAccountId,
            $this->callback->id,
            $this->callback->paid_amount,
            null,
            DateFormat::dateTimeIso(new \DateTime($this->callback->paid_at)),
            $this->callback->payment_method
        );

        $repo->save();
    }

    private function setExpired()
    {
        $this->updateInvoiceStatus();

        $repo = new SalesInvoice($this->invoice);
        $repo->setXenditCbData(json_encode($this->callback));

        foreach ($this->invoiceDetail as $detail)
            $repo->addDetail($detail['id'], $detail['item_id'], $detail['qty'], $detail['price'], $detail['notes']);

        //payment from database
        if ($this->payment->isNotEmpty()) {
            $payment = $this->payment->toArray();
            foreach ($payment as $x) {

                $repo->addPayment(
                    $x['id'],
                    $x['payment_category_id'],
                    $x['payment_account_id'],
                    $x['ref_no'],
                    $x['amount'],
                    $x['notes'],
                    $x['created']
                );
            }
        }

        $repo->save();
    }

    public function updateInvoiceStatus()
    {
        $status = strtolower($this->callback->status);

        $statusId = Category::where('group_by', 'invoice_status')
                            ->where('name', $status)->first()->id;

        $this->invoice->status_id = $statusId;
    }

    public function getResponse()
    {
        return response()->json([
            'status' => 'ok',
            'callback_data' => $this->callback
        ], 200);
    }

    private function getInvoiceModel($refNo)
    {
        $row = Invoice::where('ref_no', $refNo)->first();

        if (empty($row))
            throw new NotFoundHttpException('Invoice penjualan tidak ditemukan.');

        return $row;
    }
}