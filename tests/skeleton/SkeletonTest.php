<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \Carbon\Carbon;
use App\Skeleton\Operation;

class SkeletonTest extends TestCase {

	public function testConstructOperation() {

		$op = new Operation();
		$this->assertInstanceOf(Operation::class, $op);
	}

	public function testOperationGo() {

		$op = new Operation();
		$op->go();
	}
}
