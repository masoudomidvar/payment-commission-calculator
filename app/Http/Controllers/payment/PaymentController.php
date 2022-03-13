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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentRequest $request)
    {
        $path = "payment/";
        $fileUploaded = FileController::uploadFile($request->validated()['file'], $path);
        $csvData = (new CsvController)->read($fileUploaded);
        $commissions = (new CommissionController)->calculateCommission($csvData);
        echo "<br><br><br>";
        foreach ($commissions as $commission)
        {
            echo $commission['commission']."<br>";
        }
        FileController::deleteFile($fileUploaded);
    }
}
