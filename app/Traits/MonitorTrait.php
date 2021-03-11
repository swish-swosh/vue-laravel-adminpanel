<?php
namespace App\Traits;

use Carbon\Carbon;
use App\Models\Monitor;
use App\Models\AccessRight;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

trait MonitorTrait {

    /**
     * calcuate when the monitor will perform next.
     *
     */
    public function setNextRun($item){

        
        $token = '';
        $nextRun = Carbon::createFromFormat("Y-m-d H:i:s", Carbon::now());

        // tokenize poling interval - -1 is not used = 0 otherwise time / date is set and used
        foreach ($item['polling_interval'] as $key => $value) {
            $token .= $value == '-1' ? '0' : '1';
        }
    
          // date logic
          $d = substr($token, 0, -3);
          switch($d){
            case '00':
            break;
            case '01':
              $nextRun->addDays($item['polling_interval']['day']+1);
            break;
            case '10':
              $nextRun->addMonths($item['polling_interval']['month']+1);
            break;
            case '11':
              $nextRun->addMonths($item['polling_interval']['month']+1);
              $nextRun->addDays($item['polling_interval']['day']+1);
            break;
          }
    
          // time logic
          $d = substr($token, 2);
          switch($d){
            case '000':
            break;
            case '001':
              $nextRun->addSeconds($item['polling_interval']['second']+1);
            break;
            case '010':
              $nextRun->addMinutes($item['polling_interval']['minute']+1);
            break;
            case '011':
              $nextRun->addSeconds($item['polling_interval']['second']+1);
              $nextRun->addMinutes($item['polling_interval']['minute']+1);
            break;
            case '100':
              $nextRun->addHours($item['polling_interval']['hour']+1);
            break;
            case '101':
              $nextRun->addHours($item['polling_interval']['hour']+1);
              $nextRun->addSeconds($item['polling_interval']['second']+1);
            break;
            case '110':
              $nextRun->addHours($item['polling_interval']['hour']+1);
              $nextRun->addMinutes($item['polling_interval']['minute']+1);
            break;
            case '111':
              $nextRun->addHours($item['polling_interval']['hour']+1);
              $nextRun->addMinutes($item['polling_interval']['minute']+1);
              $nextRun->addSeconds($item['polling_interval']['second']+1);
            break;
          }

            $monitor = Monitor::find($item['id']);
            $monitor['next_run'] = $nextRun;
            $monitor->save();
    }

}