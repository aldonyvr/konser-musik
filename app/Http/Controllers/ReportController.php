<?php

namespace App\Http\Controllers;

use App\Models\Konser;
use App\Models\DataPemesanan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\PDF;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Exports\KonserReportExport;

class ReportController extends Controller
{
    public function getReport($uuid)
    {
        try {
            $user = auth()->user();
            $konser = Konser::with(['tiket'])->where('uuid', $uuid)->first();
            
            if (!$konser) {
                return response()->json(['error' => 'Konser not found'], 404);
            }

            // Get all purchases for this konser
            $pembelian = DataPemesanan::whereHas('tiket', function($q) use ($konser) {
                $q->where('konsers_id', $konser->id);
            })
            ->where('status_pembayaran', 'Successfully')
            ->get();

            // Debug raw data
            \Log::info('Raw pembelian data:', $pembelian->toArray());

            // Check the actual gate_type values in database
            $types = $pembelian->pluck('gate_type')->toArray();
            \Log::info('Gate types in database:', $types);

            // Count using direct database values
            $regularCount = $pembelian->whereIn('gate_type', ['reguler', 'regular'])->count();
            $vipCount = $pembelian->where('gate_type', 'vip')->count();

            \Log::info('Direct count:', [
                'regular' => $regularCount,
                'vip' => $vipCount
            ]);

            // Calculate revenues
            $regularRevenue = $pembelian->whereIn('gate_type', ['reguler', 'regular'])
                ->sum('total_harga');
            $vipRevenue = $pembelian->where('gate_type', 'vip')
                ->sum('total_harga');

            \Log::info('Direct revenue:', [
                'regular' => $regularRevenue,
                'vip' => $vipRevenue
            ]);

            $report = [
                'konser_info' => [
                    'title' => $konser->title,
                    'tanggal' => $konser->tanggal,
                    'lokasi' => $konser->lokasi,
                    'uuid' => $konser->uuid
                ],
                'ticket_stats' => [
                    'total_tickets' => $pembelian->count(),
                    'total_revenue' => $pembelian->sum('total_harga'),
                    'by_type' => [
                        'Regular' => $regularCount,
                        'VIP' => $vipCount
                    ],
                    'revenue_by_type' => [
                        'Regular' => $regularRevenue,
                        'VIP' => $vipRevenue
                    ]
                ],
                'sales_by_date' => $this->getSalesByDate($pembelian)
            ];

            return response()->json($report);

        } catch (\Exception $e) {
            \Log::error('Error in getReport:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getReportData($uuid)
    {
        try {
            // Find konser with relationships
            $konser = Konser::with(['tiket'])->where('uuid', $uuid)->first();
            
            if (!$konser) {
                return null;
            }

            // Get pembelian data
            $pembelian = DataPemesanan::with(['tiket'])
                ->whereHas('tiket', function($q) use ($konser) {
                    $q->where('konsers_id', $konser->id);
                })
                ->where('status_pembayaran', 'Successfully')
                ->get();

            return [
                'konser_info' => [
                    'title' => $konser->title ?? 'N/A',
                    'tanggal' => $konser->tanggal ?? 'N/A',
                    'lokasi' => $konser->lokasi ?? 'N/A',
                ],
                'ticket_stats' => [
                    'total_tickets' => $pembelian->count(),
                    'total_revenue' => $pembelian->sum('total_harga'),
                    'by_type' => [
                        'Regular' => $pembelian->whereIn('gate_type', ['Regular', 'regular', 'REGULAR'])->count(),
                        'VIP' => $pembelian->whereIn('gate_type', ['VIP', 'vip', 'Vip'])->count(),
                    ],
                    'revenue_by_type' => [
                        'Regular' => $pembelian->whereIn('gate_type', ['Regular', 'regular', 'REGULAR'])->sum('total_harga'),
                        'VIP' => $pembelian->whereIn('gate_type', ['VIP', 'vip', 'Vip'])->sum('total_harga'),
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting report data', [
                'error' => $e->getMessage(),
                'uuid' => $uuid
            ]);
            return null;
        }
    }

    public function downloadReport(Request $request, $uuid)
    {
        try {
            $type = $request->query('type', 'excel');
            $report = $this->getReportData($uuid);
            
            if (!$report) {
                return response()->json(['error' => 'Report data not found'], 404);
            }

            switch ($type) {
                case 'excel':
                    $filename = 'laporan-konser-' . date('Y-m-d-His') . '.xlsx';
                    $filepath = storage_path('app/public/reports/' . $filename);
                    
                    // Create directory if it doesn't exist
                    if (!file_exists(storage_path('app/public/reports'))) {
                        mkdir(storage_path('app/public/reports'), 0755, true);
                    }
                    
                    // Generate Excel file
                    Excel::store(new KonserReportExport($report), 'reports/' . $filename, 'public');
                    
                    // Return file URL
                    return response()->json([
                        'success' => true,
                        'file_url' => url('storage/reports/' . $filename),
                        'filename' => $filename
                    ]);
                
                case 'pdf':
                    $pdf = PDF::loadView('reports.konser', compact('report'));
                    $filename = 'laporan-konser-' . date('Y-m-d-His') . '.pdf';
                    
                    // Save PDF to storage
                    $pdf->save(storage_path('app/public/reports/' . $filename));
                    
                    return response()->json([
                        'success' => true,
                        'file_url' => url('storage/reports/' . $filename),
                        'filename' => $filename
                    ]);
                
                default:
                    return response()->json(['error' => 'Invalid report type'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Error downloading report', [
                'error' => $e->getMessage(),
                'uuid' => $uuid,
                'type' => $type,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to download report',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getSalesByDate($pembelian)
    {
        return $pembelian
            ->groupBy(function($item) {
                return Carbon::parse($item->created_at)->format('Y-m-d');
            })
            ->map(function($groupedItems) {
                // Count directly from the grouped items
                $regularCount = $groupedItems->whereIn('gate_type', ['reguler', 'regular'])->count();
                $vipCount = $groupedItems->where('gate_type', 'vip')->count();
                
                // Calculate revenue directly
                $regularRevenue = $groupedItems->whereIn('gate_type', ['reguler', 'regular'])->sum('total_harga');
                $vipRevenue = $groupedItems->where('gate_type', 'vip')->sum('total_harga');

                \Log::info('Group data:', [
                    'date' => $groupedItems->first()->created_at,
                    'items' => $groupedItems->pluck('gate_type')->toArray(),
                    'regular_count' => $regularCount,
                    'vip_count' => $vipCount,
                    'regular_revenue' => $regularRevenue,
                    'vip_revenue' => $vipRevenue
                ]);

                return [
                    'count' => $groupedItems->count(),
                    'revenue' => $groupedItems->sum('total_harga'),
                    'by_type' => [
                        'Regular' => $regularCount,
                        'VIP' => $vipCount
                    ],
                    'revenue_by_type' => [
                        'Regular' => $regularRevenue,
                        'VIP' => $vipRevenue
                    ]
                ];
            });
    }
}
