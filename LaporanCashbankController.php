<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Models\tb_akhir_bulan;
use App\Models\tb_item_bulanan;
use App\Models\tb_produk_history;
use App\Models\MasterLokasi;
use App\Models\Company;
use App\Models\Cashbank;
use App\Models\CashbankBalance;
use App\Models\tb_cashbank_history;
use App\Exports\CashbankExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use DB;
use Carbon;

class LaporancashbankController extends Controller
{
    public function konek()
    {
        $compa = auth()->user()->kode_company;
        if ($compa == '01'){
            $koneksi = 'mysqldepo';
        }else if ($compa == '02'){
            $koneksi = 'mysqlpbm';
        }else if ($compa == '0401'){
            $koneksi = 'mysqlgutjkt';
        }else if ($compa == '03'){
            $koneksi = 'mysql';
        }else if ($compa == '04'){
            $koneksi = 'mysqlgut';
        }else if ($compa == '05'){
            $koneksi = 'mysqlsub';
        }else if ($compa == '06'){
            $koneksi = 'mysqlinf';
        }
        return $koneksi;
    }
    
    public function konek2()
    {
        $compa = auth()->user()->kode_company;
        if ($compa == '02') {
            $koneksi = 'mysql_front_pbm';
        }else if ($compa == '03') {
            $koneksi = 'mysql3';
        }else if ($compa == '04' || $compa == '0401') {
            $koneksi = '';
        }else if ($compa == '05') {
            $koneksi = 'mysql_front_sub';
        }else if ($compa == '06') {
            $koneksi = 'mysql_front_inf';
        }
        return $koneksi;
    }

    public function index()
    {
        $konek = self::konek();
        $create_url = route('laporancashbank.create');
        $Cashbank = Cashbank::on($konek)->pluck('nama_cashbank','kode_cashbank');

        $tgl_jalan = tb_akhir_bulan::on($konek)->where('reopen_status','true')->orwhere('status_periode','Open')->first();
        $tgl_jalan2 = $tgl_jalan->periode;
        $period = Carbon\Carbon::parse($tgl_jalan2)->format('F Y');
        $get_lokasi = MasterLokasi::where('kode_lokasi',auth()->user()->kode_lokasi)->first();
        $nama_lokasi = $get_lokasi->nama_lokasi;

        return view('admin.laporancashbank.index',compact('create_url','period','nama_lokasi','Cashbank'));

    }

