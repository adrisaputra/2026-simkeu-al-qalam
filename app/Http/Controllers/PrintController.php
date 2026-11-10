<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Classes;
use App\Models\EmployeeKpiBonus;
use App\Models\PeriodizationHistory;
use App\Models\Salary;
use App\Models\Transaction;
use App\Models\TransactionBank;
use App\Models\WorkUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PrintController extends Controller
{
    // # Show Data
    public function index()
    {
        $title = 'Laporan';
        $work_unit = WorkUnit::whereIn('id', [3, 4, 5, 6])->get();
        $work_unit2 = WorkUnit::get();
        return view('admin.print.index', compact('title', 'work_unit','work_unit2'));
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
            } else if($request->recap_type == 3){
                return $this->print_activity($request);
            } else if($request->recap_type == 4){
                return $this->print_balance_sheet($request);
            }
        } elseif ($request->category_report == 3) {
            if($request->salary_type == 1){
                return $this->print_salary($request);
            } else if($request->salary_type == 2){
                return $this->print_salary_slip($request);
            } 
        } 
    }

    ## 1
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
                        ->where('transaction_code_id',2)
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
                        ->where('transaction_code_id',2)
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
                        ->where('transaction_code_id',3)
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
                        ->where('transaction_code_id',3)
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
                        ->where('transaction_code_id',2)
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
                        ->where('transaction_code_id',2)
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
                        ->where('transaction_code_id',3)
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
                        ->where('transaction_code_id',3)
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

    ## 2
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
        $sheet->getStyle('C5')->getNumberFormat()->setFormatCode('#,##0');

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

    public function print_activity(Request $request)
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
        $sheet->setCellValue('A2', 'TAHUN '.$request->year);
        $sheet->getStyle('A1:C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:C2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:C2')->getFont()->setBold(true);
        $sheet->getStyle('A1:C2')->getAlignment()->setWrapText(true);

        // Header tabel
        $sheet->setCellValue('A5', 'PENERIMAAN TAHUN '.$request->year);$sheet->mergeCells('A5:B5');
        $sheet->setCellValue('C5', '17977204');
        $sheet->getStyle('A5:B5')->getFont()->setBold(true);
        $sheet->getStyle('C5')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('A6', '1');
        $sheet->setCellValue('B6', 'INFAK GEDUNG');
        $sheet->setCellValue('C6', '');

        $sheet->setCellValue('A7', '2');
        $sheet->setCellValue('B7', 'INFAQ TANAH');
        $sheet->setCellValue('C7', '');

        $sheet->setCellValue('A8', '3');
        $sheet->setCellValue('B8', 'PENINGKATAN MUTU');
        $sheet->setCellValue('C8', '');

        $sheet->setCellValue('A9', '4');
        $sheet->setCellValue('B9', 'SPP');
        $sheet->setCellValue('C9', '');

        $sheet->setCellValue('A10', '5');
        $sheet->setCellValue('B10', 'CATERING');
        $sheet->setCellValue('C10', '');

        $sheet->setCellValue('A11', '6');
        $sheet->setCellValue('B11', 'RAPORT');
        $sheet->setCellValue('C11', '');

        $sheet->setCellValue('A12', '7');
        $sheet->setCellValue('B12', 'SERAGAM');
        $sheet->setCellValue('C12', '');

        $sheet->setCellValue('A13', '8');
        $sheet->setCellValue('B13', 'BOS / BOP');
        $sheet->setCellValue('C13', '');

        $sheet->setCellValue('A14', '9');
        $sheet->setCellValue('B14', 'WISUDA');
        $sheet->setCellValue('C14', '');

        $sheet->setCellValue('A15', '10');
        $sheet->setCellValue('B15', 'FORMULIR PPDB');
        $sheet->setCellValue('C15', '');

        $sheet->setCellValue('A16', '11');
        $sheet->setCellValue('B16', 'QURBAN');
        $sheet->setCellValue('C16', '');

        $sheet->setCellValue('A17', '12');
        $sheet->setCellValue('B17', "INFAQ KELAS / INFAQ JUM'AT / DONASI KEMANUSIAAN");
        $sheet->setCellValue('C17', '');

        $sheet->setCellValue('A18', '13');
        $sheet->setCellValue('B18', 'PENDAPATAN LAIN-LAIN');
        $sheet->setCellValue('C18', '');

        $sheet->setCellValue('A19', 'JUMLAH PEMASUKAN');$sheet->mergeCells('A19:B19');
        $sheet->setCellValue('B19', '-');
        $sheet->setCellValue('C19', '');
        $sheet->getStyle('A19:C19')->getFont()->setBold(true);

        $sheet->setCellValue('A20', '');$sheet->mergeCells('A20:C20');

        // Pengeluaran
        $sheet->setCellValue('A21', 'PENGELUARAN TAHUN '.$request->year);$sheet->mergeCells('A21:B21');
        $sheet->getStyle('A21:C21')->getFont()->setBold(true);

        $sheet->setCellValue('A22', '1');
        $sheet->setCellValue('B22', 'PINJAMAN GURU / PEGAWAI / UNIT');
        $sheet->setCellValue('C22', '');

        $sheet->setCellValue('A23', '2');
        $sheet->setCellValue('B23', 'GAJI GURU DAN PEGAWAI');
        $sheet->setCellValue('C23', '');

        $sheet->setCellValue('A24', '3');
        $sheet->setCellValue('B24', 'BIAYA KESEKRETARIATAN');
        $sheet->setCellValue('C24', '');

        $sheet->setCellValue('A25', '4');
        $sheet->setCellValue('B25', 'BIAYA KANTOR DAN PEMELIHARAAN');
        $sheet->setCellValue('C25', '');

        $sheet->setCellValue('A26', '5');
        $sheet->setCellValue('B26', 'BIAYA BBM, PARKIR DAN TRANSPORT');
        $sheet->setCellValue('C26', '');

        $sheet->setCellValue('A27', '6');
        $sheet->setCellValue('B27', 'BIAYA LISTRIK, AIR, TELPON DAN INTERNET');
        $sheet->setCellValue('C27', '');

        $sheet->setCellValue('A28', '7');
        $sheet->setCellValue('B28', 'BIAYA ASURANSI, INFAQ DAN PAJAK');
        $sheet->setCellValue('C28', '');

        $sheet->setCellValue('A29', '8');
        $sheet->setCellValue('B29', 'BIAYA PROGRAM DAN KEGIATAN');
        $sheet->setCellValue('C29', '');

        $sheet->setCellValue('A30', '9');
        $sheet->setCellValue('B30', 'BIAYA KONSUMSI GURU, SISWA DAN KONSUMSI RAPAT');
        $sheet->setCellValue('C30', '');

        $sheet->setCellValue('A31', '10');
        $sheet->setCellValue('B31', 'BIAYA ADMINISTRASI DAN UMUM LAINNYA');
        $sheet->setCellValue('C31', '');

        $sheet->setCellValue('A32', '11');
        $sheet->setCellValue('B32', 'BIAYA PENDIDIKAN, KESEHATAN DAN SOSIAL');
        $sheet->setCellValue('C32', '');

        $sheet->setCellValue('A33', '12');
        $sheet->setCellValue('B33', 'SERAGAM');
        $sheet->setCellValue('C33', '');

        $sheet->setCellValue('A34', '13');
        $sheet->setCellValue('B34', 'BIAYA PERJALANAN DINAS');
        $sheet->setCellValue('C34', '');

        $sheet->setCellValue('A35', '14');
        $sheet->setCellValue('B35', 'INVENTARIS DAN PERALATAN');
        $sheet->setCellValue('C35', '');

        $sheet->setCellValue('A36', 'JUMLAH PENGELUARAN');$sheet->mergeCells('A36:B36');
        $sheet->setCellValue('C36', '');
        $sheet->getStyle('A36:C36')->getFont()->setBold(true);

        $sheet->setCellValue('A37', 'SELISIH');$sheet->mergeCells('A37:B37');
        $sheet->setCellValue('C37', '');
        $sheet->getStyle('A37:C37')->getFont()->setBold(true);

        $sheet->getStyle('A5:C37')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // $sheet->getStyle('A5:I'.($totalRow - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        // $sheet->getStyle('A5:I'.($totalRow - 1))->getAlignment()->setWrapText(true);
        // $sheet->getStyle('A5:A'.($totalRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $sheet->getStyle('B5:B'.($totalRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $sheet->getStyle('F5:H'.($totalRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        // $sheet->getStyle('E' . $totalRow . ':H' . $totalRow)->getFont()->setBold(true);

        $type = 'xlsx';
        $fileName = 'REKAP AKTIVITAS YAYASAN PENDIDIKAN ALQALAM KENDARI TAHUN '.$request->year.'.'.$type;

        if ($type == 'xlsx') {
            $writer = new Xlsx($spreadsheet);
        } elseif ($type == 'xls') {
            $writer = new Xls($spreadsheet);
        }
        $writer->save('public/upload/report/'.$fileName);
        header('Content-Type: application/vnd.ms-excel');

        return redirect(url('/').'/public/upload/report/'.$fileName);
    }

    public function print_balance_sheet(Request $request)
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(2);
        $sheet->getColumnDimension('B')->setWidth(3);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(4);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(4);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(3);
        $sheet->getColumnDimension('I')->setWidth(3);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(4);
        $sheet->getColumnDimension('L')->setWidth(12);
        $sheet->getColumnDimension('M')->setWidth(4);
        $sheet->getColumnDimension('N')->setWidth(12);
        $sheet->getColumnDimension('O')->setWidth(2);

        $sheet->mergeCells('B2:N2');
        $sheet->mergeCells('B3:N3');
        $sheet->mergeCells('B4:N4');
        $sheet->setCellValue('B2', 'NERACA');
        $sheet->setCellValue('B3', 'YAYASAN PENDIDIKAN AL QALAM KENDARI');
        $sheet->setCellValue('B4', 'PER 31 DESEMBER '.$request->year);
        $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:C4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:C4')->getFont()->setBold(true);
        $sheet->getStyle('A1:C4')->getAlignment()->setWrapText(true);

        $drawing = new Drawing();

        $drawing->setName('Logo Yayasan');
        $drawing->setDescription('Logo Yayasan Al Qalam');
        $drawing->setPath(public_path('storage/upload/setting/11788185158.png'));

        // Posisi gambar mengikuti awal setiap slip
        $drawing->setCoordinates('D2');
        $drawing->setHeight(70);
        $drawing->setWorksheet($sheet);

        // Header tabel
        $sheet->setCellValue('B6', 'AKTIVA LANCAR');$sheet->getStyle('B6')->getFont()->setBold(true);
        
        ## 1
        $sheet->setCellValue('B7', '*');
        $sheet->setCellValue('C7', 'KAS');
        $sheet->setCellValue('D7', 'Rp.');
        $sheet->setCellValue('E7', '10207624');
        $sheet->getStyle('E7')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('E7')->getFont()->setBold(true);

        ## 2
        $sheet->setCellValue('B8', '*');
        $sheet->setCellValue('C8', 'KAS BANK');

        $sheet->setCellValue('C9', 'BMT ASSALAM (Yay. Pend. Al Qalam)');
        $sheet->setCellValue('D9', 'Rp.');
        $sheet->setCellValue('E9', '10207624');
        $sheet->getStyle('E9')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('C10', 'Muamalat');
        $sheet->setCellValue('D10', 'Rp.');
        $sheet->setCellValue('E10', '10207624');
        $sheet->getStyle('E10')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('C11', 'PPDB TKIT');
        $sheet->setCellValue('D11', 'Rp.');
        $sheet->setCellValue('E11', '10207624');
        $sheet->getStyle('E11')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('C12', 'PPDB SDIT');
        $sheet->setCellValue('D12', 'Rp.');
        $sheet->setCellValue('E12', '10207624');
        $sheet->getStyle('E12')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('C13', 'PPDB SMPIT');
        $sheet->setCellValue('D13', 'Rp.');
        $sheet->setCellValue('E13', '10207624');
        $sheet->getStyle('E13')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('C14', 'PPDB SMAIT');
        $sheet->setCellValue('D14', 'Rp.');
        $sheet->setCellValue('E14', '10207624');
        $sheet->getStyle('E14')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('D15', 'Rp.');
        $sheet->setCellValue('E15', '=SUM(E7:E14)');
        $sheet->getStyle('E15')->getNumberFormat()->setFormatCode('#,##0');

        ## 3
        
        $sheet->setCellValue('B16', '*');
        $sheet->setCellValue('C16', 'PIUTANG YAYASAN');

        $sheet->setCellValue('C17', 'Piutang Biaya Pendidikan TKIT');
        $sheet->setCellValue('D17', 'Rp.');
        $sheet->setCellValue('E17', '10207624');
        $sheet->getStyle('E17')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('C18', 'Piutang Biaya Pendidikan SDIT');
        $sheet->setCellValue('D18', 'Rp.');
        $sheet->setCellValue('E18', '10207624');
        $sheet->getStyle('E18')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('C19', 'Piutang Biaya Pendidikan SMPIT');
        $sheet->setCellValue('D19', 'Rp.');
        $sheet->setCellValue('E19', '10207624');
        $sheet->getStyle('E19')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('C20', 'Piutang Biaya Pendidikan SMAIT');
        $sheet->setCellValue('D20', 'Rp.');
        $sheet->setCellValue('E20', '10207624');
        $sheet->getStyle('E20')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('D21', 'Rp.');
        $sheet->setCellValue('E21', '=SUM(E17:E20)');
        $sheet->getStyle('E21')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('C22', 'TOTAL AKTIVA LANCAR');
        $sheet->setCellValue('F22', 'Rp.');
        $sheet->setCellValue('G22', '=E21+E15');
        $sheet->getStyle('G22')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('C22:G22')->getFont()->setBold(true);

        ## 4
        $sheet->setCellValue('B24', 'AKTIVA TETAP');$sheet->getStyle('B24')->getFont()->setBold(true);
        
        $sheet->setCellValue('B25', '*');
        $sheet->setCellValue('C25', 'TANAH');
        $sheet->setCellValue('D25', 'Rp.');
        $sheet->setCellValue('E25', '10207624');
        $sheet->getStyle('E25')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('B26', '*');
        $sheet->setCellValue('C26', 'GEDUNG');
        $sheet->setCellValue('D26', 'Rp.');
        $sheet->setCellValue('E26', '10207624');
        $sheet->getStyle('E26')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('B27', '*');
        $sheet->setCellValue('C27', 'PERALATAN');
        $sheet->setCellValue('D27', 'Rp.');
        $sheet->setCellValue('E27', '10207624');
        $sheet->getStyle('E27')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('C29', 'TOTAL AKTIVA TETAP');
        $sheet->setCellValue('F29', 'Rp.');
        $sheet->setCellValue('G29', '=E21+E15');
        $sheet->getStyle('G29')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('C29:G29')->getFont()->setBold(true);

        $sheet->setCellValue('C31', 'JUMLAH AKTIVA');
        $sheet->setCellValue('F31', 'Rp.');
        $sheet->setCellValue('G31', '=SUM(G22:G29)');
        $sheet->getStyle('G31')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('C31:G31')->getFont()->setBold(true);

        ## HALAMAN 2
        
        $sheet->setCellValue('I6', 'HUTANG');$sheet->getStyle('I6')->getFont()->setBold(true);
        
        ## 1
        $sheet->setCellValue('I7', '*');
        $sheet->setCellValue('J7', 'HUTANG BANK');
        $sheet->setCellValue('K7', 'Rp.');
        $sheet->setCellValue('L7', '10207624');
        $sheet->getStyle('L7')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('I8', '*');
        $sheet->setCellValue('J8', 'HUTANG PERORANGAN');
        $sheet->setCellValue('K8', 'Rp.');
        $sheet->setCellValue('L8', '10207624');
        $sheet->getStyle('L8')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('J22', 'TOTAL KEWAJIBAN LANCAR');
        $sheet->setCellValue('M22', 'Rp.');
        $sheet->setCellValue('N22', '=SUM(L7:L16)');
        $sheet->getStyle('N22')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('J22:N22')->getFont()->setBold(true);

        $sheet->setCellValue('I25', 'MODAL');
        $sheet->setCellValue('M25', 'Rp.');
        $sheet->setCellValue('N25', '=G31-N22');
        $sheet->getStyle('N25')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I25:N25')->getFont()->setBold(true);

        $sheet->setCellValue('I31', 'JUMLAH KEWAJIBAN');
        $sheet->setCellValue('M31', 'Rp.');
        $sheet->setCellValue('N31', '=SUM(N19:N29)');
        $sheet->getStyle('N31')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I31:N31')->getFont()->setBold(true);

        $sheet->setCellValue('L33', 'Kendari, 31 Desember '.$request->year);
        $sheet->setCellValue('B34', 'Ketua Yayasan Pendidikan Al Qalam Kendari');
        $sheet->setCellValue('L34', 'Bendahara');

        $sheet->getStyle('A6:G31')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('H6:N31')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A6:G6')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A24:G24')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('H6:N6')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
        
        $type = 'xlsx';
        $fileName = 'NERACA YAYASAN PENDIDIKAN ALQALAM KENDARI TAHUN '.$request->year.'.'.$type;

        if ($type == 'xlsx') {
            $writer = new Xlsx($spreadsheet);
        } elseif ($type == 'xls') {
            $writer = new Xls($spreadsheet);
        }
        $writer->save('public/upload/report/'.$fileName);
        header('Content-Type: application/vnd.ms-excel');

        return redirect(url('/').'/public/upload/report/'.$fileName);
    }

    ## 3
    public function print_salary(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        if ($request->work_unit_id2) {
            $work_unit = WorkUnit::where('id', $request->work_unit_id2)->first();
            $salary = Salary::with('employee')
                    ->where('salaries.month', $request->month)
                    ->where('salaries.year', $request->year)
                    ->join('db_2025_simpeg_al_qalam.employees', 'salaries.employee_id', '=', 'employees.id')
                    ->where('employees.work_unit_id', $request->work_unit_id2)
                    ->orderBy('employees.name', 'ASC')
                    ->select('salaries.*')
                    ->get();
            
        } else {
            $work_unit = NULL;
            $salary = Salary::with('employee')
                    ->where('salaries.month', $request->month)
                    ->where('salaries.year', $request->year)
                    ->join('db_2025_simpeg_al_qalam.employees', 'salaries.employee_id', '=', 'employees.id')
                    ->orderBy('employees.name', 'ASC')
                    ->select('salaries.*')
                    ->get();
        }

        // Atur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(40);  // Nama
        $sheet->getColumnDimension('C')->setWidth(10);  // Nilai Rapor
        $sheet->getColumnDimension('D')->setWidth(10);  // KPI
        $sheet->getColumnDimension('E')->setWidth(13);
        $sheet->getColumnDimension('F')->setWidth(55);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(12);
        $sheet->getColumnDimension('K')->setWidth(12);
        $sheet->getColumnDimension('L')->setWidth(12);
        $sheet->getColumnDimension('M')->setWidth(12);
        $sheet->getColumnDimension('N')->setWidth(12);
        $sheet->getColumnDimension('O')->setWidth(12);
        $sheet->getColumnDimension('P')->setWidth(12);
        $sheet->getColumnDimension('Q')->setWidth(12);
        $sheet->getColumnDimension('R')->setWidth(12);
        $sheet->getColumnDimension('S')->setWidth(12);
        $sheet->getColumnDimension('T')->setWidth(12);
        $sheet->getColumnDimension('U')->setWidth(12);
        $sheet->getColumnDimension('V')->setWidth(12);
        $sheet->getColumnDimension('W')->setWidth(12);

        if ($work_unit) {
            $sheet->mergeCells('A1:N1');
            $sheet->mergeCells('A2:N2');
            $sheet->mergeCells('A3:N3');
            $sheet->setCellValue('A1', 'YAYASAN PENDIDIKAN ALQALAM KENDARI');
            $sheet->setCellValue('A2', 'STRUKTUR PENGGAJIAN '.strtoupper($work_unit->name));
            $sheet->setCellValue('A3', 'BULAN '.$request->month.' TAHUN '.$request->year);
            $sheet->getStyle('A1:N3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:N3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1:N3')->getFont()->setBold(true);
            $sheet->getStyle('A1:N3')->getAlignment()->setWrapText(true);
        } else {
            $sheet->mergeCells('A1:N1');
            $sheet->mergeCells('A2:N2');
            $sheet->setCellValue('A1', 'YAYASAN PENDIDIKAN ALQALAM KENDARI');
            $sheet->setCellValue('A2', 'BULAN '.$request->month.' TAHUN '.$request->year);
            $sheet->getStyle('A1:N2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:N2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1:N2')->getFont()->setBold(true);
            $sheet->getStyle('A1:N2')->getAlignment()->setWrapText(true);
        }

        // Header tabel
        $sheet->setCellValue('A5', 'No'); $sheet->mergeCells('A5:A6');
        $sheet->setCellValue('B5', 'Nama'); $sheet->mergeCells('B5:B6');
        $sheet->setCellValue('C5', 'Periode'); $sheet->mergeCells('C5:C6');
        $sheet->setCellValue('D5', 'Status Kepegawaian'); $sheet->mergeCells('D5:D6');
        $sheet->setCellValue('E5', 'TMT'); $sheet->mergeCells('E5:E6');
        $sheet->setCellValue('F5', 'Pendidikan'); $sheet->mergeCells('F5:F6');
        $sheet->setCellValue('G5', 'Jumlah Gaji/Honor + Tunjangan'); $sheet->mergeCells('G5:S5');
        $sheet->setCellValue('T5', 'Pot. Rumbay'); $sheet->mergeCells('T5:T6');
        $sheet->setCellValue('U5', 'Jumlah Gaji'); $sheet->mergeCells('U5:U6');
        $sheet->setCellValue('V5', 'Jumlah yang diterima'); $sheet->mergeCells('V5:V6');

        $sheet->setCellValue('G6', 'Gaji Pokok/ Gaji Honor');
        $sheet->setCellValue('H6', 'Tunj. Jabatan');
        $sheet->setCellValue('I6', 'Tunj. Transport');
        $sheet->setCellValue('J6', 'Tunj. Komunikasi');
        $sheet->setCellValue('K6', 'Tunj. Keluarga');
        $sheet->setCellValue('L6', 'Tunj. Pensiun');
        $sheet->setCellValue('M6', 'Tunj. Kesehatan');
        $sheet->setCellValue('N6', 'Bonus TTQ');
        $sheet->setCellValue('O6', 'Penyesuaian');
        $sheet->setCellValue('P6', 'BOS');
        $sheet->setCellValue('Q6', 'Kelebihan JP');
        $sheet->setCellValue('R6', 'Tunjangan Kinerja');
        $sheet->setCellValue('S6', 'Uang Makan');

        $sheet->getStyle('A5:V6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:V6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A5:V6')->getFont()->setBold(true);
        $sheet->getStyle('A5:V6')->getAlignment()->setWrapText(true);

        $rows = 7;
        $no = 1;

        foreach ($salary as $v) {

            $employee_kpi_ttq = EmployeeKpiBonus::
                    whereHas('employee_kpi_period', function ($query) use ($v) {
                        $query->where('employee_id', $v->employee_id)
                        ->where('month', $v->month)
                        ->where('year', $v->year)
                        ->where('category', 1);
                    })->sum('value');
            
            $employee_kpi_catering = EmployeeKpiBonus::
                    whereHas('employee_kpi_period', function ($query) use ($v) {
                        $query->where('employee_id', $v->employee_id)
                        ->where('month', $v->month)
                        ->where('year', $v->year)
                        ->where('category', 2);
                    })->sum('value');
            
            $employee_kpi_jp = EmployeeKpiBonus::
                    whereHas('employee_kpi_period', function ($query) use ($v) {
                        $query->where('employee_id', $v->employee_id)
                        ->where('month', $v->month)
                        ->where('year', $v->year)
                        ->where('category', 3);
                    })->sum('value');
            
            $periodization = PeriodizationHistory::where('employee_id', $v->employee_id)->latest('id')->first();
             switch ($periodization->periodization) {
                case 0:
                    $periodization = '-';break;
                case 1:
                    $periodization = 'I';break;
                case 2:
                    $periodization = 'II';break;
                case 3:
                    $periodization = 'III';break;
                case 4:
                    $periodization = 'IV';break;
                case 5:
                    $periodization = 'V';break;
                default:
                    $periodization = null;
            }

            $tmt = Carbon::parse($v->employee->tmt);

            // $masa_kerja = $tmt->diff(now());
            $tanggal_hitung = Carbon::create($v->year, $v->month, 1)->endOfMonth();
            $masa_kerja = $tmt->diff($tanggal_hitung);

            $tahun = $masa_kerja->y;
            $bulan = $masa_kerja->m;

            $masa_kerja_value = $tahun . ' Thn ' . $bulan . ' Bln';
            
            $total_bulan = ($tahun * 12) + $bulan;

            // Tentukan status dan tambahan gaji
            if ($total_bulan < 12) {
                $status = 'GTTY';
                $tambahan_gaji = 50000;
            } 
            
            if ($v->employee->niy != null) {
                $status = 'GTY';

                if ($total_bulan < 72) {
                    $tambahan_gaji = 75000;
                } elseif ($total_bulan < 132) {
                    $tambahan_gaji = 100000;
                } else {
                    $tambahan_gaji = 150000;
                }
            } else {
                $status = '-';
                $tambahan_gaji = 0;
            }

            $sheet->setCellValue('A' . $rows, $no++);
            $sheet->setCellValue('B' . $rows, $v->employee?->name);
            $sheet->setCellValue('C' . $rows, $periodization);
            $sheet->setCellValue('D' . $rows, $status);
            $sheet->setCellValue('E' . $rows, $masa_kerja_value);
            $sheet->setCellValue('F' . $rows, $v->employee?->education);
            $sheet->setCellValue('G' . $rows, '=' . $v->salary .'+'.$tambahan_gaji);
            $sheet->setCellValue('H' . $rows, '=50%*G' . $rows);
            $sheet->setCellValue('I' . $rows, '=12%*G' . $rows);
            $sheet->setCellValue('J' . $rows, '=I'.$rows);
            $sheet->setCellValue('K' . $rows, $v->allowance4);
            $sheet->setCellValue('L' . $rows, '=2%*G' . $rows);
            $sheet->setCellValue('M' . $rows, $v->allowance6);
            $sheet->setCellValue('N' . $rows, $employee_kpi_ttq);
            $sheet->setCellValue('O' . $rows, $v->salary_adjustment);
            $sheet->setCellValue('P' . $rows, $v->bos);
            $sheet->setCellValue('Q' . $rows, $employee_kpi_jp);
            $sheet->setCellValue('R' . $rows, '');
            $sheet->setCellValue('S' . $rows, $employee_kpi_catering);
            $sheet->setCellValue('T' . $rows, $v->rumbay);
            $sheet->setCellValue('U' . $rows, '=SUM(H' . $rows . ':S' . $rows . ')');
            $sheet->setCellValue('V' . $rows, '=U' . $rows . '-T' . $rows . '-S' . $rows . '-M' . $rows . '-L' . $rows . '');
            $sheet->getStyle('G' . $rows . ':V' . $rows)->getNumberFormat()->setFormatCode('#,##0');
            $rows++;
        }

        // Border dan alignment
        $sheet->getStyle('A5:V' . ($rows - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('A5:A' . ($rows - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('U5:V' . ($rows - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C7:E' . ($rows - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $type = 'xlsx';
        if ($work_unit) {
            $fileName = "REKAP GAJI YAYASAN PENDIDIKAN ALQALAM KENDARI (" . strtoupper($work_unit->name) . ") BULAN ".$request->month." TAHUN ".$request->year.".". $type;
        } else {
            $fileName = "REKAP GAJI YAYASAN PENDIDIKAN ALQALAM KENDARI BULAN ".$request->month." TAHUN ".$request->year." .". $type;
        }

        if ($type == 'xlsx') {
            $writer = new Xlsx($spreadsheet);
        } else if ($type == 'xls') {
            $writer = new Xls($spreadsheet);
        }
        $writer->save("public/upload/report/" . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/') . "/public/upload/report/" . $fileName);
    }

    public function print_salary_slip(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        if ($request->work_unit_id2) {
            $work_unit = WorkUnit::where('id', $request->work_unit_id2)->first();
            $salary = Salary::with('employee')
                    ->where('salaries.month', $request->month)
                    ->where('salaries.year', $request->year)
                    ->join('db_2025_simpeg_al_qalam.employees', 'salaries.employee_id', '=', 'employees.id')
                    ->where('employees.work_unit_id', $request->work_unit_id2)
                    ->orderBy('employees.name', 'ASC')
                    ->select('salaries.*')
                    ->get();
            
        } else {
            $work_unit = NULL;
            $salary = Salary::with('employee')
                    ->where('salaries.month', $request->month)
                    ->where('salaries.year', $request->year)
                    ->join('db_2025_simpeg_al_qalam.employees', 'salaries.employee_id', '=', 'employees.id')
                    ->orderBy('employees.name', 'ASC')
                    ->select('salaries.*')
                    ->get();
        }

        // Atur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5); 
        $sheet->getColumnDimension('B')->setWidth(5);  
        $sheet->getColumnDimension('C')->setWidth(22);  
        $sheet->getColumnDimension('D')->setWidth(6);  
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(5);
            
        $rows = 1;

        foreach ($salary as $v) {

            // Fungsi untuk menentukan koordinat baris
            $r = fn($row) => $rows + $row - 1;

            // =========================
            // HEADER YAYASAN
            // =========================

            $sheet->mergeCells('B'.$r(1).':G'.$r(1));
            $sheet->mergeCells('B'.$r(2).':G'.$r(2));
            $sheet->mergeCells('B'.$r(3).':G'.$r(3));

            $drawing = new Drawing();

            $drawing->setName('Logo Yayasan');
            $drawing->setDescription('Logo Yayasan Al Qalam');
            $drawing->setPath(public_path('storage/upload/setting/11788185158.png'));

            // Posisi gambar mengikuti awal setiap slip
            $drawing->setCoordinates('C'.$r(1));
            $drawing->setHeight(60);
            $drawing->setWorksheet($sheet);

            $sheet->setCellValue('B'.$r(1), 'YAYASAN PENDIDIKAN ALQALAM KENDARI');
            $sheet->setCellValue('B'.$r(2), 'SULAWESI TENGGARA');
            $sheet->setCellValue('B'.$r(3), 'Jl. Asrama Haji Kel. Wundudopi Kec. Baruga');
            $sheet->getStyle('B'.$r(3).':G'.$r(3))
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $sheet->getStyle('B'.$r(1).':G'.$r(3))
                ->getAlignment()->setHorizontal('center');

            $sheet->getStyle('B'.$r(1).':G'.$r(3))
                ->getAlignment()->setVertical('center');

            $sheet->getStyle('B'.$r(1).':G'.$r(3))
                ->getFont()->setBold(true);

            $sheet->getStyle('B'.$r(1).':G'.$r(3))
                ->getAlignment()->setWrapText(true);


            // =========================
            // JUDUL SLIP GAJI
            // =========================

            $sheet->mergeCells('B'.$r(5).':G'.$r(5));
            $sheet->mergeCells('B'.$r(6).':G'.$r(6));

            $sheet->setCellValue('B'.$r(5), 'SLIP PEMBAYARAN GAJI');

            $sheet->setCellValue(
                'B'.$r(6),
                'Bulan : '.Helpers::month_name($request->month).' Tahun '.$request->year
            );

            $sheet->getStyle('B'.$r(5).':G'.$r(6))
                ->getAlignment()->setHorizontal('center');

            $sheet->getStyle('B'.$r(5).':G'.$r(6))
                ->getAlignment()->setVertical('center');

            $sheet->getStyle('B'.$r(5).':G'.$r(6))
                ->getFont()->setBold(true);


            // =========================
            // IDENTITAS PEGAWAI
            // =========================

            $sheet->mergeCells('B'.$r(8).':C'.$r(8));
            $sheet->mergeCells('D'.$r(8).':G'.$r(8));

            $sheet->setCellValue('B'.$r(8), 'NAMA');
            $sheet->setCellValue('D'.$r(8), ': '.$v->employee->name);

            $sheet->mergeCells('B'.$r(9).':C'.$r(9));
            $sheet->mergeCells('D'.$r(9).':G'.$r(9));

            $sheet->setCellValue('B'.$r(9), 'NIY');
            $sheet->setCellValue('D'.$r(9), ': '.$v->employee->niy);

            $sheet->mergeCells('B'.$r(10).':C'.$r(10));
            $sheet->mergeCells('D'.$r(10).':G'.$r(10));

            $sheet->setCellValue('B'.$r(10), 'JABATAN');
            $sheet->setCellValue('D'.$r(10), ': ');


            // =========================
            // HEADER TABEL
            // =========================

            $sheet->setCellValue('B'.$r(13), 'NO');
            $sheet->setCellValue('C'.$r(13), 'RINCIAN');
            $sheet->setCellValue('D'.$r(13), '%');
            $sheet->setCellValue('E'.$r(13), 'Gaji Diterima');
            $sheet->setCellValue('F'.$r(13), 'Gaji Disimpan');
            $sheet->setCellValue('G'.$r(13), 'Jumlah');


            // =========================
            // RINCIAN GAJI
            // =========================

            $rincian = [
                'Gaji pokok / Gaji Honor',
                'Tunjangan Jabatan',
                'Tunjangan Transportasi',
                'Tunjangan Komunikasi',
                'Tunjangan Keluarga',
                'Tunjangan Kesehatan',
                'Tunjangan Pensiun',
                'Kelebihan JP',
                'Uang Makan',
                'Bonus TTQ',
                'Tunjangan Kinerja',
            ];

            foreach ($rincian as $index => $nama) {

                $row = $r(14 + $index);

                $sheet->setCellValue('B'.$row, $index + 1);
                $sheet->setCellValue('C'.$row, $nama);
                $sheet->setCellValue('D'.$row, '');
                $sheet->setCellValue('E'.$row, '');
                $sheet->setCellValue('F'.$row, '');
                $sheet->setCellValue('G'.$row, '');
            }


            // =========================
            // TOTAL GAJI
            // =========================

            $totals = [
                25 => 'Jumlah Gaji',
                26 => 'Terbayar ke BPJS',
                27 => 'Jumlah Yang Tersimpan',
                28 => 'Total Yang Diterima',
            ];

            foreach ($totals as $row => $label) {

                $sheet->mergeCells('B'.$r($row).':D'.$r($row));

                $sheet->setCellValue('B'.$r($row), $label);$sheet->getStyle('B'.$r($row))->getFont()->setBold(true);
                $sheet->setCellValue('E'.$r($row), '');$sheet->getStyle('E'.$r($row))->getFont()->setBold(true);
                $sheet->setCellValue('F'.$r($row), '');$sheet->getStyle('F'.$r($row))->getFont()->setBold(true);
                $sheet->setCellValue('G'.$r($row), '');$sheet->getStyle('G'.$r($row))->getFont()->setBold(true);
            }


            // =========================
            // TANDA TANGAN
            // =========================

            $sheet->setCellValue('C'.$r(31), 'Yang Menerima');
            $sheet->setCellValue('C'.$r(35), $v->employee->name);

            $tanggal = Carbon::createFromDate($request->year, $request->month, 1)
                        ->addMonth()
                        ->translatedFormat('F Y');

            $sheet->mergeCells('F'.$r(30).':G'.$r(30));
            $sheet->setCellValue('F'.$r(30), 'Kendari, 15 '.$tanggal);

            $sheet->mergeCells('F'.$r(31).':G'.$r(31));
            $sheet->setCellValue('F'.$r(31), 'Yang Membayar');

            $sheet->mergeCells('F'.$r(32).':G'.$r(32));
            $sheet->setCellValue('F'.$r(32), 'Bendahara Yayasan');

            $sheet->mergeCells('F'.$r(35).':G'.$r(35));
            $sheet->setCellValue('F'.$r(35), '');


            // =========================
            // STYLE
            // =========================

            $sheet->getStyle('C'.$r(30).':G'.$r(35))
                ->getAlignment()->setHorizontal('center');

            $sheet->getStyle('B'.$r(13).':G'.$r(13))
                ->getFont()->setBold(true);

            // Border slip
            $sheet->getStyle('B'.$r(13).':G'.$r(28))
                ->getBorders()->getAllBorders()
                ->setBorderStyle(
                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                );

            // Alignment tabel
            $sheet->getStyle('B'.$r(13).':G'.$r(28))
                ->getAlignment()->setVertical('center');

            $sheet->getStyle('B'.$r(13).':B'.$r(24))
                ->getAlignment()->setHorizontal('center');

            $sheet->getStyle('D'.$r(13).':G'.$r(28))
                ->getAlignment()->setHorizontal('center');

            // =========================
            // POSISI SLIP BERIKUTNYA
            // =========================

            // 35 baris slip + 2 baris kosong
            $rows += 37;
            
            $sheet->getStyle('B'.$r(13).':G'.$r(28))
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(
                        \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    );
            // Border dan alignment
            // $sheet->getStyle('A5:G' . ($rows - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            // $sheet->getStyle('A5:A' . ($rows - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            // $sheet->getStyle('U5:V' . ($rows - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            // $sheet->getStyle('C7:E' . ($rows - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        }

        $type = 'xlsx';
        if ($work_unit) {
            $fileName = "SLIP GAJI YAYASAN PENDIDIKAN ALQALAM KENDARI (" . strtoupper($work_unit->name) . ") BULAN ".$request->month." TAHUN ".$request->year.".". $type;
        } else {
            $fileName = "SLIP GAJI YAYASAN PENDIDIKAN ALQALAM KENDARI BULAN ".$request->month." TAHUN ".$request->year." .". $type;
        }

        if ($type == 'xlsx') {
            $writer = new Xlsx($spreadsheet);
        } else if ($type == 'xls') {
            $writer = new Xls($spreadsheet);
        }
        $writer->save("public/upload/report/" . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/') . "/public/upload/report/" . $fileName);
    }

}
