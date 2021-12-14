<?php

namespace App\Observers;

use App\Events\DeviceParamUpdate;
use App\Models\DeviceParams;

class DeviceParamsObserver
{
    /**
     * Handle the device params "created" event.
     *
     * @param  \App\DeviceParams  $deviceParams
     * @return void
     */
    public function created(DeviceParams $deviceParams)
    {
        broadcast(new DeviceParamUpdate($deviceParams));
    }

    /**
     * Handle the device params "updated" event.
     *
     * @param  \App\DeviceParams  $deviceParams
     * @return void
     */
    public function updated(DeviceParams $deviceParams)
    {
        broadcast(new DeviceParamUpdate($deviceParams));
    }

    /**
     * Handle the device params "deleted" event.
     *
     * @param  \App\DeviceParams  $deviceParams
     * @return void
     */
    public function deleted(DeviceParams $deviceParams)
    {
        //
    }

    /**
     * Handle the device params "restored" event.
     *
     * @param  \App\DeviceParams  $deviceParams
     * @return void
     */
    public function restored(DeviceParams $deviceParams)
    {
        //
    }

    /**
     * Handle the device params "force deleted" event.
     *
     * @param  \App\DeviceParams  $deviceParams
     * @return void
     */
    public function forceDeleted(DeviceParams $deviceParams)
    {
        //
    }
}
