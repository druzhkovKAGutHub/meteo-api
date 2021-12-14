<?php

namespace App\Jobs;

use App\Events\DeviceParamUpdate;
use App\Models\Device;
use App\Models\DeviceParams;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDeviceData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deviceKey;
    public $paramName;
    public $value;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($deviceKey, $paramName, $value)
    {
        $this->deviceKey = $deviceKey;
        $this->paramName = $paramName;
        $this->value = $value;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $device = Device::firstOrCreate(['key' =>  $this->deviceKey], ['name' => $this->deviceKey, 'status' => 'on']);
        $timestamp = date('Y-m-d H:i:s');

        if ($device !== null) {
            $device->lastUpdate = $timestamp;
            $device->save();
            
            $param = DeviceParams::firstOrCreate(
                [
                    'id_device' => $device->id,
                    'name' => $this->paramName
                ],
                [
                    'label' => $this->paramName,
                    'value' => $this->value,
                ]
            );
            $param->value = $this->value;
            $param->save();
            
            $table_name = "ParamsData" . $param->id;
            if (!Schema::hasTable($table_name)) {
                if (gettype($this->value) == "string") {
                    Schema::create($table_name, function (Blueprint $table) {
                        $table->increments('id');
                        $table->string('value', 255);
                        $table->timestamp('time');
                    });
                } else {
                    Schema::create($table_name, function (Blueprint $table) {
                        $table->increments('id');
                        $table->float('value')->nullable();
                        $table->timestamp('time');
                    });
                }
            }
            $timestamp = date('Y-m-d H:i:s');
            DB::table($table_name)->insert(
                array('value' => $this->value, 'time' => $timestamp)
            );
        }
    }
}
