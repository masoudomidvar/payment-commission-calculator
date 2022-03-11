<?php

namespace App\Http\Controllers\payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Controllers\file\FileController;

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
        FileController::deleteFile($fileUploaded);
    }
}
