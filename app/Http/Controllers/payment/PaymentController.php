<?php

namespace App\Http\Controllers\payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Controllers\file\FileController;
use App\Http\Controllers\file\CsvController;

class PaymentController extends Controller
{
    /**
     * Display main page of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('payment');
    }

    /**
     * Receive a payment list with a CSV file.
     *
     * @param  \Illuminate\Http\StorePaymentRequest  $request
     * @return redirect to payments page
     */
    public function store(StorePaymentRequest $request)
    {
        $commissions = $this->getPaymentCommissions($request);
        return redirect()->back()->with('commissions', $commissions);
    }

    /**
     * Commission calculation starts from this method. This method
     * PaymentCommissionCalculatorTest calls this method
     *
     * @param  \Illuminate\Http\StorePaymentRequest  $request
     * @param  Boolean  $test
     * @return Array $commissions
     */
    public function getPaymentCommissions ($request, $test=false)
    {
        $path = "payment/";
        $fileUploaded = FileController::uploadFile($request['file'], $path);
        $csvData = (new CsvController)->read($fileUploaded);
        $payments = (new CommissionController($test))->calculateCommission($csvData, $test);
        $commissions = [];
        foreach ($payments as $payment)
        {
            array_push($commissions, $payment['commission']);
        }
        FileController::deleteFile($fileUploaded);
        return $commissions;
    }
}
