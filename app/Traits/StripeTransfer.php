<?php

trait StripeTransferEvent {

	function getEventVariables($event) {  

		$vars = array(
			'account_id' => $event['user_id'],
			'date_created' => $event['data']['created'],
			'arrival_date' => $event['data']['date'],
			'amount_raw' => $event['data']['amount'], // given in cents
			'amount' => $event['data']['amount'] / 100,
			'application_fee_raw' => $event['data']['application_fee'], // given in cents
			'application_fee' => $event['data']['application_fee'] / 100,
			'bank_account_name' => $event['data']['bank_account']['account_holder_name'],
			'bank_account_last_4' => $event['data']['bank_account']['last_4'],
			'bank_account_bank' => $event['data']['bank_account']['bank_name']
		);

		return $vars;
	}
}

?>
