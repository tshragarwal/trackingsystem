<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Jenssegers\Agent\Agent;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getUserAgentDetails() {
        $agent = new Agent();
  
        // Get the browser name
        $browser = $agent->browser();
  
        // Get the browser version
        $version = $agent->version($browser);
  
        // Get the platform name (Operating System)
        $platform = $agent->platform();
  
        // Get the platform version
        $platformVersion = $agent->version($platform);
  
        // Check if the device is mobile
        $isMobile = $agent->isMobile();
  
        // Check if the device is a tablet
        $isTablet = $agent->isTablet();
  
        // Get device name
        $device = $agent->device();
  
        return [
            'browser' => $browser,
            'browser_version' => $version,
            'platform' => $platform,
            'platform_version' => $platformVersion,
            'is_mobile' => $isMobile,
            'is_tablet' => $isTablet,
            'device' => $device,
            'userAgent' => $_SERVER['HTTP_USER_AGENT'],
        ];
      }
}
