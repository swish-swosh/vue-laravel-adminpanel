<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Monitor;
use App\Traits\MonitorTrait;
use App\Events\MonitorAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\MonitorCollection;

class Monitors extends Command
{
    use MonitorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:servers';
    private $monitors = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'monitor remote servers, every minute called';

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
     * @return int
     */
    public function handle()
    {
        $now = Carbon::createFromFormat("Y-m-d H:i:s", Carbon::now());
        $this->monitors = Monitor::all()->toArray();

        // adjust timezone... todo
        foreach ($this->monitors as $n => &$m) {

            // check if 'now' date time is greater than nect run date time in database
            if($now->greaterThanOrEqualTo($this->monitors[$n]['next_run'])) {
                
                // update db, calculate next run date polling_interval settings.
                $this->setNextRun($this->monitors[$n]);

                // carry out monitor action via event
                event(new MonitorAction(Monitor::find($this->monitors[$n]['id'])));
                // Log::notice('updating monitor id:'.$this->monitors[$n]['id']);
            }    
            else{
                // Log::notice('notting to update for:'.$this->monitors[$n]['id']);
            } 
        }
  
    }
}
