
# Payment Commission Calculator

## Description

This is a simple web application for calculating commissions of different users' payments, by certain rules, which is written by the latest version of Laravel framework (9 at the moment).
The commission is calculated differently based on user type, operation type, weekly withdraw counts, weekly withdraw amount and etc. These payment records are given to the application by a CSV file. Then certain validations are done to assure that application receives proper values. Finally commissions for each record is calculated and displayed to the user.

## Requirements

- PHP >= 8.0
- Composer

## Installation

Clone the repository

    git clone https://github.com/masoudomidvar/payment-commission-calculator.git

Switch to the repo folder

    cd payment-commission-calculator

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

	cp .env.example .env

Generate a new application key

	php artisan key:generate

Start the local development server

	php artisan serve

You can now access the server at [http://localhost:8000](http://localhost:8000/)

***Note*** : This project does not require any database connection.

## Config

- `config/payment.php`
This file contains all the configurations used in the application. Please keep in mind that if you decided to change any of the defined values, do not forget to run below command:
	php artisan config:cache


## Code overview


### Main Files

- `app/Http/Controllers/payment/PaymentController` - Contains index and store methods to show and receive payment respectively.
- `App/Http/Requests/StorePaymentRequest` - Contains validation of the input which is a CSV file. It is used in PaymentController store method.
- `app/Http/Controllers/payment/CommissionController` - Contains all the related methods to calculate commissions. In this Controller data inside the CSV file is also validated.
- `app/Http/Controllers/file/FileController` - Contains methods to handle a uploaded file like saving and deleting the file.
- `app/Http/Controllers/file/CsvController` - Contains methods to read the CSV file.
- `config/payment.php` - Contains all the configurations used in the app.
- `routes/web.php` - Contains all the web defined routes
- `tests/Feature/Payment/PaymentCommissionCalculatorTest` - Contains the related test for Payment Commission Calculation

### Environment variables

- `.env` - Environment variables can be set in this file


## Test

In order to test the application please run below command

	php artisan test

If the tests pass you will see a mark named PASS beside the test otherwise FAIL is displayed.

- `tests/Feature/Payment/PaymentCommissionCalculatorTest` - Contains the related test for Payment Commission Calculation


## Contact

If you had any questions please feel free to contact me at:
Email: masoudomidvar7@gmail.com
Phone: +989122139474

----------