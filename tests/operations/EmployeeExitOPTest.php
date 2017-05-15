<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \Carbon\Carbon;
use App\Operations\EmployeeExitOP;

class EmployeeExitOPTest extends TestCase {

	use DatabaseTransactions;

	public function setUp() {

		parent::setUp();
		$this->op = \App::make('App\Operations\EmployeeExitOP');
	}

	public function testConstruct() {

		$this->assertInstanceOf(EmployeeExitOP::class, $this->op);
	}

	public function testGo() {

		$this->op->go();
	}
}
