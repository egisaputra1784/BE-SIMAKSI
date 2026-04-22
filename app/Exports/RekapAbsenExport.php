<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RekapAbsenExport implements WithMultipleSheets
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new RekapAbsenDetailSheet($this->filters),
            new RekapAbsenSummarySheet($this->filters),
        ];
    }
}

// ─── Sheet 1: Detail Per Absensi ─────────────────────────────────────────────
class RekapAbsenDetailSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected array $filters;
    public function __construct(array $filters) { $this->filters = $filters; }

    public function collection()
    {
        $query = Absensi::with([
            'sesiAbsen.jadwal.kelas.tahunAjar',
            'sesiAbsen.jadwal.mapel',
            'sesiAbsen.jadwal.guru',
        ])->whereHas('sesiAbsen.jadwal');

        $this->applyFilters($query);
        return $query->orderByDesc('created_at')->get();
    }

    protected function applyFilters($query)
    {
        $f = $this->filters;
        if (!empty($f['tahun_ajar_id'])) {
            $query->whereHas('sesiAbsen.jadwal.kelas', fn($q) => $q->where('tahun_ajar_id', $f['tahun_ajar_id']));
        }
        if (!empty($f['kelas_id'])) {
            $query->whereHas('sesiAbsen.jadwal', fn($q) => $q->where('kelas_id', $f['kelas_id']));
        }
        if (!empty($f['mapel_id'])) {
            $query->whereHas('sesiAbsen.jadwal', fn($q) => $q->where('mapel_id', $f['mapel_id']));
        }
        if (!empty($f['dari'])) {
            $query->whereHas('sesiAbsen', fn($q) => $q->where('tanggal', '>=', $f['dari']));
        }
        if (!empty($f['sampai'])) {
            $query->whereHas('sesiAbsen', fn($q) => $q->where('tanggal', '<=', $f['sampai']));
        }
    }

    public function headings(): array
    {
        return ['No', 'Nama Murid', 'NISN', 'Kelas', 'Tahun Ajar', 'Mata Pelajaran', 'Guru', 'Tanggal', 'Status', 'Waktu Scan'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        $sesi   = $row->sesiAbsen;
        $jadwal = $sesi?->jadwal;
        $murid  = User::find($row->murid_id);
        return [
            $no,
            $murid?->name ?? '-',
            $murid?->nisn ?? '-',
            $jadwal?->kelas?->nama_kelas ?? '-',
            $jadwal?->kelas?->tahunAjar?->nama ?? '-',
            $jadwal?->mapel?->nama_mapel ?? '-',
            $jadwal?->guru?->name ?? '-',
            $sesi?->tanggal ?? '-',
            strtoupper($row->status),
            $row->waktu_scan ?? '-',
        ];
    }

    public function title(): string { return 'Detail Absensi'; }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1565C0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBDBDBD']]],
        ]);
        if ($lastRow > 1) {
            $sheet->getStyle('A2:' . $lastCol . $lastRow)->applyFromArray([
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBDBDBD']]],
            ]);
            for ($i = 2; $i <= $lastRow; $i++) {
                if ($i % 2 == 0) {
                    $sheet->getStyle('A' . $i . ':' . $lastCol . $i)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF0F7FF']],
                    ]);
                }
                // Color-code status column (J = col 10)
                $status = $sheet->getCell('J' . $i)->getValue();
                $colors = ['HADIR' => 'FF2E7D32', 'IZIN' => 'FF1565C0', 'SAKIT' => 'FFF57F17', 'ALPHA' => 'FFC62828', 'TERLAMBAT' => 'FFE65100'];
                if (isset($colors[$status])) {
                    $sheet->getStyle('J' . $i)->getFont()->getColor()->setARGB($colors[$status]);
                    $sheet->getStyle('J' . $i)->getFont()->setBold(true);
                }
            }
        }
        $sheet->getStyle('A1:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(22);
        return [];
    }
}

// ─── Sheet 2: Ringkasan Per Murid ─────────────────────────────────────────────
class RekapAbsenSummarySheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected array $filters;
    public function __construct(array $filters) { $this->filters = $filters; }

    public function collection()
    {
        $query = Absensi::with(['sesiAbsen.jadwal'])->whereHas('sesiAbsen.jadwal');
        $f = $this->filters;
        if (!empty($f['tahun_ajar_id'])) {
            $query->whereHas('sesiAbsen.jadwal.kelas', fn($q) => $q->where('tahun_ajar_id', $f['tahun_ajar_id']));
        }
        if (!empty($f['kelas_id'])) {
            $query->whereHas('sesiAbsen.jadwal', fn($q) => $q->where('kelas_id', $f['kelas_id']));
        }
        if (!empty($f['mapel_id'])) {
            $query->whereHas('sesiAbsen.jadwal', fn($q) => $q->where('mapel_id', $f['mapel_id']));
        }
        if (!empty($f['dari'])) {
            $query->whereHas('sesiAbsen', fn($q) => $q->where('tanggal', '>=', $f['dari']));
        }
        if (!empty($f['sampai'])) {
            $query->whereHas('sesiAbsen', fn($q) => $q->where('tanggal', '<=', $f['sampai']));
        }

        $all = $query->get()->groupBy('murid_id');
        return $all->map(function ($records, $muridId) {
            $murid  = User::find($muridId);
            $counts = $records->groupBy('status')->map->count();
            $total  = $records->count();
            return (object)[
                'nama_murid' => $murid?->name ?? '-',
                'nisn'       => $murid?->nisn ?? '-',
                'hadir'      => $counts['hadir']     ?? 0,
                'izin'       => $counts['izin']      ?? 0,
                'sakit'      => $counts['sakit']     ?? 0,
                'alpha'      => $counts['alpha']     ?? 0,
                'terlambat'  => $counts['terlambat'] ?? 0,
                'total'      => $total,
                'pct_hadir'  => $total > 0 ? round((($counts['hadir'] ?? 0) / $total) * 100) . '%' : '0%',
            ];
        })->values()->collect();
    }

    public function headings(): array
    {
        return ['No', 'Nama Murid', 'NISN', 'Hadir', 'Izin', 'Sakit', 'Alpha', 'Terlambat', 'Total', '% Kehadiran'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [$no, $row->nama_murid, $row->nisn, $row->hadir, $row->izin, $row->sakit, $row->alpha, $row->terlambat, $row->total, $row->pct_hadir];
    }

    public function title(): string { return 'Ringkasan Per Murid'; }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D47A1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBDBDBD']]],
        ]);
        if ($lastRow > 1) {
            $sheet->getStyle('A2:' . $lastCol . $lastRow)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBDBDBD']]],
            ]);
            $sheet->getStyle('B2:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            for ($i = 2; $i <= $lastRow; $i++) {
                if ($i % 2 == 0) {
                    $sheet->getStyle('A' . $i . ':' . $lastCol . $i)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF0F7FF']],
                    ]);
                }
            }
        }
        $sheet->getRowDimension(1)->setRowHeight(22);
        return [];
    }
}
