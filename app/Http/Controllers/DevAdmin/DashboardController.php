<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // Business metrics
        $totalUsers = DB::table('users')->count();
        $ordersToday = DB::table('order_masters')
            ->whereDate('created_at', now())
            ->count();
        $revenueToday = DB::table('order_masters')
            ->whereDate('created_at', now())
            ->sum('total_amount');

        // Database size
        $dbName = env('DB_DATABASE');
        $databaseSize = DB::selectOne("
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.tables 
            WHERE table_schema = ?
            GROUP BY table_schema
        ", [$dbName]);
        $databaseSize = $databaseSize->size_mb ?? 0;

        // System health
        $serverLoad = $this->checkServerLoad();
        $systemHealth = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'queue' => $this->checkQueue(),
            'payment_gateway' => $this->checkPaymentGateway(),
            'server_load' => $serverLoad,
        ];

        // Errors today from Laravel log file
        $errorsToday = $this->errorsToday();

        return view('devAdmin.dashboard', compact(
            'totalUsers',
            'ordersToday',
            'revenueToday',
            'databaseSize',
            'errorsToday',
            'systemHealth',
            'serverLoad'
        ));
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return 'Connected';
        } catch (\Throwable $e) {
            return 'Disconnected';
        }
    }

    private function checkCache()
    {
        try {
            Cache::put('health_check', true, 10);
            return Cache::has('health_check') ? 'Healthy' : 'Unhealthy';
        } catch (\Throwable $e) {
            return 'Unhealthy';
        }
    }

    private function checkQueue()
    {
        return DB::table('jobs')->count() > 0 ? 'Processing' : 'Idle';
    }

    private function checkPaymentGateway()
    {
        try {
            $response = Http::timeout(3)->get('https://api.stripe.com'); // Replace with your payment API
            return $response->successful() ? 'Online' : 'Offline';
        } catch (\Throwable $e) {
            return 'Offline';
        }
    }

    private function checkServerLoad()
    {
        // Local development (Windows or local environment)
        if (app()->environment('local') || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'Dev Only';
        }

        // Linux / cPanel server
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg(); // returns [1min, 5min, 15min averages]
            return round($load[0], 2) . ' (1 min avg)';
        }

        return 'Unavailable';
    }


    private function errorsToday()
    {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return 0;
        }

        $lines = file($logFile);
        $today = date('Y-m-d');
        $count = 0;

        foreach ($lines as $line) {
            if (str_contains($line, $today) && str_contains(strtolower($line), 'error')) {
                $count++;
            }
        }

        return $count;
    }
}
