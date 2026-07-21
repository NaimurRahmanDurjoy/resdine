<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Models\DatabaseBackup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\ActivityLog;
use PDO;
use PDOException;

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
    public function clearCache(Request $request)
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
        $dbName = config('database.connections.mysql.database', env('DB_DATABASE'));
        $tables = DB::select("SELECT
            table_name AS name,
            ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
            FROM information_schema.TABLES
            WHERE table_schema = ?
            ORDER BY size_mb DESC", [$dbName]);

        $totalSize = array_sum(array_column($tables, 'size_mb'));

        $backups = DatabaseBackup::orderBy('created_at', 'desc')
            ->get()
            ->map(function ($backup) {
                return [
                    'id'                   => $backup->id,
                    'filename'             => $backup->filename,
                    'db_name'              => $backup->db_name,
                    'db_host'              => $backup->db_host,
                    'backup_type'          => $backup->backup_type,
                    'backup_type_label'    => $backup->backup_type_label,
                    'file_size'            => $backup->file_size,
                    'file_size_formatted'  => $backup->file_size_formatted,
                    'status'               => $backup->status,
                    'error_message'        => $backup->error_message,
                    'created_at'           => $backup->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return Inertia::render('DevAdmin/Systems/Database', [
            'tables'    => $tables,
            'totalSize' => $totalSize,
            'dbName'    => $dbName,
            'backups'   => $backups,
            'dbDefaults' => [
                'host'     => config('database.connections.mysql.host', env('DB_HOST', '127.0.0.1')),
                'port'     => config('database.connections.mysql.port', env('DB_PORT', '3306')),
                'database' => $dbName,
                'username' => config('database.connections.mysql.username', env('DB_USERNAME', 'root')),
            ],
        ]);
    }

    /**
     * Test a database connection with provided or env credentials.
     */
    public function testDatabaseConnection(Request $request)
    {
        $host     = $request->input('host')     ?: config('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        $port     = $request->input('port')     ?: config('database.connections.mysql.port', env('DB_PORT', '3306'));
        $database = $request->input('database') ?: config('database.connections.mysql.database', env('DB_DATABASE'));
        $username = $request->input('username') ?: config('database.connections.mysql.username', env('DB_USERNAME', 'root'));
        $password = $request->has('password') && $request->input('password') !== null 
                    ? $request->input('password') 
                    : config('database.connections.mysql.password', env('DB_PASSWORD', ''));

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password ?? '', [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT            => 5,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // Verify we can query
            $version = $pdo->query('SELECT VERSION() as version')->fetch();

            return response()->json([
                'success' => true,
                'message' => 'Connection successful!',
                'version' => $version['version'] ?? 'Unknown',
            ]);
        } catch (PDOException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Locate mysqldump binary executable on local system (Laragon/XAMPP/System PATH).
     */
    private function findMysqldumpBinary(): ?string
    {
        // Check system PATH first
        $testCmd = stripos(PHP_OS, 'WIN') !== false ? 'where mysqldump 2>&1' : 'which mysqldump 2>&1';
        exec($testCmd, $out, $code);
        if ($code === 0 && !empty($out[0])) {
            return 'mysqldump';
        }

        // Check Laragon MySQL paths
        $laragonPath = 'C:\\laragon\\bin\\mysql';
        if (File::exists($laragonPath)) {
            $dirs = File::directories($laragonPath);
            foreach ($dirs as $dir) {
                $exe = $dir . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'mysqldump.exe';
                if (File::exists($exe)) {
                    return '"' . $exe . '"';
                }
            }
        }

        // Check XAMPP MySQL path
        $xamppExe = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        if (File::exists($xamppExe)) {
            return '"' . $xamppExe . '"';
        }

        return null; // Binary not found, will use PDO fallback
    }

    /**
     * Create a database backup using mysqldump or PDO fallback.
     */
    public function backupDatabase(Request $request)
    {
        $request->validate([
            'backup_type' => 'required|in:data_only,structure_only,complete',
        ]);

        $host     = $request->input('host')     ?: config('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        $port     = (int) ($request->input('port') ?: config('database.connections.mysql.port', env('DB_PORT', '3306')));
        $database = $request->input('database') ?: config('database.connections.mysql.database', env('DB_DATABASE'));
        $username = $request->input('username') ?: config('database.connections.mysql.username', env('DB_USERNAME', 'root'));
        $password = $request->has('password') && $request->input('password') !== null 
                    ? $request->input('password') 
                    : config('database.connections.mysql.password', env('DB_PASSWORD', ''));

        $typeTag = match ($request->backup_type) {
            'data_only'      => 'data',
            'structure_only' => 'structure',
            default          => 'complete',
        };
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename  = "{$database}_{$typeTag}_{$timestamp}.sql";

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filePath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        // Try CLI mysqldump executable first
        $mysqldumpBin = $this->findMysqldumpBinary();

        if ($mysqldumpBin) {
            $escapedHost     = escapeshellarg($host);
            $escapedDatabase = escapeshellarg($database);
            $escapedUsername = escapeshellarg($username);
            $passwordFlag    = $password !== '' ? '--password=' . escapeshellarg($password) : '';

            $typeFlags = match ($request->backup_type) {
                'data_only'      => '--no-create-info',
                'structure_only' => '--no-data',
                default          => '',
            };

            $command = sprintf(
                '%s --host=%s --port=%d --user=%s %s %s %s --single-transaction --skip-lock-tables 2>&1',
                $mysqldumpBin,
                $escapedHost,
                $port,
                $escapedUsername,
                $passwordFlag,
                $typeFlags,
                $escapedDatabase
            );

            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar === 0 && !empty($output)) {
                file_put_contents($filePath, implode("\n", $output));
                $fileSize = filesize($filePath);

                DatabaseBackup::create([
                    'filename'    => $filename,
                    'db_host'     => $host,
                    'db_port'     => (string) $port,
                    'db_name'     => $database,
                    'db_username' => $username,
                    'file_path'   => $filePath,
                    'file_size'   => $fileSize,
                    'backup_type' => $request->backup_type,
                    'status'      => 'completed',
                ]);

                return back()->with('success', "Backup created successfully! File: {$filename}");
            }
        }

        // Fallback: Native PHP PDO SQL Dumper (works everywhere without mysqldump binary)
        try {
            $this->generatePdoBackup($host, $port, $database, $username, $password, $request->backup_type, $filePath);
            $fileSize = filesize($filePath);

            DatabaseBackup::create([
                'filename'    => $filename,
                'db_host'     => $host,
                'db_port'     => (string) $port,
                'db_name'     => $database,
                'db_username' => $username,
                'file_path'   => $filePath,
                'file_size'   => $fileSize,
                'backup_type' => $request->backup_type,
                'status'      => 'completed',
            ]);

            return back()->with('success', "Backup created successfully! File: {$filename}");
        } catch (\Exception $e) {
            DatabaseBackup::create([
                'filename'      => $filename,
                'db_host'       => $host,
                'db_port'       => (string) $port,
                'db_name'       => $database,
                'db_username'   => $username,
                'file_path'     => $filePath,
                'file_size'     => 0,
                'backup_type'   => $request->backup_type,
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Pure PHP PDO SQL Dumper fallback when mysqldump CLI binary is missing.
     */
    private function generatePdoBackup(string $host, int $port, string $database, string $username, string $password, string $type, string $outputPath): void
    {
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        ]);

        $handle = fopen($outputPath, 'w');
        if (!$handle) throw new \Exception("Cannot write to file path: {$outputPath}");

        fwrite($handle, "-- Resdine Database Backup\n");
        fwrite($handle, "-- Host: {$host}:{$port} | Database: {$database}\n");
        fwrite($handle, "-- Type: {$type}\n");
        fwrite($handle, "-- Date: " . date('Y-m-d H:i:s') . "\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // Write Structure
            if ($type === 'complete' || $type === 'structure_only') {
                fwrite($handle, "-- Table structure for table `{$table}`\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
                fwrite($handle, $createTable['Create Table'] . ";\n\n");
            }

            // Write Data
            if ($type === 'complete' || $type === 'data_only') {
                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    fwrite($handle, "-- Dumping data for table `{$table}`\n");
                    foreach ($rows as $row) {
                        $values = array_map(function ($value) use ($pdo) {
                            if ($value === null) return 'NULL';
                            return $pdo->quote($value);
                        }, array_values($row));

                        $columns = array_map(fn($col) => "`{$col}`", array_keys($row));
                        $sql = "INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                        fwrite($handle, $sql);
                    }
                    fwrite($handle, "\n");
                }
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    /**
     * Download a backup file.
     */
    public function downloadBackup(DatabaseBackup $backup)
    {
        if (!File::exists($backup->file_path)) {
            return back()->with('error', 'Backup file not found on disk.');
        }

        return response()->download($backup->file_path, $backup->filename, [
            'Content-Type'        => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $backup->filename . '"',
        ]);
    }

    /**
     * Delete a backup record and its file.
     */
    public function deleteBackup(DatabaseBackup $backup)
    {
        if (File::exists($backup->file_path)) {
            File::delete($backup->file_path);
        }

        $backup->delete();

        return back()->with('success', 'Backup deleted successfully.');
    }
}
