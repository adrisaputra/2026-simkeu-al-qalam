@extends('admin.layout')
@section('content')
<!--  BEGIN CONTENT AREA  -->
<div id="content" class="main-content">
    <div class="layout-px-spacing">

        <div class="row layout-top-spacing">
            <div id="tableHover" class="col-lg-12 col-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <h4>Cetak {{ __($title) }}</h4>
                            </div>
                        </div>
                    </div>

                    <form action="{{ url(Request::segment(1)) }}" method="POST">
                        @csrf
                        <div class="widget-content widget-content-area">
                            <div class="row">
                                <div class="col-xl-3 col-md-12 col-sm-12 col-12">
                                    <div class="form-group" style="margin-bottom: 0rem;">
                                        <select class="form-control form-control-sm" name="work_unit_id">
                                            
                                            @if(Auth::user()->group->name == 'Admin KPI')
                                                <option value="">- Pilih Unit Kerja -</option>
                                            @endif
                                            @foreach($work_unit as $v)
                                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-xl-3 col-md-12 col-sm-12 col-12">
                                    <div class="form-group" style="margin-bottom: 0rem;">
                                        <select class="form-control form-control-sm" id="category_report" name="category_report" required>
                                            <option value="">- Jenis Laporan -</option>
                                            <option value="1">Rekap Rapor</option>
                                            <option value="3">Rekap Nilai Akhir</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-12 col-sm-12 col-12" id="show_month">
                                    <select id="month" name="month" class="form-control form-control-sm">
                                        <option value="01" @if(date('m') == '01') selected @endif>Januari</option>
                                        <option value="02" @if(date('m') == '02') selected @endif>Februari</option>
                                        <option value="03" @if(date('m') == '03') selected @endif>Maret</option>
                                        <option value="04" @if(date('m') == '04') selected @endif>April</option>
                                        <option value="05" @if(date('m') == '05') selected @endif>Mei</option>
                                        <option value="06" @if(date('m') == '06') selected @endif>Juni</option>
                                        <option value="07" @if(date('m') == '07') selected @endif>Juli</option>
                                        <option value="08" @if(date('m') == '08') selected @endif>Agustus</option>
                                        <option value="09" @if(date('m') == '09') selected @endif>September</option>
                                        <option value="10" @if(date('m') == '10') selected @endif>Oktober</option>
                                        <option value="11" @if(date('m') == '11') selected @endif>November</option>
                                        <option value="12" @if(date('m') == '12') selected @endif>Desember</option>
                                    </select>
                                </div>

                                <div class="col-xl-3 col-md-12 col-sm-12 col-12" id="show_year">
                                    <select id="year" name="year" class="form-control form-control-sm">
                                        @for($i=2026;$i<=date('Y');$i++)
                                            <option value="{{ $i }}" @if(date('Y')==$i) selected @endif>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                
                                <div class="col-xl-10 col-md-12 col-sm-12 col-12" style="margin-top:20px">
                                    <button type="submit" id="excel" class="btn btn-info" data-placement="top" title="Cetak Daftar Pegawai" name="export" value="excel">Cetak</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        @endsection