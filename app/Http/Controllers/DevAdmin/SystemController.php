<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use App\Models\ActivityLog;

class SystemController extends Controller
{
    public function activityLogs()
    {
        $logs = ActivityLog::with('user')->orderBy('id', 'desc')->paginate(50);

        return Inertia::render('DevAdmin/Systems/ActivityLogs', [
            'logs' => $logs
        ]);
    }

    public function queueMonitor()
    {
        $pendingJobs = DB::table('jobs')->count();
        $failedJobsCount = DB::table('failed_jobs')->count();
        $failedJobs = DB::table('failed_jobs')->orderBy('id', 'desc')->take(20)->get();
        $recentJobs = DB::table('jobs')->orderBy('id', 'desc')->take(20)->get();

        return Inertia::render('DevAdmin/Systems/QueueMonitor', [
            'pendingJobs' => $pendingJobs,
            'failedJobsCount' => $failedJobsCount,
            'failedJobs' => $failedJobs,
            'recentJobs' => $recentJobs,
        ]);
    }

    public function cacheManagement()
    {
        return Inertia::render('DevAdmin/Systems/CacheManagement');
    }

    /**
     * Clear application caches.
     */
    public function clearCache(\Illuminate\Http\Request $request)
    {
        $type = $request->input('type', 'all');

        try {
            switch ($type) {
                case 'config':
                    Artisan::call('config:clear');
                    $message = 'Configuration cache cleared successfully!';
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    $message = 'Route cache cleared successfully!';
                    break;
                case 'view':
                    Artisan::call('view:clear');
                    $message = 'View cache cleared successfully!';
                    break;
                case 'application':
                    Artisan::call('cache:clear');
                    $message = 'Application cache cleared successfully!';
                    break;
                default:
                    Artisan::call('optimize:clear');
                    $message = 'All caches cleared successfully!';
                    break;
            }
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
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

        return Inertia::render('DevAdmin/Systems/Logs', [
            'logs' => $logs
        ]);
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

        return Inertia::render('DevAdmin/Systems/Database', [
            'tables' => $tables,
            'totalSize' => $totalSize,
            'dbName' => $dbName
        ]);
    }
}