    public function exportPDF(){
        $konek = self::konek();
        $konek2 = self::konek2();
        $tanggal_awal = $_GET['tanggal_awal'];
        $tanggal_akhir = $_GET['tanggal_akhir'];
        $tipe = $_GET['jenis_report'];
        $cashbank = $_GET['cashbank'];
        if(isset($_GET['ttd'])){
            $format_ttd = $_GET['ttd']; 
        }else{
            $format_ttd = 0;
        }
        
        $dt = Carbon\Carbon::now();
        $date=date_create($dt);
    
        $ttd = auth()->user()->name;
        $level = auth()->user()->level;
        $get_lokasi = auth()->user()->kode_lokasi;
        $get_company = auth()->user()->kode_company;

        $nama_lokasi = MasterLokasi::find($get_lokasi);
        $nama = $nama_lokasi->nama_lokasi;

        $nama_company = Company::find($get_company);
        $nama2 = $nama_company->nama_company;

        $nama_cashbank = Cashbank::on($konek)->find($cashbank);
        $nama3 = $nama_cashbank->nama_cashbank;
        ini_set('max_execution_time', '3600');
        
        if ($tipe == 'PDF') {
            $cashbankbalance = tb_cashbank_history::on($konek)->whereBetween('tanggal_transaksi', array($tanggal_awal, $tanggal_akhir))->where('kode_cashbank', $cashbank)->orderBy('tanggal_transaksi','asc')->orderBy('created_at','asc')->get();

            $tgl_jalan = tb_akhir_bulan::on($konek)->where('reopen_status','true')->orwhere('status_periode','Open')->first();
            $tgl_jalan2 = $tgl_jalan->periode;
            $hari = Carbon\Carbon::parse($tanggal_awal)->format('d');
            $bulan = Carbon\Carbon::parse($tanggal_awal)->format('m');
            $tahun = Carbon\Carbon::parse($tanggal_awal)->format('Y');

            if($bulan == '01'){
                $saldo_awal = CashbankBalance::on($konek)->where('kode_cashbank', $cashbank)->whereYear('periode', $tahun - 1)->whereMonth('periode', '12')->first();
                
                $saldo_awal2 = CashbankBalance::on($konek)->where('kode_cashbank', $cashbank)->whereYear('periode', $tahun)->orderBy('periode','desc')->first();
                $saldo_awal3 = CashbankBalance::on($konek)->where('kode_cashbank', $cashbank)->whereYear('periode', $tahun)->orderBy('periode','asc')->first();
                
                if ($saldo_awal3 == null){
                    $saldo_awal3 = CashbankBalance::on($konek)->where('kode_cashbank', $cashbank)->orderBy('periode','asc')->first();
                }
                
                $saldo_debet_flex = tb_cashbank_history::on($konek)->where('kode_cashbank', $cashbank)->whereYear('tanggal_transaksi', $tahun)->whereMonth('tanggal_transaksi', $bulan)->where('tanggal_transaksi','<',$tanggal_awal)->where('dbkr_type', 'D')->sum('harga_transaksi');
                $saldo_kredit_flex = tb_cashbank_history::on($konek)->where('kode_cashbank', $cashbank)->whereYear('tanggal_transaksi', $tahun)->whereMonth('tanggal_transaksi', $bulan)->where('tanggal_transaksi','<',$tanggal_awal)->where('dbkr_type', 'K')->sum('harga_transaksi');

                $saldo_debet = tb_cashbank_history::on($konek)->where('kode_cashbank', $cashbank)->whereYear('tanggal_transaksi', $tahun)->where('tanggal_transaksi','<',$tanggal_awal)->where('dbkr_type', 'D')->sum('harga_transaksi');
                $saldo_kredit = tb_cashbank_history::on($konek)->where('kode_cashbank', $cashbank)->whereYear('tanggal_transaksi', $tahun)->where('tanggal_transaksi','<',$tanggal_awal)->where('dbkr_type', 'K')->sum('harga_transaksi');
            }else{
                $saldo_awal = CashbankBalance::on($konek)->where('kode_cashbank', $cashbank)->whereYear('periode', floatval($tahun))->whereMonth('periode', $bulan-1)->first();
                $saldo_awal2 = CashbankBalance::on($konek)->where('kode_cashbank', $cashbank)->whereYear('periode', $tahun)->whereMonth('periode', $bulan)->first();
                $saldo_awal3 = CashbankBalance::on($konek)->where('kode_cashbank', $cashbank)->whereYear('periode', $tahun)->orderBy('periode','asc')->first();
                
                $saldo_debet_flex = tb_cashbank_history::on($konek)->where('kode_cashbank', $cashbank)->whereYear('tanggal_transaksi', $tahun)->whereMonth('tanggal_transaksi', $bulan)->where('tanggal_transaksi','<',$tanggal_awal)->where('dbkr_type', 'D')->sum('harga_transaksi');
                $saldo_kredit_flex = tb_cashbank_history::on($konek)->where('kode_cashbank', $cashbank)->whereYear('tanggal_transaksi', $tahun)->whereMonth('tanggal_transaksi', $bulan)->where('tanggal_transaksi','<',$tanggal_awal)->where('dbkr_type', 'K')->sum('harga_transaksi');

                $saldo_debet = tb_cashbank_history::on($konek)->where('kode_cashbank', $cashbank)->whereYear('tanggal_transaksi', $tahun)->whereMonth('tanggal_transaksi', $bulan)->where('tanggal_transaksi','<',$tanggal_awal)->where('dbkr_type', 'D')->sum('harga_transaksi');
                $saldo_kredit = tb_cashbank_history::on($konek)->where('kode_cashbank', $cashbank)->whereYear('tanggal_transaksi', $tahun)->whereMonth('tanggal_transaksi', $bulan)->where('tanggal_transaksi','<',$tanggal_awal)->where('dbkr_type', 'K')->sum('harga_transaksi');
            }
            
            $pdf = PDF::loadView('/admin/laporancashbank/pdf', compact('konek2','saldo_debet_flex','saldo_kredit_flex','cashbankbalance','saldo_awal','tanggal_awal','tanggal_akhir','date','ttd','nama','nama2','saldo_debet','saldo_kredit','cashbank','konek','dt','nama3','format_ttd','get_company','bulan','saldo_awal2','saldo_awal3'));
            $pdf->setPaper('a4', 'landscape');

            return $pdf->stream('Laporan CashBank Dari Tanggal '.$tanggal_awal.' s/d '.$tanggal_akhir.'.pdf');
        }else if ($tipe == 'excel') {
            return Excel::download(new CashbankExport($tanggal_awal, $tanggal_akhir, $tipe, $cashbank), 'Laporan Cash Bank dari tanggal '.$tanggal_awal.' sd '.$tanggal_akhir.'.xlsx');
        }

    }
}
