<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Menggunakan query builder agar pagination di-handle oleh DataTables
                $query = ActivityLog::with('user.profile')
                    ->select('activity_logs.*')
                    ->orderBy('created_at', 'desc');

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('fullname', function ($log) {
                        if ($log->user && $log->user->profile) {
                            return $log->user->profile->first_name . ' ' . $log->user->profile->last_name;
                        }
                        return $log->user ? $log->user->username : 'System';
                    })
                    ->editColumn('metadata', function ($log) {
                        if (empty($log->metadata)) {
                            return '<span class="text-muted">No metadata</span>';
                        }

                        // Format JSON metadata
                        $metadata = json_decode($log->metadata, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($metadata)) {
                            $output = '<div class="metadata-preview">';

                            // Jika ada data yang terlalu banyak, tampilkan tombol untuk melihat detail
                            if (count($metadata) > 3 || strlen(json_encode($metadata)) > 100) {
                                $output .= '<button class="btn btn-sm btn-outline-primary mb-1 metadata-toggle" data-id="' . $log->id . '">Show Details</button>';
                                $output .= '<div id="metadata-' . $log->id . '" class="collapse">';
                                $output .= '<pre class="bg-light p-2 mt-2 rounded" style="max-height: 150px; overflow-y: auto;">'
                                    . json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                                    . '</pre>';
                                $output .= '</div>';
                            } else {
                                // Tampilkan langsung untuk data kecil
                                $output .= '<pre class="bg-light p-2 rounded" style="max-height: 80px; overflow-y: auto;">'
                                    . json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                                    . '</pre>';
                            }

                            $output .= '</div>';
                            return $output;
                        }

                        return '<span class="text-muted">Invalid JSON</span>';
                    })
                    ->addColumn('action_buttons', function ($log) {
                        return '<button class="btn btn-info btn-sm me-1 btn-detail" data-id="' . $log->id . '">
                                <i class="bx bx-detail"></i> Details
                            </button>';
                    })
                    ->rawColumns(['metadata', 'action_buttons'])
                    ->make(true);
            }

            // Statistik untuk view non-AJAX
            $totalLogs = ActivityLog::count();
            $totalToday = ActivityLog::whereDate('created_at', now()->toDateString())->count();
            $topActions = ActivityLog::select('action', \DB::raw('count(*) as count'))
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            return view('dashboard.activity-logs.index', compact('totalLogs', 'totalToday', 'topActions'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $log = ActivityLog::with('user.profile')->findOrFail($id);

            // Format metadata jika ada
            if (!empty($log->metadata)) {
                $metadata = json_decode($log->metadata, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $log->formatted_metadata = $metadata;
                } else {
                    $log->formatted_metadata = $log->metadata;
                }
            }

            // Ambil informasi tambahan jika perlu
            if ($log->target_table && $log->target_id) {
                try {
                    // Coba dapatkan model terkait jika masih ada
                    $targetModelClass = $this->getModelClassFromTable($log->target_table);
                    if ($targetModelClass && class_exists($targetModelClass)) {
                        $targetModel = $targetModelClass::find($log->target_id);
                        if ($targetModel) {
                            $log->target_object = $targetModel;
                            $log->target_exists = true;
                        } else {
                            $log->target_exists = false;
                        }
                    }
                } catch (\Exception $e) {
                    // Tangani jika terjadi error saat mengambil model terkait
                    $log->target_error = $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Activity log details fetched successfully.',
                'log' => $log
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper method to determine the model class from table name
     */
    private function getModelClassFromTable($table)
    {
        // Map table names to model classes
        $mapping = [
            'users' => \App\Models\User::class,
            'products' => \App\Models\Product::class,
            'sections' => \App\Models\Section::class,
            'categories' => \App\Models\Category::class,
            'system_settings' => \App\Models\SystemSetting::class,
            // Tambahkan mapping lain sesuai kebutuhan
        ];

        return $mapping[$table] ?? null;
    }

    /**
     * Get statistics for log data
     */
    public function getStats(Request $request)
    {
        try {
            $period = $request->input('period', 'today');

            $query = ActivityLog::query();

            // Filter berdasarkan periode
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', now()->subDay()->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
                default:
                    break;
            }

            // Statistik berdasarkan action
            $actionStats = $query->clone()
                ->select('action', \DB::raw('count(*) as count'))
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            // Statistik berdasarkan user
            $userStats = $query->clone()
                ->select('user_id', \DB::raw('count(*) as count'))
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->with('user.profile')
                ->get()
                ->map(function ($item) {
                    return [
                        'user_id' => $item->user_id,
                        'username' => $item->user ? $item->user->username : 'System',
                        'name' => $item->user && $item->user->profile ?
                            $item->user->profile->first_name . ' ' . $item->user->profile->last_name :
                            ($item->user ? $item->user->username : 'System'),
                        'count' => $item->count
                    ];
                });

            // Statistik berdasarkan tabel target
            $tableStats = $query->clone()
                ->select('target_table', \DB::raw('count(*) as count'))
                ->whereNotNull('target_table')
                ->groupBy('target_table')
                ->orderBy('count', 'desc')
                ->get();

            // Total aktivitas
            $totalCount = $query->count();

            // Aktivitas per hari (untuk grafik)
            $dailyStats = $query->clone()
                ->select(\DB::raw('DATE(created_at) as date'), \DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->limit(30)
                ->get();

            return response()->json([
                'success' => true,
                'stats' => [
                    'total' => $totalCount,
                    'byAction' => $actionStats,
                    'byUser' => $userStats,
                    'byTable' => $tableStats,
                    'daily' => $dailyStats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}