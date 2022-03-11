<?php

namespace App\Http\Controllers\file;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CsvController extends Controller
{
    public function read ($csvFile)
    {
        $payment = [];
        if (($open = fopen(public_path()."/storage/".$csvFile, "r")) !== FALSE) {
            while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                $payment[] = $data;
            }
            fclose($open);
        }
        return $payment;
    }
}
