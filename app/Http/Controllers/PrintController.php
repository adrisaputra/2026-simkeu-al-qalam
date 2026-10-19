<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Classes;
use App\Models\Employee;
use App\Models\EmployeeReport;
use App\Models\Transaction;
use App\Models\TransactionBank;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PrintController extends Controller
{
    // # Show Data
    public function index()
    {
        $title = 'Laporan';
        $work_unit = WorkUnit::whereIn('id', [3, 4, 5, 6])->get();

        return view('admin.print.index', compact('title', 'work_unit'));
    }

    public function print(Request $request)
    {
        if ($request->category_report == 1) {
            return $this->print_checklist_report($request);
        } elseif ($request->category_report == 2) {
            if($request->recap_type == 1){
                return $this->print_recap_daily($request);
            } else if($request->recap_type == 2){
                return $this->print_recap_monthly($request);
            }
        } 
    }

    public function print_checklist_report(Request $request)
    {
        $spreadsheet = new Spreadsheet;

        $period = $request->period;
        [$first_year, $second_year] = explode(' - ', $period);
        
        $work_unit = WorkUnit::where('id', $request->work_unit_id)->first();
        $classes = Classes::with(['students' => function ($query) {
            $query->orderBy('name', 'ASC');
        }])
            ->where('work_unit_id', $request->work_unit_id)
            ->when($request->classes_id, function ($query, $classesId) {
                $query->where('id', $classesId);
            })
            ->orderBy('name', 'ASC')
            ->get();

        $sheetTitles = [];

        foreach ($classes as $index => $class) {
            $sheet = $index === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheetTitle = str_replace(['\\', '/', '?', '*', '[', ']', ':'], '-', trim($class->name));
            $sheetTitle = trim(mb_substr($sheetTitle, 0, 31), " '");

            if ($sheetTitle === '') {
                $sheetTitle = 'Kelas '.$class->id;
            }

            $baseTitle = $sheetTitle;
            $suffix = 2;

            while (isset($sheetTitles[mb_strtolower($sheetTitle)])) {
                $ending = ' ('.$suffix++.')';
                $sheetTitle = mb_substr($baseTitle, 0, 31 - mb_strlen($ending)).$ending;
            }

            $sheetTitles[mb_strtolower($sheetTitle)] = true;
            $sheet->setTitle($sheetTitle);

            // Atur lebar kolom
            $sheet->getColumnDimension('A')->setWidth(5);   // No
            $sheet->getColumnDimension('B')->setWidth(40);  // Nama
            $sheet->getColumnDimension('C')->setWidth(15);  // Kelas

            $sheet->mergeCells('A1:AL1');
            $sheet->mergeCells('A2:AL2');
            $sheet->mergeCells('A3:AL3');
            $sheet->setCellValue('A1', 'DATA CEKLIS YAYASAN PENDIDIKAN ALQALAM KENDARI');
            $sheet->setCellValue('A2', strtoupper($work_unit->name));
            $sheet->setCellValue('A3', 'BULAN JULI - JUNI TAHUN '.$first_year.' - '.$second_year);
            $sheet->getStyle('A1:AL3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:AL3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1:AL3')->getFont()->setBold(true);
            $sheet->getStyle('A1:AL3')->getAlignment()->setWrapText(true);

            // Header tabel
            $sheet->setCellValue('A5', 'NO');$sheet->mergeCells('A5:A7');
            $sheet->setCellValue('B5', 'NAMA SISWA');$sheet->mergeCells('B5:B7');
            $sheet->setCellValue('C5', 'KELAS');$sheet->mergeCells('C5:C7');

            $sheet->setCellValue('D5', 'SPP (Juli s.d Desember '.$first_year.')');$sheet->mergeCells('D5:I5');
            $sheet->setCellValue('J5', 'CATERING (Juli s.d Desember '.$first_year.')');$sheet->mergeCells('J5:O5');
            $sheet->setCellValue('P5', 'SPP (Januari s.d Juni '.$second_year.')');$sheet->mergeCells('P5:U5');
            $sheet->setCellValue('V5', 'CATERING (Januari s.d Juni '.$second_year.')');$sheet->mergeCells('V5:AA5');

            $sheet->setCellValue('D6', 'SYARAT PTS');$sheet->mergeCells('D6:F6');
            $sheet->setCellValue('G6', 'SYARAT PAS');$sheet->mergeCells('G6:I6');
            $sheet->setCellValue('J6', 'SYARAT PTS');$sheet->mergeCells('J6:L6');
            $sheet->setCellValue('M6', 'SYARAT PAS');$sheet->mergeCells('M6:O6');
            $sheet->setCellValue('P6', 'SYARAT PTS');$sheet->mergeCells('P6:R6');
            $sheet->setCellValue('S6', 'SYARAT PAS');$sheet->mergeCells('S6:U6');
            $sheet->setCellValue('V6', 'SYARAT PTS');$sheet->mergeCells('V6:X6');
            $sheet->setCellValue('Y6', 'SYARAT PAS');$sheet->mergeCells('Y6:AA6');

            $sheet->setCellValue('D7', '7');
            $sheet->setCellValue('E7', '8');
            $sheet->setCellValue('F7', '9');
            $sheet->setCellValue('G7', '10');
            $sheet->setCellValue('H7', '11');
            $sheet->setCellValue('I7', '12');
            $sheet->setCellValue('J7', '7');
            $sheet->setCellValue('K7', '8');
            $sheet->setCellValue('L7', '9');
            $sheet->setCellValue('M7', '10');
            $sheet->setCellValue('N7', '11');
            $sheet->setCellValue('O7', '12');
            $sheet->setCellValue('P7', '1');
            $sheet->setCellValue('Q7', '2');
            $sheet->setCellValue('R7', '3');
            $sheet->setCellValue('S7', '4');
            $sheet->setCellValue('T7', '5');
            $sheet->setCellValue('U7', '6');
            $sheet->setCellValue('V7', '1');
            $sheet->setCellValue('W7', '2');
            $sheet->setCellValue('X7', '3');
            $sheet->setCellValue('Y7', '4');
            $sheet->setCellValue('Z7', '5');
            $sheet->setCellValue('AA7', '6');

            $sheet->getStyle('A5:AL5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A5:AL5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A5:AL5')->getFont()->setBold(true);
            $sheet->getStyle('A5:AL5')->getAlignment()->setWrapText(true);

            $rows = 8;
            $no = 1;

            foreach ($class->students as $student) {
                $sheet->setCellValue('A'.$rows, $no++);
                $sheet->setCellValue('B'.$rows, $student->name);
                $sheet->setCellValue('C'.$rows, $class->name);

                ## SPP (Juli s.d Desember $first_year)
                $columns = ['D' => 7, 'E' => 8, 'F' => 9];

                foreach ($columns as $column => $month) {
                    $amount = TransactionBank::where('student_id', $student->id)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $first_year)
                        ->where('desc_category_id', 3)
                        ->sum('amount');

                    if($amount > 0) {
                        $sheet->setCellValue($column . $rows, $request->ceklist_type == 'nominal' ? $amount : '✓');
                        if ($request->ceklist_type == 'nominal') {
                            $sheet->getStyle($column . $rows)->getNumberFormat()->setFormatCode('#,##0');
                        }
                    }
                }

                $columns = ['G' => 10, 'H' => 11, 'I' => 12];

                foreach ($columns as $column => $month) {
                    $amount = TransactionBank::where('student_id', $student->id)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $first_year)
                        ->where('desc_category_id', 3)
                        ->sum('amount');
                    
                    if($amount > 0) {
                        $sheet->setCellValue($column . $rows, $request->ceklist_type == 'nominal' ? $amount : '✓');
                        if ($request->ceklist_type == 'nominal') {
                            $sheet->getStyle($column . $rows)->getNumberFormat()->setFormatCode('#,##0');
                        }
                    }
                }
                
                ## CATERING (Juli s.d Desember $first_year)
                $columns = ['J' => 7, 'K' => 8, 'L' => 9];

                foreach ($columns as $column => $month) {
                    $amount = TransactionBank::where('student_id', $student->id)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $first_year)
                        ->where('desc_category_id', 2)
                        ->sum('amount');

                    if($amount > 0) {
                        $sheet->setCellValue($column . $rows, $request->ceklist_type == 'nominal' ? $amount : '✓');
                        if ($request->ceklist_type == 'nominal') {
                            $sheet->getStyle($column . $rows)->getNumberFormat()->setFormatCode('#,##0');
                        }
                    }
                }

                $columns = ['M' => 10, 'N' => 11, 'O' => 12];

                foreach ($columns as $column => $month) {
                    $amount = TransactionBank::where('student_id', $student->id)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $first_year)
                        ->where('desc_category_id', 2)
                        ->sum('amount');

                    if($amount > 0) {
                        $sheet->setCellValue($column . $rows, $request->ceklist_type == 'nominal' ? $amount : '✓');
                        if ($request->ceklist_type == 'nominal') {
                            $sheet->getStyle($column . $rows)->getNumberFormat()->setFormatCode('#,##0');
                        }
                    }
                }
                
                ## SPP (Januari s.d Juni $second_year)
                $columns = ['P' => 1, 'Q' => 2, 'R' => 3];

                foreach ($columns as $column => $month) {
                    $amount = TransactionBank::where('student_id', $student->id)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $second_year)
                        ->where('desc_category_id', 3)
                        ->sum('amount');

                    if($amount > 0) {
                        $sheet->setCellValue($column . $rows, $request->ceklist_type == 'nominal' ? $amount : '✓');
                        if ($request->ceklist_type == 'nominal') {
                            $sheet->getStyle($column . $rows)->getNumberFormat()->setFormatCode('#,##0');
                        }
                    }
                }

                $columns = ['S' => 4, 'T' => 5, 'U' => 6];

                foreach ($columns as $column => $month) {
                    $amount = TransactionBank::where('student_id', $student->id)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $second_year)
                        ->where('desc_category_id', 3)
                        ->sum('amount');

                    if($amount > 0) {
                        $sheet->setCellValue($column . $rows, $request->ceklist_type == 'nominal' ? $amount : '✓');
                        if ($request->ceklist_type == 'nominal') {
                            $sheet->getStyle($column . $rows)->getNumberFormat()->setFormatCode('#,##0');
                        }
                    }
                }
                
                ## CATERING (Januari s.d Juni $second_year)
                $columns = ['V' => 1, 'W' => 2, 'X' => 3];

                foreach ($columns as $column => $month) {
                    $amount = TransactionBank::where('student_id', $student->id)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $second_year)
                        ->where('desc_category_id', 2)
                        ->sum('amount');

                    if($amount > 0) {
                        $sheet->setCellValue($column . $rows, $request->ceklist_type == 'nominal' ? $amount : '✓');
                        if ($request->ceklist_type == 'nominal') {
                            $sheet->getStyle($column . $rows)->getNumberFormat()->setFormatCode('#,##0');
                        }
                    }
                }

                $columns = ['Y' => 4, 'Z' => 5, 'AA' => 6];

                foreach ($columns as $column => $month) {
                    $amount = TransactionBank::where('student_id', $student->id)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $second_year)
                        ->where('desc_category_id', 2)
                        ->sum('amount');

                    if($amount > 0) {
                        $sheet->setCellValue($column . $rows, $request->ceklist_type == 'nominal' ? $amount : '✓');
                        if ($request->ceklist_type == 'nominal') {
                            $sheet->getStyle($column . $rows)->getNumberFormat()->setFormatCode('#,##0');
                        }
                    }
                }
                
                $rows++;
            }

            // Border dan alignment
            $sheet->getStyle('A5:AA'.($rows - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A5:A'.($rows - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C5:AA'.($rows - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $type = 'xlsx';
        $fileName = 'DATA CEKLIS YAYASAN PENDIDIKAN ALQALAM KENDARI ('.strtoupper($work_unit->name).') BULAN JULI - JUNI TAHUN '.$request->year.'.'.$type;

        if ($type == 'xlsx') {
            $writer = new Xlsx($spreadsheet);
        } elseif ($type == 'xls') {
            $writer = new Xls($spreadsheet);
        }
        $writer->save('public/upload/report/'.$fileName);
        header('Content-Type: application/vnd.ms-excel');

        return redirect(url('/').'/public/upload/report/'.$fileName);
    }

    public function print_recap_daily(Request $request)
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(8);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(70);
        $sheet->getColumnDimension('F')->setWidth(17);
        $sheet->getColumnDimension('G')->setWidth(17);
        $sheet->getColumnDimension('H')->setWidth(17);
        $sheet->getColumnDimension('I')->setWidth(17);

        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A1', 'LAPORAN KEUANGAN YAYASAN PENDIDIKAN ALQALAM KENDARI');
        $sheet->setCellValue('A2', 'BULAN '.$request->month.' TAHUN '.$request->year);
        $sheet->getStyle('A1:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:I2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:I2')->getFont()->setBold(true);
        $sheet->getStyle('A1:I2')->getAlignment()->setWrapText(true);

        // Header tabel
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Kode');
        $sheet->setCellValue('C5', 'Hari/Tanggal');
        $sheet->setCellValue('D5', 'Nama');
        $sheet->setCellValue('E5', 'Uraian');
        $sheet->setCellValue('F5', 'Debet');
        $sheet->setCellValue('G5', 'Kredit');
        $sheet->setCellValue('H5', 'Saldo');
        $sheet->setCellValue('I5', 'Keterangan');

        $sheet->getStyle('A5:I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:I5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A5:I5')->getFont()->setBold(true);
        $sheet->getStyle('A5:I5')->getAlignment()->setWrapText(true);

        $rows = 7;
        $no = 2;
        $periodStart = sprintf('%04d-%02d-01', $request->year, $request->month);
        $openingDebit = Transaction::where('date', '<', $periodStart)
            ->where('type', 'income')
            ->sum('amount');
        $openingCredit = Transaction::where('date', '<', $periodStart)
            ->where('type', 'expense')
            ->sum('amount');
        $openingBalance = $openingDebit - $openingCredit;

        $sheet->setCellValue('A6', 1);
        $sheet->setCellValue('C6', Helpers::date_indo_full($periodStart.' -1 day'));
        $sheet->setCellValue('E6', 'Saldo bulan sebelumnya');
        $sheet->setCellValue('H6', $openingBalance);
        $sheet->getStyle('F6:H6')->getNumberFormat()->setFormatCode('#,##0');

        $transaction = Transaction::with(['transaction_code', 'desc_category', 'user'])
            ->whereMonth('date', $request->month)
            ->whereYear('date', $request->year)
            ->orderBy('date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        foreach ($transaction as $v) {
            $debit = $v->type === 'income' ? (float) $v->amount : null;
            $credit = $v->type === 'expense' ? (float) $v->amount : null;

            $sheet->setCellValue('A'.$rows, $no++);
            $sheet->setCellValue('B'.$rows, $v->transaction_code?->code ?? '');
            $sheet->setCellValue('C'.$rows, Helpers::date_indo_full($v->date));
            $sheet->setCellValue('D'.$rows, $v->user?->name ?? '');
            $sheet->setCellValue('E'.$rows, $v->desc ?? '');
            $sheet->setCellValue('F'.$rows, $debit);
            $sheet->setCellValue('G'.$rows, $credit);
            $balanceFormula = '=H'.($rows - 1).'+F'.$rows.'-G'.$rows;
            $sheet->setCellValue('H'.$rows, $balanceFormula);
            $sheet->setCellValue('I'.$rows, '');
            $sheet->getStyle('F'.$rows.':H'.$rows)->getNumberFormat()->setFormatCode('#,##0');
            $rows++;
        }

        $totalRow = $rows;
        // $sumStartRow = $rows > 6 ? 6 : 1;
        // $sumEndRow = $rows > 6 ? $rows - 1 : 5;
        // $sheet->setCellValue('E' . $totalRow, 'TOTAL');
        // $sheet->setCellValue('F' . $totalRow, '=SUM(F' . $sumStartRow . ':F' . $sumEndRow . ')');
        // $sheet->setCellValue('G' . $totalRow, '=SUM(G' . $sumStartRow . ':G' . $sumEndRow . ')');
        // $sheet->setCellValue('H' . $totalRow, '=SUM(H' . $sumStartRow . ':H' . $sumEndRow . ')');
        // $sheet->getStyle('F' . $totalRow . ':H' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle('A5:I'.($totalRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A5:I'.($totalRow - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A5:I'.($totalRow - 1))->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:A'.($totalRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B5:B'.($totalRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F5:H'.($totalRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        // $sheet->getStyle('E' . $totalRow . ':H' . $totalRow)->getFont()->setBold(true);

        $type = 'xlsx';
        $fileName = 'REKAP KEUANGAN YAYASAN PENDIDIKAN ALQALAM KENDARI BULAN '.$request->month.' TAHUN '.$request->year.'.'.$type;

        if ($type == 'xlsx') {
            $writer = new Xlsx($spreadsheet);
        } elseif ($type == 'xls') {
            $writer = new Xls($spreadsheet);
        }
        $writer->save('public/upload/report/'.$fileName);
        header('Content-Type: application/vnd.ms-excel');

        return redirect(url('/').'/public/upload/report/'.$fileName);
    }

    public function print_recap_monthly(Request $request)
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(60);
        $sheet->getColumnDimension('C')->setWidth(15);

        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('A1', 'LAPORAN KEUANGAN YAYASAN PENDIDIKAN ALQALAM KENDARI');
        $sheet->setCellValue('A2', 'BULAN '.$request->month.' TAHUN '.$request->year);
        $sheet->getStyle('A1:C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:C2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:C2')->getFont()->setBold(true);
        $sheet->getStyle('A1:C2')->getAlignment()->setWrapText(true);

        // Header tabel
        $sheet->setCellValue('A5', 'SALDO');$sheet->mergeCells('A5:B5');
        $sheet->setCellValue('C5', '17977204');
        $sheet->getStyle('A5:B5')->getFont()->setBold(true);

        $sheet->setCellValue('A6', 'PEMASUKAN');$sheet->mergeCells('A6:C6');
        $sheet->getStyle('A6:C6')->getFont()->setBold(true);
        
        $sheet->setCellValue('A7', 'No.');
        $sheet->setCellValue('B7', 'Uraian');
        $sheet->setCellValue('C7', '');
        $sheet->getStyle('A7:C7')->getFont()->setBold(true);

        $sheet->setCellValue('A8', '1');
        $sheet->setCellValue('B8', 'Penarikan Assalam');
        $sheet->setCellValue('C8', '');
        
        $sheet->setCellValue('A9', '2');
        $sheet->setCellValue('B9', 'Penarikan Bank Muamalat');
        $sheet->setCellValue('C9', '');

        $sheet->setCellValue('A10', '3');
        $sheet->setCellValue('B10', 'Pengembalian dana kegiatan');
        $sheet->setCellValue('C10', '');

        $sheet->setCellValue('A11', '4');
        $sheet->setCellValue('B11', 'Pengembalian pinjaman pegawai / unit');
        $sheet->setCellValue('C11', '');

        $sheet->setCellValue('A12', '5');
        $sheet->setCellValue('B12', 'Raport TK');
        $sheet->setCellValue('C12', '');

        $sheet->setCellValue('A13', '6');
        $sheet->setCellValue('B13', 'Pendapatan Lainnya');
        $sheet->setCellValue('C13', '');

        $sheet->setCellValue('A14', '7');
        $sheet->setCellValue('B14', 'Administrasi penelitian');
        $sheet->setCellValue('C14', '');

        $sheet->setCellValue('A15', 'TOTAL 1-7');
        $sheet->setCellValue('B15', '-');
        $sheet->setCellValue('C15', '');
        $sheet->getStyle('A15:C15')->getFont()->setBold(true);

        // Pengeluaran
        $sheet->setCellValue('A16', 'PENGELUARAN');
        $sheet->getStyle('A16:C16')->getFont()->setBold(true);

        $sheet->setCellValue('A17', 'No.');
        $sheet->setCellValue('B17', 'Uraian');
        $sheet->setCellValue('C17', '');
        $sheet->getStyle('A17:C17')->getFont()->setBold(true);

        $sheet->setCellValue('A18', '1');
        $sheet->setCellValue('B18', 'Catering YAS');
        $sheet->setCellValue('C18', '');

        $sheet->setCellValue('A19', '2');
        $sheet->setCellValue('B19', 'Snack siswa dan guru YAS');
        $sheet->setCellValue('C19', '');

        $sheet->setCellValue('A20', '3');
        $sheet->setCellValue('B20', 'Belanja kantor & ATK');
        $sheet->setCellValue('C20', '');

        $sheet->setCellValue('A21', '4');
        $sheet->setCellValue('B21', 'Gaji OB SDIT, SMPIT');
        $sheet->setCellValue('C21', '');

        $sheet->setCellValue('A22', '5');
        $sheet->setCellValue('B22', 'BPJS');
        $sheet->setCellValue('C22', '');

        $sheet->setCellValue('A23', '6');
        $sheet->setCellValue('B23', 'Penggajian TKIT, MBC, dan SMPIT');
        $sheet->setCellValue('C23', '');

        $sheet->setCellValue('A24', '7');
        $sheet->setCellValue('B24', 'Pembimbingan olimpiade / beasiswa');
        $sheet->setCellValue('C24', '');

        $sheet->setCellValue('A25', '8');
        $sheet->setCellValue('B25', 'Wifi');
        $sheet->setCellValue('C25', '');

        $sheet->setCellValue('A26', '9');
        $sheet->setCellValue('B26', 'Gaji Guru SDIT');
        $sheet->setCellValue('C26', '');

        $sheet->setCellValue('A27', '10');
        $sheet->setCellValue('B27', 'Pembayaran koran');
        $sheet->setCellValue('C27', '');

        $sheet->setCellValue('A28', '11');
        $sheet->setCellValue('B28', 'THR');
        $sheet->setCellValue('C28', '');

        $sheet->setCellValue('A29', '12');
        $sheet->setCellValue('B29', 'RAB Unit');
        $sheet->setCellValue('C29', '');

        $sheet->setCellValue('A30', '13');
        $sheet->setCellValue('B30', 'Administrasi transfer / sekretariatan');
        $sheet->setCellValue('C30', '');

        $sheet->setCellValue('A31', '14');
        $sheet->setCellValue('B31', 'Penggajian YAS, USQ, SMAIT');
        $sheet->setCellValue('C31', '');

        $sheet->setCellValue('A32', '15');
        $sheet->setCellValue('B32', 'Kampung Inggris');
        $sheet->setCellValue('C32', '');

        $sheet->setCellValue('A33', '16');
        $sheet->setCellValue('B33', 'Mesjid Al Qalam');
        $sheet->setCellValue('C33', '');

        $sheet->setCellValue('A34', '17');
        $sheet->setCellValue('B34', 'Setoran ke rekening BMT');
        $sheet->setCellValue('C34', '');

        $sheet->setCellValue('A35', '18');
        $sheet->setCellValue('B35', 'Pinjaman pegawai / unit');
        $sheet->setCellValue('C35', '');

        $sheet->setCellValue('A36', '19');
        $sheet->setCellValue('B36', 'Listrik dan kelistrikan Al Qalam');
        $sheet->setCellValue('C36', '');

        $sheet->setCellValue('A37', '20');
        $sheet->setCellValue('B37', 'Pengembalian dana PPDB / SPP / Catering ke ortu siswa');
        $sheet->setCellValue('C37', '');

        $sheet->setCellValue('A38', '21');
        $sheet->setCellValue('B38', 'Operasional / grab / transport');
        $sheet->setCellValue('C38', '');

        $sheet->setCellValue('A39', '22');
        $sheet->setCellValue('B39', 'Iuran JSIT');
        $sheet->setCellValue('C39', '');

        $sheet->setCellValue('A40', '23');
        $sheet->setCellValue('B40', 'Snack Rapat / Tamu');
        $sheet->setCellValue('C40', '');

        $sheet->setCellValue('A41', '24');
        $sheet->setCellValue('B41', 'Kursi SMP dan SD');
        $sheet->setCellValue('C41', '');

        $sheet->setCellValue('A42', '25');
        $sheet->setCellValue('B42', 'Galon');
        $sheet->setCellValue('C42', '');

        $sheet->setCellValue('A43', '26');
        $sheet->setCellValue('B43', 'Program Al Qalam Peduli');
        $sheet->setCellValue('C43', '');

        $sheet->setCellValue('A44', '27');
        $sheet->setCellValue('B44', 'Seragam Ummi Erni');
        $sheet->setCellValue('C44', '');

        $sheet->setCellValue('A45', '28');
        $sheet->setCellValue('B45', 'Seragam guru SDIT');
        $sheet->setCellValue('C45', '');

        $sheet->setCellValue('A46', '29');
        $sheet->setCellValue('B46', 'Pekerjaan Lugis RKB SDIT');
        $sheet->setCellValue('C46', '');

        $sheet->setCellValue('A47', '30');
        $sheet->setCellValue('B47', 'Infaq sosial / bantuan pernikahan / Beasiswa');
        $sheet->setCellValue('C47', '');

        $sheet->setCellValue('A48', '31');
        $sheet->setCellValue('B48', 'Kafalah marbot');
        $sheet->setCellValue('C48', '');

        $sheet->setCellValue('A49', '32');
        $sheet->setCellValue('B49', 'Honor pegawai dapur');
        $sheet->setCellValue('C49', '');

        $sheet->setCellValue('A50', '33');
        $sheet->setCellValue('B50', 'Pelatihan guru / studi banding / outbond');
        $sheet->setCellValue('C50', '');

        $sheet->setCellValue('A51', '34');
        $sheet->setCellValue('B51', 'Dana pemateri');
        $sheet->setCellValue('C51', '');

        $sheet->setCellValue('A52', '35');
        $sheet->setCellValue('B52', 'Tunjangan Pangan');
        $sheet->setCellValue('C52', '');

        $sheet->setCellValue('A53', '36');
        $sheet->setCellValue('B53', 'Asrama Santri (kafalah, air, uang makan, sewa BTN)');
        $sheet->setCellValue('C53', '');

        $sheet->setCellValue('A54', '37');
        $sheet->setCellValue('B54', 'Sarpras');
        $sheet->setCellValue('C54', '');

        $sheet->setCellValue('A55', 'Total 1-37');$sheet->mergeCells('A55:B55');
        $sheet->setCellValue('C55', '');
        $sheet->getStyle('A55:C55')->getFont()->setBold(true);

        $sheet->setCellValue('A56', 'SALDO');$sheet->mergeCells('A56:B56');
        $sheet->setCellValue('C56', '');
        $sheet->getStyle('A56:C56')->getFont()->setBold(true);
        
        $sheet->getStyle('A5:C56')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // $sheet->getStyle('A5:I'.($totalRow - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        // $sheet->getStyle('A5:I'.($totalRow - 1))->getAlignment()->setWrapText(true);
        // $sheet->getStyle('A5:A'.($totalRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $sheet->getStyle('B5:B'.($totalRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $sheet->getStyle('F5:H'.($totalRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        // $sheet->getStyle('E' . $totalRow . ':H' . $totalRow)->getFont()->setBold(true);

        $type = 'xlsx';
        $fileName = 'REKAP KEUANGAN YAYASAN PENDIDIKAN ALQALAM KENDARI BULAN '.$request->month.' TAHUN '.$request->year.'.'.$type;

        if ($type == 'xlsx') {
            $writer = new Xlsx($spreadsheet);
        } elseif ($type == 'xls') {
            $writer = new Xls($spreadsheet);
        }
        $writer->save('public/upload/report/'.$fileName);
        header('Content-Type: application/vnd.ms-excel');

        return redirect(url('/').'/public/upload/report/'.$fileName);
    }

    public function print_kpi_yearly(Request $request)
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        if ($request->work_unit_id) {
            $work_unit = WorkUnit::where('id', $request->work_unit_id)->first();
            $employee = Employee::where('work_unit_id', $request->work_unit_id)->orderBy('name', 'ASC')->get();

        } else {
            $work_unit = null;
            $employee = Employee::orderBy('name', 'ASC')->get();
        }

        // Atur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(40);  // Nama
        $sheet->getColumnDimension('C')->setWidth(8);  // Nilai Rapor
        $sheet->getColumnDimension('D')->setWidth(8);  // KPI

        $sheet->getColumnDimension('E')->setWidth(8);  // Jan
        $sheet->getColumnDimension('F')->setWidth(8);  // Feb
        $sheet->getColumnDimension('G')->setWidth(8);  // Mar
        $sheet->getColumnDimension('H')->setWidth(8);  // Apr
        $sheet->getColumnDimension('I')->setWidth(8);  // Mei
        $sheet->getColumnDimension('J')->setWidth(8);  // Jun
        $sheet->getColumnDimension('K')->setWidth(8);  // Jul
        $sheet->getColumnDimension('L')->setWidth(8);  // Agu
        $sheet->getColumnDimension('M')->setWidth(8);  // Sep
        $sheet->getColumnDimension('N')->setWidth(8);  // Okt
        $sheet->getColumnDimension('O')->setWidth(8);  // Nov
        $sheet->getColumnDimension('P')->setWidth(8);  // Des

        $sheet->getColumnDimension('Q')->setWidth(15);
        $sheet->getColumnDimension('R')->setWidth(15);
        $sheet->getColumnDimension('S')->setWidth(15);
        $sheet->getColumnDimension('T')->setWidth(15);
        $sheet->getColumnDimension('U')->setWidth(15);

        if ($work_unit) {
            $sheet->mergeCells('A1:U1');
            $sheet->mergeCells('A2:U2');
            $sheet->mergeCells('A3:U3');
            $sheet->setCellValue('A1', 'REKAP BULANAN PENILAIAN KPI YAYASAN PENDIDIKAN ALQALAM KENDARI');
            $sheet->setCellValue('A2', strtoupper($work_unit->name));
            $sheet->setCellValue('A3', 'TAHUN '.$request->year);
            $sheet->getStyle('A1:U3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:U3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1:U3')->getFont()->setBold(true);
            $sheet->getStyle('A1:U3')->getAlignment()->setWrapText(true);
        } else {
            $sheet->mergeCells('A1:U1');
            $sheet->mergeCells('A2:U2');
            $sheet->setCellValue('A1', 'REKAP BULANAN PENILAIAN KPI YAYASAN PENDIDIKAN ALQALAM KENDARI');
            $sheet->setCellValue('A2', 'TAHUN '.$request->year);
            $sheet->getStyle('A1:U2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:U2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1:U2')->getFont()->setBold(true);
            $sheet->getStyle('A1:U2')->getAlignment()->setWrapText(true);
        }

        // Header tabel
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Nama');
        $sheet->setCellValue('C5', 'Rapor');
        $sheet->setCellValue('D5', 'KPI');
        $sheet->setCellValue('E5', 'Jan');
        $sheet->setCellValue('F5', 'Feb');
        $sheet->setCellValue('G5', 'Mar');
        $sheet->setCellValue('H5', 'Apr');
        $sheet->setCellValue('I5', 'Mei');
        $sheet->setCellValue('J5', 'Jun');
        $sheet->setCellValue('K5', 'Jul');
        $sheet->setCellValue('L5', 'Agu');
        $sheet->setCellValue('M5', 'Sep');
        $sheet->setCellValue('N5', 'Okt');
        $sheet->setCellValue('O5', 'Nov');
        $sheet->setCellValue('P5', 'Des');
        $sheet->setCellValue('Q5', 'Rata-rata Tahunan');
        $sheet->setCellValue('R5', 'Predikat');
        $sheet->setCellValue('S5', 'Tukin');
        $sheet->setCellValue('T5', 'Besaran Tukin');
        $sheet->setCellValue('U5', 'Tukin Diterima');

        $sheet->getStyle('A5:U5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:U5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A5:U5')->getFont()->setBold(true);
        $sheet->getStyle('A5:U5')->getAlignment()->setWrapText(true);

        $rows = 6;
        $no = 1;

        foreach ($employee as $v) {

            $employee_report = EmployeeReport::whereHas('employee_report_period', function ($query) use ($v, $request) {
                $query->where('employee_id', $v->id)
                    ->whereMonth('date', $request->month)
                    ->whereyear('date', $request->year);
            })->sum('value');

            $employee_kpi = EmployeeReport::whereHas('employee_report_period', function ($query) use ($v, $request) {
                $query->where('employee_id', $v->id)
                    ->whereMonth('date', $request->month)
                    ->whereyear('date', $request->year);
            })->sum('value');

            $sheet->setCellValue('A'.$rows, $no++);
            $sheet->setCellValue('B'.$rows, $v->name);
            $sheet->setCellValue('C'.$rows, $employee_report);
            $sheet->setCellValue('Q'.$rows, '=AVERAGE(E'.$rows.':P'.$rows.')');
            $sheet->setCellValue('R'.$rows, '=IF(Q'.$rows.'>=90,"Sangat Baik",IF(Q'.$rows.'>=80,"Baik",IF(Q'.$rows.'>=70,"Cukup",IF(Q'.$rows.'>=60,"Kurang","Perlu Pembinaan"))))');
            $sheet->setCellValue('S'.$rows, '=IF(Q'.$rows.'>=90,100%,IF(Q'.$rows.'>=80,90%,IF(Q'.$rows.'>=70,80%,IF(Q'.$rows.'>=60,70%,IF(Q'.$rows.'>=50,60%,IF(Q'.$rows.'>=40,50%,IF(Q'.$rows.'>=30,40%,30%)))))))');
            $sheet->setCellValue('T'.$rows, '200000');
            $sheet->setCellValue('U'.$rows, '=T'.$rows.'*S'.$rows);
            $sheet->getStyle('T'.$rows.':U'.$rows)->getNumberFormat()->setFormatCode('#,##0');
            $rows++;
        }

        // Border dan alignment
        $sheet->getStyle('A5:U'.($rows - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A5:A'.($rows - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('U5:U'.($rows - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $type = 'xlsx';
        if ($work_unit) {
            $fileName = 'REKAP RAPOR YAYASAN PENDIDIKAN ALQALAM KENDARI ('.strtoupper($work_unit->name).') TAHUN '.$request->year.'.'.$type;
        } else {
            $fileName = 'REKAP RAPOR YAYASAN PENDIDIKAN ALQALAM KENDARI TAHUN '.$request->year.' .'.$type;
        }

        if ($type == 'xlsx') {
            $writer = new Xlsx($spreadsheet);
        } elseif ($type == 'xls') {
            $writer = new Xls($spreadsheet);
        }
        $writer->save('public/upload/report/'.$fileName);
        header('Content-Type: application/vnd.ms-excel');

        return redirect(url('/').'/public/upload/report/'.$fileName);
    }
}
