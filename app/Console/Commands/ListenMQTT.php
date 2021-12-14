<?php

namespace App\Console\Commands;

use App\Jobs\CreateDeviceData;
use Illuminate\Console\Command;
use Salman\Mqtt\Facades\Mqtt;

class ListenMQTT extends Command
{
    public $topic = 'devices/+/params/#';
    public $client_id = 'meteo-server-mqtt';

    protected $signature = 'listen:mqtt';

    protected $description = 'Слушает сообщения mqtt';

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
        Mqtt::ConnectAndSubscribe($this->topic, function ($topic, $msg) {
            $arr = explode('/', $topic);
            $deviceKey = $arr[1];
            $paramName = $arr[3];
            dispatch(new CreateDeviceData($deviceKey, $paramName, $msg));
        }, $this->client_id);
    }
}
