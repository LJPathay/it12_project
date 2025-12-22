<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RunQueueWorker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Only run if not in maintenance mode and not a console command
        if (app()->isDownForMaintenance()) {
            return;
        }

        // Circuit Breaker: If the online database is known to be down, don't run the worker
        // to avoid hanging the PHP process during the terminate phase.
        if (\Illuminate\Support\Facades\Cache::has('online_db_unavailable')) {
            // Log it once in a while or just return. Returning is safer for performance.
            return;
        }

        try {
            // Run the queue worker to process pending jobs (stop when empty to avoid hanging)
            // --stop-when-empty: Process all jobs on the queue and exit
            // --tries=3: Overridden by job config usually, but good fallback
            // We suppress output to avoid cluttering logs unnecessarily here, or we could log it.
            Artisan::call('queue:work', [
                '--stop-when-empty' => true,
                '--tries' => 3
            ]);

        } catch (\Exception $e) {
            // Log silently so we don't crash the already-sent response
            Log::warning('RunQueueWorker Middleware Error: ' . $e->getMessage());
        }
    }
}
