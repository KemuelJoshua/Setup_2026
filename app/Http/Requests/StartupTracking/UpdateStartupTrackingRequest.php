<?php

namespace App\Http\Requests\StartupTracking;

use App\Models\StartupTracking;

class UpdateStartupTrackingRequest extends StoreStartupTrackingRequest
{
    public function authorize(): bool
    {
        $startupTracking = $this->route('startup_tracking');

        return $startupTracking instanceof StartupTracking
            && $this->user()?->can('update', $startupTracking) === true;
    }
}
