<?php

namespace Modules\Apps\Http\Middleware;

use Closure;

class SiteActivationChecker
{
   

   public function handle($request, Closure $next)
   {
       if(config('setting.other.site_activation',1) == 0) {

          abort(404);
       }

       return $next($request);
   }
}
