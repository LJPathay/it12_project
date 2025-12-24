<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncModelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $modelClass;
    protected $data;
    protected $action;
    protected $modelId;
    protected $table;

    /**
     * Create a new job instance.
     */
    public function __construct($model, $action)
    {
        $this->modelClass = get_class($model);
        $this->table = $model->getTable();
        // We convert to array to pass raw data
        $this->data = $model->getAttributes();
        $this->action = $action;
        $this->modelId = $model->getKey();
    }

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Circuit Breaker: Check if the online database is marked as unavailable
        // We use 'file' store to ensure we can read/write status even if DB is down.
        if (\Illuminate\Support\Facades\Cache::store('file')->has('online_db_unavailable')) {
            Log::info("SyncModelJob: Online database is marked as unavailable. Deferring sync for {$this->modelClass} ID: {$this->modelId}");
            // Re-queue with a delay (e.g., 5 minutes) to try again later
            $this->release(300); 
            return;
        }

        try {
            // Prevent self-syncing: if the default connection is already the target connection
            if (config('database.default') === 'pgsql_online') {
                Log::debug("SyncModelJob: Skipping sync as the default connection is already 'pgsql_online'.");
                return;
            }

            // Target the online connection
            $targetDb = DB::connection('pgsql_online');

            // Verify connection before attempting operations
            // We use a try-catch specifically for the connection to set the circuit breaker
            try {
                $targetDb->getPdo();
            } catch (\Exception $e) {
                Log::warning("SyncModelJob: Connection to online database failed. Activating circuit breaker. Error: " . $e->getMessage());
                
                // Set circuit breaker for 5 minutes (using file store)
                \Illuminate\Support\Facades\Cache::store('file')->put('online_db_unavailable', true, now()->addMinutes(5));
                
                // Re-queue the job
                $this->release(300);
                return;
            }

            if ($this->action === 'created' || $this->action === 'updated') {
                // Manually cast boolean values to strings 'true'/'false' for PostgreSQL 
                // when EMULATE_PREPARES is enabled (common in Supabase/Render)
                $data = array_map(function ($value) {
                    if (is_bool($value)) {
                        return $value ? 'true' : 'false';
                    }
                    return $value;
                }, $this->data);

                // Use updateOrInsert to handle both creation and updates, preserving the ID
                // We use the primary key (usually 'id') to match
                $targetDb->table($this->table)->updateOrInsert(
                    ['id' => $this->modelId],
                    $data
                );
            } elseif ($this->action === 'deleted') {
                $targetDb->table($this->table)->where('id', $this->modelId)->delete();
            }

            Log::info("Synced {$this->modelClass} ID: {$this->modelId} to online database. Action: {$this->action}");

        } catch (\Exception $e) {
            // Check if it's a connection error (Postgres code 08006 or general PDO connection issues)
            // SQLSTATE[08006] is "connection_failure"
            $isConnectionError = false;
            if ($e instanceof \PDOException) {
                if ($e->getCode() == 7 || $e->getCode() == '08006' || str_contains($e->getMessage(), 'could not translate host name')) {
                    $isConnectionError = true;
                }
            }

            if ($isConnectionError) {
                Log::warning("Offline/Connection Error syncing {$this->modelClass} ID: {$this->modelId}. Retrying... Error: " . $e->getMessage());
                // Also set circuit breaker if we hit it here
                \Illuminate\Support\Facades\Cache::store('file')->put('online_db_unavailable', true, now()->addMinutes(5));
            } else {
                Log::error("Failed to sync {$this->modelClass} ID: {$this->modelId}. Error: " . $e->getMessage());
            }

            // Throwing the exception will automatically trigger the retry logic with backoff
            throw $e;
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        // Exponential backoff: 30s, 1m, 2m, 5m, 10m, 20m, etc.
        return [30, 60, 120, 300, 600, 1200, 2400, 3600];
    }
}
