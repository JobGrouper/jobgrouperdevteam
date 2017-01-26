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
    protected $description = 'Removes the current maintenance period notice';

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
            $this->info("Maintenance period notice taken down");
        }
        else{
            $this->error("Error: No maintenance period has been set.");
        }

    }
}
