<?php

namespace App\Helpers;

use App\Models\Setting;   //nama model
use App\Models\Transaction;
use Carbon\Carbon;

class Helpers
{

    public static function setting()
    {
        $setting = Setting::find(1);
        return $setting;
    }

    public static function format_number($value)
    {
        $value = number_format($value, 0, ",", ".");;
        return $value;
    }

    public static function date($value)
    {
        $value = date('d-m-Y', strtotime($value));
        return $value;
    }

    public static function day_name($day)
    {
        switch ($day) {
            case "Monday":
                return "Senin";
            case "Tuesday":
                return "Selasa";
            case "Wednesday":
                return "Rabu";
            case "Thursday":
                return "Kamis";
            case "Friday":
                return "Jumat";
            case "Saturday":
                return "Sabtu";
            case "Sunday":
                return "Minggu";
            default:
                return "Nomor hari tidak valid. Masukkan nomor antara 1 dan 7.";
        }
    }

    public static function month_name($month)
    {
        switch ($month) {
            case "01":
                return "Januari";
            case "02":
                return "Februari";
            case "03":
                return "Maret";
            case "04":
                return "April";
            case "05":
                return "Mei";
            case "06":
                return "Juni";
            case "07":
                return "Juli";
            case "08":
                return "Agustus";
            case "09":
                return "September";
            case "10":
                return "Oktober";
            case "11":
                return "November";
            case "12":
                return "Desember";
            default:
                return "Nomor bulan tidak valid. Masukkan nomor antara 1 dan 12.";
        }
    }


    public static function date_indo_full($value)
    {
        if (!$value) return '-';

        $timestamp = strtotime($value);
        $day_name  = self::day_name(date('l', $timestamp));
        $day       = date('d', $timestamp);
        $month     = self::month_name(date('m', $timestamp));
        $year      = date('Y', $timestamp);

        return $day_name . '/ ' . $day . ' ' . $month . ' ' . $year;
    }


    public static function opening_balance()
    {
        return [
            18 => self::setting()->first_saldo_cash, // Kas Tunai
            19 => self::setting()->first_saldo_bank, // Kas Bank
        ];
    }
    
    public static function cash_balance($payment_account_id, $year)
    {
        $first_year = 2026;

        // Saldo awal
        if ($year == $first_year) {
            $saldo_awal = self::opening_balance()[$payment_account_id] ?? 0;
        } else {
            $saldo_awal = self::cash_balance($payment_account_id, $year - 1);
        }

        // Pemasukan tahun berjalan
        $pemasukan = Transaction::where('type', 'income')
            ->where('payment_account_id', $payment_account_id)
            ->whereYear('date', $year)
            ->sum('amount');

        // Pengeluaran tahun berjalan
        $pengeluaran = Transaction::where('type', 'expense')
            ->where('payment_account_id', $payment_account_id)
            ->whereYear('date', $year)
            ->sum('amount');

        return $saldo_awal + $pemasukan - $pengeluaran;
    }

    public static function length_of_service($tmt, $month, $year)
    {
         
        $tmt = Carbon::parse($tmt);

        // Tentukan awal perhitungan TMT
        if ($tmt->day >= 15) {
            $tmt->addMonthNoOverflow();
        }

        $tmt->startOfMonth();

        // Tanggal perhitungan berdasarkan periode gaji
        $tanggal_hitung = Carbon::create($year, $month, 1)->endOfMonth();

        // Hitung masa kerja
        $masa_kerja = $tmt->diff($tanggal_hitung);

        $tahun = $masa_kerja->y;
        $bulan = $masa_kerja->m;

        return $tahun . ' Thn ' . $bulan . ' Bln';
    }

    public static function salary_allowance($tmt, $niy, $month, $year)
    {
         
        $tmt = Carbon::parse($tmt);

        // Tentukan awal perhitungan TMT
        if ($tmt->day >= 15) {
            $tmt->addMonthNoOverflow();
        }

        $tmt->startOfMonth();

        // Tanggal perhitungan berdasarkan periode gaji
        $tanggal_hitung = Carbon::create($year, $month, 1)->endOfMonth();

        // Hitung masa kerja
        $masa_kerja = $tmt->diff($tanggal_hitung);

        $tahun = $masa_kerja->y;
        $bulan = $masa_kerja->m;

        $total_bulan = ($tahun * 12) + $bulan;

        // Tentukan status dan tambahan gaji
        if ($niy != null) {
            if ($total_bulan < 72) {
                $salary_allowance = 75000;
            } elseif ($total_bulan < 132) {
                $salary_allowance = 100000;
            } else {
                $salary_allowance = 150000;
            }
        } elseif ($total_bulan < 12) {
            $salary_allowance = 50000;
        } else {
            $salary_allowance = 0;
        }

        return $salary_allowance;
    }


}
