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
            $konser = Konser::with(['tiket'])->where('uuid', $uuid);
            
            // Filter for mitra
            if ($user->role_id === 3) {
                if ($user->konser_id != $konser->first()->id) {
                    return response()->json(['error' => 'Unauthorized access'], 403);
                }
            }
            
            $konser = $konser->first();
            
            if (!$konser) {
                return response()->json(['error' => 'Konser not found or unauthorized'], 404);
            }

            $pembelianQuery = DataPemesanan::whereHas('tiket', function($q) use ($konser) {
                $q->where('konsers_id', $konser->id);
            })->where('status_pembayaran', 'Successfully');

            // Calculate stats
            $report = [
                'konser_info' => [
                    'title' => $konser->title,
                    'tanggal' => $konser->tanggal,
                    'lokasi' => $konser->lokasi,
                ],
                'ticket_stats' => [
                    'total_tickets' => $pembelianQuery->count(),
                    'total_revenue' => $pembelianQuery->sum('total_harga'),
                    'by_type' => [
                        'Regular' => $pembelianQuery->where('gate_type', 'Regular')->count(),
                        'VIP' => $pembelianQuery->where('gate_type', 'VIP')->count(),
                        'VVIP' => $pembelianQuery->where('gate_type', 'VVIP')->count(),
                    ],
                    'revenue_by_type' => [
                        'Regular' => $pembelianQuery->where('gate_type', 'Regular')->sum('total_harga'),
                        'VIP' => $pembelianQuery->where('gate_type', 'VIP')->sum('total_harga'),
                        'VVIP' => $pembelianQuery->where('gate_type', 'VVIP')->sum('total_harga'),
                    ]
                ],
                'sales_by_date' => $this->getSalesByDate($pembelianQuery->get())
            ];

            Log::info('Report generated successfully', ['konser_id' => $konser->id]);
            return response()->json($report);

        } catch (\Exception $e) {
            Log::error('Error generating report', [
                'error' => $e->getMessage(),
                'uuid' => $uuid
            ]);
            return response()->json([
                'error' => 'Failed to generate report',
                'message' => $e->getMessage()
            ], 500);
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
                        'Regular' => $pembelian->where('gate_type', 'Regular')->count(),
                        'VIP' => $pembelian->where('gate_type', 'VIP')->count(),
                        'VVIP' => $pembelian->where('gate_type', 'VVIP')->count(),
                    ],
                    'revenue_by_type' => [
                        'Regular' => $pembelian->where('gate_type', 'Regular')->sum('total_harga'),
                        'VIP' => $pembelian->where('gate_type', 'VIP')->sum('total_harga'),
                        'VVIP' => $pembelian->where('gate_type', 'VVIP')->sum('total_harga'),
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
                    return Excel::download(
                        new KonserReportExport($report), 
                        'konser-report-' . $uuid . '.xlsx'
                    );
                
                case 'pdf':
                    return PDF::loadView('reports.konser', compact('report'))
                        ->download('konser-report-' . $uuid . '.pdf');
                
                default:
                    return response()->json(['error' => 'Invalid report type'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Error downloading report', [
                'error' => $e->getMessage(),
                'uuid' => $uuid,
                'type' => $type
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
                return Carbon::parse($item->tanggal_pemesan)->format('Y-m-d');
            })
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'revenue' => $group->sum('total_harga')
                ];
            });
    }
}
