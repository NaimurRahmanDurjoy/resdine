<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SystemController extends Controller
{
    /**
     * Clear all application caches.
     */
    public function clearCache()
    {
        Artisan::call('optimize:clear');

        return back()->with('success', 'Application cache cleared successfully!');
    }

    /**
     * Display Laravel logs.
     */
    public function viewLogs()
    {
        $logFile = storage_path('logs/laravel.log');

        $logs = File::exists($logFile)
            ? File::get($logFile)
            : 'No logs found.';

        // Optionally show last 200 lines only for performance
        $logs = collect(explode("\n", $logs))
            ->take(-100)
            ->implode("\n");

        return view('devAdmin.systems.logs', compact('logs'));
    }

    /**
     * Display basic database information.
     */
    public function databaseInfo()
    {
        $dbName = env('DB_DATABASE');
        $tables = DB::select("SELECT 
            table_name AS name,
            ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
            FROM information_schema.TABLES
            WHERE table_schema = ?
            ORDER BY size_mb DESC", [$dbName]);

        $totalSize = array_sum(array_column($tables, 'size_mb'));

        return view('devAdmin.systems.database', compact('tables', 'totalSize', 'dbName'));
    }
}
