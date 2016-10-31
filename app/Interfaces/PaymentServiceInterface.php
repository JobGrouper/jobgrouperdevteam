<?php

namespace App\Interfaces;

interface PaymentServiceInterface {

	public function initialize();
	public function createCreditCardToken();
	public function createPayment();
}

?>
