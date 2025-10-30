<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Yajra\DataTables\Facades\DataTables;

class SystemSettingController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                if ($request->has('get_stats')) {
                    // Khusus untuk permintaan statistik
                    $stats = [
                        'totalSettingActive' => SystemSetting::where('status', 'ACTIVE')->count(),
                        'totalSettingInactive' => SystemSetting::where('status', 'INACTIVE')->count(),
                    ];

                    return response()->json(['success' => true, 'stats' => $stats]);
                }

                // Gunakan query builder langsung ke DataTables agar pagination bisa di-handle di level database
                $query = SystemSetting::select('*');

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('action', function ($setting) {
                        $btn = '<button class="btn btn-info btn-sm me-1 btn-detail" data-id="' . $setting->id . '">
                                <i class="bx bx-bullseye"></i>
                             </button>';
                        $btn .= '<button class="btn btn-warning btn-sm me-1 btn-edit"
                                    data-id="' . $setting->id . '"
                                    data-key="' . $setting->key . '"
                                    data-value="' . htmlspecialchars($setting->value, ENT_QUOTES) . '"
                                    data-type="' . $setting->type . '"
                                    data-description="' . htmlspecialchars($setting->description, ENT_QUOTES) . '"
                                    data-status="' . $setting->status . '">
                                    <i class="bx bx-edit"></i>
                             </button>';
                        $btn .= '<button class="btn btn-danger btn-sm btn-delete" data-id="' . $setting->id . '">
                                <i class="bx bx-trash"></i>
                             </button>';
                        return $btn;
                    })
                    ->editColumn('type', function ($row) {
                        $type = $row->type;
                        $typeColors = [
                            'STRING' => 'primary',
                            'INTEGER' => 'info',
                            'BOOLEAN' => 'success',
                            'JSON' => 'warning',
                            'URL' => 'dark',
                        ];

                        $color = $typeColors[$type] ?? 'secondary';
                        return '<span class="badge bg-' . $color . '">' . $type . '</span>';
                    })
                    ->editColumn('status', function ($row) {
                        $status = $row->status;
                        $statusColors = [
                            'ACTIVE' => 'success',
                            'INACTIVE' => 'secondary',
                            'ARCHIVED' => 'info',
                            'DELETED' => 'dark',
                        ];
                        $labels = [
                            'ACTIVE' => 'Active',
                            'INACTIVE' => 'Inactive',
                            'ARCHIVED' => 'Archived',
                            'DELETED' => 'Deleted',
                        ];
                        $color = $statusColors[$status] ?? 'secondary';
                        $label = $labels[$status] ?? 'Unknown';
                        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
                    })
                    ->editColumn('value', function ($row) {
                        // Ambil nilai dan tipe
                        $value = $row->value;
                        $type = $row->type;

                        if ($type === 'JSON') {
                            // Decode HTML entities jika ada
                            $decodedValue = html_entity_decode($value);
                            try {
                                $decodedJson = json_decode($decodedValue, true);
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    $prettyJson = json_encode($decodedJson, JSON_PRETTY_PRINT);
                                    if (strlen($prettyJson) > 100) {
                                        $prettyJson = substr($prettyJson, 0, 97) . '...';
                                    }
                                    return '<pre style="margin: 0; white-space: pre-wrap; max-height: 50px; overflow: auto;">' .
                                        htmlspecialchars($prettyJson) .
                                        '</pre>';
                                }
                            } catch (\Exception $e) {
                                // Jika gagal parsing, lanjutkan dengan perilaku default
                            }
                        } elseif ($type === 'BOOLEAN') {
                            if ($value == 1 || strtolower($value) === 'true') {
                                return '<span class="badge bg-success">True</span>';
                            } else {
                                return '<span class="badge bg-danger">False</span>';
                            }
                        }

                        // Perilaku default untuk tipe lainnya
                        if (strlen($value) > 100) {
                            $value = substr($value, 0, 97) . '...';
                        }
                        return htmlspecialchars($value);
                    })
                    ->rawColumns(['action', 'status', 'type', 'value'])
                    ->make(true);
            }

            // Data untuk tampilan non-AJAX
            $settings = SystemSetting::all();
            $totalSettingActive = SystemSetting::where('status', 'ACTIVE')->count();
            $totalSettingInactive = SystemSetting::where('status', 'INACTIVE')->count();

            return view('dashboard.system-settings.index', compact('settings', 'totalSettingActive', 'totalSettingInactive'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        return view('dashboard.system-settings.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'key' => 'required|min:3|unique:system_settings,key',
                'value' => 'required',
                'type' => 'required|in:STRING,INTEGER,BOOLEAN,JSON,URL',
                'description' => 'nullable',
                'status' => 'required|in:ACTIVE,INACTIVE,ARCHIVED,DELETED',
            ]);

            $setting = SystemSetting::create([
                'key' => $validated['key'],
                'value' => $validated['value'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'status' => $validated['status'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'System setting added successfully.',
                'setting' => $setting
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(SystemSetting $systemSetting)
    {
        return view('dashboard.system-settings.edit', compact('systemSetting'));
    }

    public function update(Request $request, SystemSetting $setting)
    {
        try {

            $validated = $request->validate([
                'key' => 'required|min:3|unique:system_settings,key,' . $setting->id,
                'value' => 'required',
                'type' => 'required|in:STRING,INTEGER,BOOLEAN,JSON,URL',
                'description' => 'nullable',
                'status' => 'required|in:ACTIVE,INACTIVE,ARCHIVED,DELETED',
            ]);

            $setting->update([
                'key' => $validated['key'],
                'value' => $validated['value'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'status' => $validated['status'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'System setting updated successfully.',
                'setting' => $setting
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemSetting $setting)
    {
        try {
            // Simpan data sebelum dihapus untuk logging
            $settingData = $setting->toArray();

            // Hapus record
            $deleted = $setting->delete();

            // Verifikasi penghapusan
            $checkExists = SystemSetting::find($settingData['id']);


            return response()->json([
                'success' => true,
                'message' => 'System setting deleted successfully.',
                'deleted' => $deleted, // Untuk debugging
                'still_exists' => $checkExists !== null // Untuk debugging
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $systemSetting = SystemSetting::with(['creator', 'updater'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'System setting details fetched successfully.',
                'setting' => $systemSetting
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}