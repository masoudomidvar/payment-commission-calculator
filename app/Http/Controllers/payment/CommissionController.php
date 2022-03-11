<?php

namespace App\Http\Controllers\payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function calculateCommission ($csvData)
    {
        $data = $this->labelCsvData($csvData);
        $csvDataRequest = new Request($data);
        $this->validateCsvData($csvDataRequest);
        $groupedData = $this->groupDataByUserId($data);
        return $groupedData;
    }

    /**
     * Add keys to csv data instead of numbers.
     *
     * @param  Array  $csvData
     * @return Array
     */
    public function labelCsvData ($csvData)
    {
        // $i = -1;
        return collect($csvData)
            ->map(function($row, $i=0){
                return [
                    'key' => $i,
                    'date' => $row[0],
                    'userId' => $row[1],
                    'userType' => $row[2],
                    'operationType' => $row[3],
                    'amount' => $row[4],
                    'currency' => $row[5],
                ];
                $i++;
            })
            ->toArray();
    }

    /**
     * group data by user id.
     *
     * @param  Array  $labeledData
     * @return Array
     */
    public function groupDataByUserId ($data)
    {
        return collect($data)
            ->groupBy('userId')
            ->toArray();
    }

    /**
     * validate data inside CSV file.
     *
     * @param  \Illuminate\Http\Request  $csvDataRequest
     * @return Error or nothing
     */
    public function validateCsvData (Request $csvDataRequest)
    {
        $csvDataRequest->validate(
            [
                '*.date' => [
                    'required',
                    'date'
                ],
                '*.userId' => [
                    'required',
                    'int'
                ],
                '*.userType' => [
                    'required',
                    'in:private,business'
                ],
                '*.operationType' => [
                    'required',
                    'in:deposit,withdraw'
                ],
                '*.amount' => [
                    'required',
                    'int',
                    'min:1'
                ],
                '*.currency' => [
                    'required',
                    'in:EUR,JPY,USD'
                ],
            ]
        );
    }
}
