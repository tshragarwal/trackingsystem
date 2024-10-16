<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PublisherJobModel;
use Illuminate\Database\QueryException;

class ResetTrackingCountCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset_tracking_count:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset tracking count on daily basis.';

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
     * @return void
     */
    public function handle()
    {
        try {
            info("Reset tracking Cron Job running at ". now());
            $rows = PublisherJobModel::where('status', '=', 1 )->update(['tracking_count' => 0]);
            info("Reset tracking Cron Job completed at ". now() . " and affected rows are " . $rows);
            return;
        } catch (QueryException $qe) {
            logger($qe->getMessage());
        }
    }
}
