<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;

class KonserReportExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function array(): array
    {
        $data = [];

        // Add concert info
        $data[] = ['Nama Konser', $this->report['konser_info']['title']];
        $data[] = ['Tanggal', $this->report['konser_info']['tanggal']];
        $data[] = ['Lokasi', $this->report['konser_info']['lokasi']];
        $data[] = [''];  // Empty row for spacing

        // Add summary stats
        $data[] = ['Ringkasan Penjualan'];
        $data[] = ['Total Tiket Terjual', $this->report['ticket_stats']['total_tickets']];
        $data[] = ['Total Pendapatan', 'Rp ' . number_format($this->report['ticket_stats']['total_revenue'])];
        $data[] = [''];  // Empty row for spacing

        // Add ticket type breakdown
        $data[] = ['Detail Penjualan per Tipe Tiket'];
        $data[] = [
            'Tipe Tiket',
            'Jumlah Terjual',
            'Total Pendapatan',
            'Persentase'
        ];

        foreach ($this->report['ticket_stats']['by_type'] as $type => $count) {
            $revenue = $this->report['ticket_stats']['revenue_by_type'][$type];
            $percentage = ($count / $this->report['ticket_stats']['total_tickets']) * 100;
            
            $data[] = [
                $type,
                $count,
                'Rp ' . number_format($revenue),
                number_format($percentage, 1) . '%'
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return []; // No headings needed as we're creating a custom layout
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Style for title cells
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        
        // Style for summary section
        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->getStyle('A6:A7')->getFont()->setBold(true);
        
        // Style for ticket type breakdown
        $sheet->getStyle('A9')->getFont()->setBold(true);
        $sheet->getStyle('A10:D10')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF']
            ]
        ]);

        // Add borders to the ticket type breakdown table
        $tableLastRow = $lastRow;
        $sheet->getStyle('A10:D' . $tableLastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 25,
            'D' => 15,
        ];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }
}
