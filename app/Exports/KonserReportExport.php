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
        $rows = [];
        
        // Add konser info
        $rows[] = ['Informasi Konser'];
        $rows[] = ['Judul', $this->report['konser_info']['title']];
        $rows[] = ['Tanggal', $this->report['konser_info']['tanggal']];
        $rows[] = ['Lokasi', $this->report['konser_info']['lokasi']];
        $rows[] = [];

        // Add ticket stats
        $rows[] = ['Statistik Tiket'];
        $rows[] = ['Total Tiket Terjual', $this->report['ticket_stats']['total_tickets']];
        $rows[] = ['Total Pendapatan', 'Rp ' . number_format($this->report['ticket_stats']['total_revenue'])];
        $rows[] = [];

        // Add ticket types detail
        $rows[] = ['Detail Per Tipe Tiket'];
        $rows[] = ['Tipe Tiket', 'Jumlah Terjual', 'Pendapatan'];
        foreach ($this->report['ticket_stats']['by_type'] as $type => $count) {
            $rows[] = [
                $type,
                $count,
                'Rp ' . number_format($this->report['ticket_stats']['revenue_by_type'][$type])
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return []; // Headings are included in array()
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            6 => ['font' => ['bold' => true, 'size' => 14]],
            10 => ['font' => ['bold' => true]],
            11 => ['font' => ['bold' => true]]
        ];
    }
}
