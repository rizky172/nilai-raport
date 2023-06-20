<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class ThemeController extends Controller
{
    // ================== PDF Theme ==============
    public function pdfLayout()
    {
        $pdf = PDF::loadView('theme.pdf.layout');
        return $pdf->stream();
    }

    public function pdfPr()
    {
        $pdf = PDF::loadView('theme.pdf.pr');
        return $pdf->stream();
        // return view('theme.pdf.po');
    }

    public function pdfPo()
    {
        $pdf = PDF::loadView('theme.pdf.po');
        return $pdf->stream();
        // return view('theme.pdf.po');
    }

    public function pdfReceipt()
    {
        $pdf = PDF::loadView('theme.pdf.receipt');
        return $pdf->stream();
        // return view('theme.pdf.invoice');
    }

    public function template()
    {
        $pdf = PDF::loadView('theme.pdf.template');
        return $pdf->stream();
        return view('theme.pdf.template');
    }

    public function kwitansi()
    {
        $pdf = PDF::loadView('theme.pdf.kwitansi');
        return $pdf->stream();
        // return view('theme.pdf.kwitansi');
    }

    public function labaRugi()
    {
        $pdf = PDF::loadView('theme.pdf.laba-rugi');
        return $pdf->stream();
        // return view('theme.pdf.laba-rugi');
    }

    public function neracaSaldo()
    {
        $pdf = PDF::loadView('theme.pdf.neraca-saldo');
        return $pdf->stream();
        // return view('theme.pdf.laba-rugi');
    }

    public function pdfSq()
    {
        $pdf = PDF::loadView('theme.pdf.sq');
        return $pdf->stream();
        // return view('theme.pdf.sq');
    }

    public function pdfSo()
    {
        $pdf = PDF::loadView('theme.pdf.so');
        return $pdf->stream();
        // return view('theme.pdf.so');
    }

    public function pdfDo()
    {
        $pdf = PDF::loadView('theme.pdf.do');
        return $pdf->stream();
        // return view('theme.pdf.do');
    }

    public function pdfSj()
    {
        $pdf = PDF::loadView('theme.pdf.sj');
        return $pdf->stream();
        // return view('theme.pdf.sj');
    }

    public function pdfSalesInvoice()
    {
        $pdf = PDF::loadView('theme.pdf.sales-invoice');
        return $pdf->stream();
        // return view('theme.pdf.do');
    }

    public function pdfByName($name)
    {
        $pdf = PDF::loadView('theme.pdf.' . $name);
        return $pdf->stream();
        // return view('theme.pdf.do');
    }

    public function pdfSalarySlip()
    {
        $pdf = PDF::loadView('theme.pdf.salary-slip');
        return $pdf->stream();
        // return view('theme.pdf.do');
    }
    
    public function test()
    {
        $pdf = PDF::loadView('theme.pdf.test');
        return $pdf->stream();
        // return view('theme.pdf.do');
    }

    // Show A4 Paper
    public function a4Paper()
    {
        $pdf = PDF::loadView('theme.pdf.a4-paper');
        return $pdf->stream();
        // return view('theme.pdf.a4-paper');
    }

    // Testing various header type
    public function headerTest()
    {
        $pdf = PDF::loadView('theme.pdf.header-test');
        // return $pdf->stream();
        return view('theme.pdf.header-test');
    }

    public function pdfHeaderFooter()
    {
        $data['type'] = 'dot-matrix';
        // $data['paper'] = 'dot-matrix';
        $pdf = PDF::loadView('theme.pdf.header-footer',$data);
        return $pdf->stream();
        // return view('theme.pdf.header-footer',$data);
    }
}