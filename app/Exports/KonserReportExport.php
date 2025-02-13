<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KonserReportExport implements FromArray, WithHeadings, WithStyles
{
    protected $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function array(): array
    {
        $data = [];
        
        // Konser Info
        $data[] = ['Informasi Konser'];
        $data[] = ['Judul', $this->report['konser_info']['title']];
        $data[] = ['Tanggal', $this->report['konser_info']['tanggal']];
        $data[] = ['Lokasi', $this->report['konser_info']['lokasi']];
        $data[] = [];

        // Ticket Stats
        $data[] = ['Statistik Tiket'];
        $data[] = ['Total Tiket Terjual', $this->report['ticket_stats']['total_tickets']];
        $data[] = ['Total Pendapatan', 'Rp ' . number_format($this->report['ticket_stats']['total_revenue'], 0, ',', '.')];
        $data[] = [];

        // By Type
        $data[] = ['Penjualan per Kategori'];
        $data[] = ['Kategori', 'Jumlah', 'Pendapatan'];
        foreach ($this->report['ticket_stats']['by_type'] as $type => $count) {
            $data[] = [
                $type,
                $count,
                'Rp ' . number_format($this->report['ticket_stats']['revenue_by_type'][$type], 0, ',', '.')
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            ['Laporan Konser'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            6 => ['font' => ['bold' => true]],
            10 => ['font' => ['bold' => true]],
        ];
    }
}
