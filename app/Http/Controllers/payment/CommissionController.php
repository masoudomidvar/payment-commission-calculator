<?php

namespace App\Http\Controllers\payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CommissionController extends Controller
{
    public function __construct()
    {
        $this->config = config('payment');
        $this->weeklyWithdrawAmount = 0;
        $this->weeklyWithdrawCount = 0;
        $this->currencyRates = json_decode(file_get_contents(config('payment.currencyRateSource')), true)['rates'];
    }

    public function calculateCommission ($csvData)
    {
        $data = $this->labelCsvData($csvData);
        $csvDataRequest = new Request($data);
        $this->validateCsvData($csvDataRequest);
        $groupedData = $this->groupDataByUserId($data);
        foreach ($groupedData as $userPayments)
        {
            $data = $this->calculateUserCommissions($data, $userPayments);
        }
        return $data;
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
     * Full validation of data inside CSV file.
     *
     * @param  \Illuminate\Http\Request  $csvDataRequest
     * @return Error or nothing
     */
    public function validateCsvData (Request $csvDataRequest)
    {
        $currencyRates = $this->currencyRates;
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
                    'numeric',
                    'min:1'
                ],
                '*.currency' => [
                    'required',
                    function ($attribute, $value, $fail) use ($currencyRates) {
                        if (!isset($currencyRates[$value]))
                            $fail("The $value currency is not supported.");
                    },
                ],
            ]
        );
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

    public function calculateUserCommissions ($data, $userPayments)
    {
        // Sort user payments by date ascending
        $userPayments = $this->sortUserPaymentsByDate($userPayments);

        $week = $this->defineWeek($userPayments[0]['date']);

        $weeklyWithdrawAmount = 0;
        $weeklyWithdraws = 0;
        $commissionFreeAmountDeducted = false;
        $commissionFreeAmount = $this->config['withdraw']['private']['commissionFreeAmount'];
        $commissionFreeLimit = $this->config['withdraw']['private']['commissionFreeLimit'];

        foreach ($userPayments as $payment) {
            $CommissionRate = $this->config[$payment['operationType']][$payment['userType']]['commission'];
            $amount = $payment['amount'];

            switch ($payment['operationType']) {
                case "deposit":
                    $data[$payment['key']]['commission'] = ($amount / 100) * $CommissionRate;
                    break;
                case "withdraw":
                    switch ($payment['userType']) {
                        case "business":
                            $data[$payment['key']]['commission'] = ($amount / 100) * $CommissionRate;
                            break;
                        case "private":
                            $date = Carbon::createFromDate($payment['date'])->format('Y-m-d');
                            $currencyToEuro = $this->convertCurrencyToEuro($amount, $payment['currency']);
                            if (($date >= $week['start']) && ($date <= $week['end'])) {
                                $weeklyWithdraws++;
                                $weeklyWithdrawAmount += $currencyToEuro;
                            }
                            else {
                                $weeklyWithdrawAmount = $currencyToEuro;
                                $weeklyWithdraws = 1;
                                $week = $this->defineWeek($payment['date']);
                            }
                            if ($weeklyWithdrawAmount <= $commissionFreeAmount && $weeklyWithdraws <= $commissionFreeLimit) {
                                $CommissionRate = 0;
                            } elseif ($weeklyWithdraws <= $commissionFreeLimit && $commissionFreeAmountDeducted == false) {
                                $commissionFreeAmountDeducted = true;
                                $amount = $weeklyWithdrawAmount - $commissionFreeAmount;
                                $amount = $this->convertEuroToCurrency($amount, $payment['currency']);
                            }
                            $decimalPlaces = strlen(substr(strrchr($payment['amount'], "."), 1));
                            $data[$payment['key']]['commission'] = $this->roundUp(($amount / 100) * $CommissionRate, $decimalPlaces-1);
                            break;
                    }
                    break;
            }
        }
        return $data;
    }

    public function sortUserPaymentsByDate ($userPayments)
    {
        usort($userPayments, function($a, $b) {
            return Carbon::createFromDate($a['date']) <=> Carbon::createFromDate($b['date']);
        });
        return $userPayments;
    }

    /**
     * Detect start and end of the week.
     *
     * @param  Date  $date
     * @return Array ['start' => $startDate, 'end' => $endDate]
     */
    public function defineWeek ($date)
    {
        $week= [];
        $startDate = Carbon::createFromDate($date);
        $week['start'] = $startDate->startOfWeek()->toDateString();
        $week['end'] = $startDate->endOfWeek()->toDateString();
        return $week;
    }

    private function convertCurrencyToEuro ($amount, $currency)
    {
        if ($currency === 'EUR')
            return $amount;
        return $amount / $this->currencyRates[$currency];
    }

    private function convertEuroToCurrency ($amount, $currency)
    {
        if ($currency === 'EUR')
            return $amount;
        return $amount * $this->currencyRates[$currency];
    }

    public function roundUp($number, $decimalPlaces) {
        if ($decimalPlaces <= 0)
            return ceil($number);
        return round($number, $decimalPlaces);
    }
}
