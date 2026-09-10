<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminSystemController extends Controller
{
    public function logs(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        $logContent = '';
        $fileSize = 0;

        if (File::exists($logPath)) {
            $fileSize = File::size($logPath);
            $lines = file($logPath);
            $totalLines = count($lines);
            
            // Get last 250 lines
            $slice = array_slice($lines, max(0, $totalLines - 250));
            
            if ($request->filled('filter')) {
                $filter = $request->filter;
                $slice = array_filter($slice, function ($line) use ($filter) {
                    return stripos($line, $filter) !== false;
                });
            }

            $logContent = implode('', $slice);
        } else {
            $logContent = 'No logs found. Laravel log file is empty or has not been generated yet.';
        }

        return view('admin.system.logs', compact('logContent', 'fileSize'));
    }

    public function clearLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
        }

        AuditLog::record('logs_cleared', 'Admin ' . Auth::user()->email . ' cleared application logs.', Auth::id());

        return back()->with('success', 'Application logs cleared successfully.');
    }

    public function backupDatabase(): StreamedResponse
    {
        AuditLog::record('database_backup_downloaded', 'Admin ' . Auth::user()->email . ' triggered and downloaded full database backup.', Auth::id());

        $driver = DB::connection()->getDriverName();
        $dbName = DB::getDatabaseName();

        $tableNames = [];
        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($tables as $t) {
                $tableNames[] = $t->name;
            }
        } else {
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $dbName;
            foreach ($tables as $t) {
                $tableName = $t->$tableKey ?? current((array) $t);
                if ($tableName) {
                    $tableNames[] = $tableName;
                }
            }
        }

        $headers = [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="backup_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $dbName) . '_' . date('Y-m-d_His') . '.sql"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($tableNames, $driver) {
            $out = fopen('php://output', 'w');
            fwrite($out, "-- VTU Platform Automated Database Backup Dump\n");
            fwrite($out, "-- Generated at: " . date('Y-m-d H:i:s') . "\n\n");
            
            if ($driver === 'mysql') {
                fwrite($out, "SET FOREIGN_KEY_CHECKS=0;\n\n");
            }

            foreach ($tableNames as $tableName) {
                // Create table statement
                if ($driver === 'sqlite') {
                    $createRes = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$tableName]);
                    $createSql = $createRes[0]->sql ?? '';
                } else {
                    $createRes = DB::select("SHOW CREATE TABLE `{$tableName}`");
                    $createSql = $createRes[0]->{'Create Table'} ?? '';
                }

                fwrite($out, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                if ($createSql) {
                    fwrite($out, $createSql . ";\n\n");
                }

                // Rows
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    foreach ($rows as $row) {
                        $rowArr = (array) $row;
                        $keys = array_keys($rowArr);
                        $escapedValues = array_map(function ($val) {
                            if (is_null($val)) return 'NULL';
                            return "'" . addslashes((string) $val) . "'";
                        }, array_values($rowArr));

                        $sql = "INSERT INTO `{$tableName}` (`" . implode('`, `', $keys) . "`) VALUES (" . implode(', ', $escapedValues) . ");\n";
                        fwrite($out, $sql);
                    }
                    fwrite($out, "\n");
                }
            }

            if ($driver === 'mysql') {
                fwrite($out, "SET FOREIGN_KEY_CHECKS=1;\n");
            }
            fclose($out);
        }, 200, $headers);
    }
}
