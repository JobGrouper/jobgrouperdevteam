<?php

namespace App\Console\Commands;

use App\MaintenanceWarning;
use Illuminate\Console\Command;
use DB;

class MaintenanceClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removing the maintenance period';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(MaintenanceWarning::all()->count() > 0){
            DB::table('maintenance_warnings')->delete();
            $this->info("Next maintenance period removed");
        }
        else{
            $this->error("Maintenance period does not exist");
        }

    }
}
