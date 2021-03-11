<?php

namespace App\Listeners;

use ping\JJG\Ping;

use GuzzleHttp\Client;
use App\Models\MonitorLog;
use App\Events\MonitorAction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MonitorActionNotification // implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MonitorAction  $event
     * @return void
     */
    public function handle(MonitorAction $event)
    {
        Log::notice('monitor called for: ' . $event->monitor->address);

        $MB = 1048576;
        $GB = 1073741824;

        switch($event->monitor->monitorType->name)
        {
            
            case 'PhpSysInfo':

                $client = new Client(['verify' => false]);
                $options = [
                    'http_errors'     => true,
                    'connect_timeout' => 5,
                    'read_timeout'    => 5,
                    'timeout'         => 10,
                ];
         
                $headers = [
                   // 'auth' => [$event->monitor->login, Crypt::decryptString($event->monitor->password)],
                   // 'cert' => 'c:\wamp64\bin\php\php7.3.12\extras\ssl\cacert.pem'
                ];
         
                $get = $client->request('GET', $event->monitor->address, $headers , $options);
                $data = json_decode($get->getBody()->getContents(), true);
                
                // memory resources
                $result['memory']['free'] = (float) number_format($data['Memory']['@attributes']['Free'] / $MB, 2, '.', '');
                $result['memory']['used'] = (float) number_format($data['Memory']['@attributes']['Used'] / $MB, 2, '.', '');
                $result['memory']['total'] = (float) number_format($data['Memory']['@attributes']['Total'] / $MB, 2, '.', '');
                $result['memory']['percent'] = (float) $data['Memory']['@attributes']['Percent'];
                
                // network resources
                foreach($data['Network']['NetDevice'] as $key => $value){

                    $result['network'][$data['Network']['NetDevice'][$key]['@attributes']['Name']]['drops'] = 
                        (int) $data['Network']['NetDevice'][$key]['@attributes']['Drops'];

                    $result['network'][$data['Network']['NetDevice'][$key]['@attributes']['Name']]['error'] = 
                        $data['Network']['NetDevice'][$key]['@attributes']['Err'];
                };
                
            
                // processor resources
                $n = 1;
                foreach($data['Hardware']['CPU']['CpuCore'] as $key => $value){

                    $result['cpu']['core'.$n]['model'] = 
                        $data['Hardware']['CPU']['CpuCore'][$key]['@attributes']['Model'];

                    $result['cpu']['core'.$n]['speed'] = 
                        (float) $data['Hardware']['CPU']['CpuCore'][$key]['@attributes']['CpuSpeed'];

                    $result['cpu']['core'.$n]['load'] = 
                        (float) $data['Hardware']['CPU']['CpuCore'][$key]['@attributes']['Load'];

                    $n++;
                };


                // storage resources
                foreach($data['FileSystem']['Mount'] as $key => $value){

                    $result['storage']['mountPointID-'.$data['FileSystem']['Mount'][$key]['@attributes']['MountPointID']]['name'] = 
                        $data['FileSystem']['Mount'][$key]['@attributes']['Name'];

                    $result['storage']['mountPointID-'.$data['FileSystem']['Mount'][$key]['@attributes']['MountPointID']]['free'] = 
                        (float)number_format($data['FileSystem']['Mount'][$key]['@attributes']['Free'] / $GB, 2, '.', '');

                    $result['storage']['mountPointID-'.$data['FileSystem']['Mount'][$key]['@attributes']['MountPointID']]['used'] = 
                        (float)number_format($data['FileSystem']['Mount'][$key]['@attributes']['Used'] / $GB, 2, '.', '');

                    $result['storage']['mountPointID-'.$data['FileSystem']['Mount'][$key]['@attributes']['MountPointID']]['total'] = 
                        (float)number_format($data['FileSystem']['Mount'][$key]['@attributes']['Total'] / $GB, 2, '.', '');

                    $result['storage']['mountPointID-'.$data['FileSystem']['Mount'][$key]['@attributes']['MountPointID']]['percent'] = 
                        (float)number_format($data['FileSystem']['Mount'][$key]['@attributes']['Percent'] , 2, '.', '');
                };

                $logItem['data'] = [$result];
                $logItem['user_id'] = $event->monitor->user->id;
                $logItem['monitor_type_id'] = $event->monitor->monitorType->id;
                $monitorLog = MonitorLog::create($logItem);

            break;

            case 'DirectAdmin':

                $client = new Client(['verify' => false]);
                $options = [
                    'http_errors'     => true,
                    'connect_timeout' => 5,
                    'read_timeout'    => 5,
                    'timeout'         => 10,
                ];
         
                $headers = [
                   'auth' => [$event->monitor->login, Crypt::decryptString($event->monitor->password)],
                   // 'cert' => 'c:\wamp64\bin\php\php7.3.12\extras\ssl\cacert.pem'
                ];
                $address = "{$event->monitor->address}/CMD_API_SHOW_USER_USAGE";
                $result = $client->request('GET', $address, $headers , $options);
                parse_str($result->getBody()->getContents(), $output);

                // check if min key exists - (wrong pw will deliver html)
                if (array_key_exists('bandwidth', $output)) {

                    $data['bandwidth'] = (float) $output['bandwidth'];
                    $data['storage'] = (float) $output['quota'];
                    $data['database'] = (float) $output['db_quota'] / $MB;
                    $data['emails'] = (int) $output['nemails'];
                    $data['subdomains'] = (int) $output['nsubdomains'];

                    $logItem['data'] = [$data];
                    $logItem['user_id'] = $event->monitor->user->id;
                    $logItem['monitor_type_id'] = $event->monitor->monitorType->id;
                    $monitorLog = MonitorLog::create($logItem);
                }
            break;

            case 'Ping':
                $ping = new \JJG\Ping($event->monitor->address);
                $latency = $ping->ping();
                $logItem = [];

                if ($latency === false) {
                    $logItem['data'] = [array("latency" => "lost")];
                }
                else {
                    $logItem['data'] = [array("latency" => $latency)];
                }

                $logItem['user_id'] = $event->monitor->user->id;
                $logItem['monitor_type_id'] = $event->monitor->monitorType->id;
                $monitorLog = MonitorLog::create($logItem);

            break;
        }

    }
}
