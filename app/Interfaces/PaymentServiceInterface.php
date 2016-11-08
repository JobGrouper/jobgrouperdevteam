<?php

namespace App\Interfaces;

interface PaymentServiceInterface {

	public function initialize();
	public function createCreditCardToken(array $creditCardData);
	public function createSubscription();
	public function createPayment();
}

?>
