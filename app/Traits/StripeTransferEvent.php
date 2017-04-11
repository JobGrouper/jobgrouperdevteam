<?php

namespace App\Traits;

use \Carbon\Carbon;

trait StripeTransferEvent {

	function getEventVariables($event) {  

		$vars = array(
			'account_id' => $event['user_id'],
			'date_created' => $event['data']['object']['created'],
			'arrival_date_raw' => $event['data']['object']['arrival_date'],
			'arrival_date' => Carbon::createFromTimestamp( $event['data']['object']['arrival_date'] )->format('m-d-Y'),
			'amount_raw' => $event['data']['object']['amount'], // given in cents
			'amount' => $event['data']['object']['amount'] / 100,
			//'application_fee_raw' => $event['data']['object']['application_fee'], // given in cents
			//'application_fee' => $event['data']['object']['application_fee'],
			//'bank_account_name' => $event['data']['object']['bank_account']['account_holder_name'],
			//'bank_account_last_4' => $event['data']['object']['bank_account']['last4'],
			//'bank_name' => $event['data']['object']['bank_account']['bank_name']
		);

		return $vars;
	}
}

?>
