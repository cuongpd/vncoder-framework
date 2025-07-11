<?php

namespace VnCoder\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use VnCoder\Models\VnConfig;
use VnCoder\Models\VnUser;

class WebsiteMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $maintenanceData = VnConfig::getMaintenanceData();
        if($maintenanceData['status'] == 1){
            return redirect()->to('maintenance.html');
        }
        return $next($request);
    }
}