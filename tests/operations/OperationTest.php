<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \Carbon\Carbon;

class OperationTest extends TestCase {

	public function testConstruct() {

		$op = new Operation();
		$this->assertInstanceOf(Operation::class, $op);
	}
}
