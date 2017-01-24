<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MaintenanceWarning;
use DB;
class MaintenanceSet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:set {date} {time} {duration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the maintenance period';

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
        $date = $this->argument('date');
        $time = $this->argument('time');
        $duration = $this->argument('duration');
        DB::table('maintenance_warnings')->delete();
        MaintenanceWarning::create([
            'date' => $date,
            'time' => $time,
            'duration' => $duration,
            ]);
        $this->info("Next maintenance period created, Date: $date Time: $time Duration: $duration hour".($duration < 1 ? 's' : ''));
    }
}
