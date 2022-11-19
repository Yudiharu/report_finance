<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Models\tb_akhir_bulan;
use App\Models\MasterLokasi;
use App\Models\Company;
use App\Models\Vendor;
use App\Models\ApBalance;
use App\Models\Tb_ap_history;
use App\Exports\AgingapExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use DB;
use Carbon;

class LaporanAgingapController extends Controller
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

    public function index()
    {
        $konek = self::konek();
        $create_url = route('laporanagingar.create');

        $Vendor = Vendor::pluck('nama_vendor','id');

        $tgl_jalan = tb_akhir_bulan::on($konek)->where('reopen_status','true')->orwhere('status_periode','Open')->first();
        $tgl_jalan2 = $tgl_jalan->periode;
        $period = Carbon\Carbon::parse($tgl_jalan2)->format('F Y');
        $get_lokasi = MasterLokasi::where('kode_lokasi',auth()->user()->kode_lokasi)->first();
        $nama_lokasi = $get_lokasi->nama_lokasi;

        return view('admin.laporanagingap.index',compact('create_url','period','nama_lokasi','Vendor'));
    }

    public function exportPDF(){
        $konek = self::konek();
        $dt = Carbon\Carbon::now();
        $date=date_create($dt);

        $tanggal_awal = $_GET['tanggal_awal'];
        $tanggal_akhir = $_GET['tanggal_akhir'];
        $getven = $_GET['vendor'];

        $tipe = $_GET['jenis_report'];
        if(isset($_GET['ttd'])){
            $format_ttd = $_GET['ttd']; 
        }else{
            $format_ttd = 0;
        }
        
        $rekap = $_GET['rekap'];
    
        $ttd = auth()->user()->name;
        $level = auth()->user()->level;
        $get_lokasi = auth()->user()->kode_lokasi;
        $get_company = auth()->user()->kode_company;

        $nama_lokasi = MasterLokasi::find($get_lokasi);
        $nama = $nama_lokasi->nama_lokasi;

        $nama_company = Company::find($get_company);
        $nama2 = $nama_company->nama_company;

        if($getven != 'SEMUA'){
            if ($tipe == 'PDF') {
                $arhistory = Tb_ap_history::on($konek)->where('kode_vendor',$getven)->whereBetween('tanggal_transaksi', array($tanggal_awal, $tanggal_akhir))->where('transaction_type','Invoice')->get();

                $tgl_jalan = tb_akhir_bulan::on($konek)->where('reopen_status','true')->orwhere('status_periode','Open')->first();
                $tgl_jalan2 = $tgl_jalan->periode;
                $hari = Carbon\Carbon::parse($tanggal_awal)->format('d');
                $bulan = Carbon\Carbon::parse($tanggal_awal)->format('m');
                $tahun = Carbon\Carbon::parse($tanggal_awal)->format('Y');

                if ($rekap == 'Detail') {
                    $pdf = PDF::loadView('/admin/laporanagingap/pdf', compact('arhistory','tanggal_awal','tanggal_akhir','date','ttd','nama','nama2','konek','dt', 'format_ttd', 'get_company','rekap','getven'));
                }else {
                    $pdf = PDF::loadView('/admin/laporanagingap/pdf2', compact('arhistory','tanggal_awal','tanggal_akhir','date','ttd','nama','nama2','konek','dt', 'format_ttd', 'get_company','rekap','getven'));
                }
                
                $pdf->setPaper('a4', 'landscape');

                return $pdf->stream('Laporan Aging AP Dari Tanggal '.$tanggal_awal.' s/d '.$tanggal_akhir.'.pdf');
            }else if ($tipe == 'excel') {
                return Excel::download(new AgingapExport($tanggal_awal, $tanggal_akhir, $getven), 'Laporan Aging AP dari tanggal '.$tanggal_awal.' sd '.$tanggal_akhir.'.xlsx');
            }
        }else{
            if ($tipe == 'PDF') {
                $arhistory = Tb_ap_history::on($konek)->select('kode_vendor','no_transaksi')->whereBetween('tanggal_transaksi', array($tanggal_awal, $tanggal_akhir))->groupBy('kode_vendor','no_transaksi')->get();

                $tgl_jalan = tb_akhir_bulan::on($konek)->where('reopen_status','true')->orwhere('status_periode','Open')->first();
                $tgl_jalan2 = $tgl_jalan->periode;
                $hari = Carbon\Carbon::parse($tanggal_awal)->format('d');
                $bulan = Carbon\Carbon::parse($tanggal_awal)->format('m');
                $tahun = Carbon\Carbon::parse($tanggal_awal)->format('Y');

                if ($rekap == 'Detail') {
                    $pdf = PDF::loadView('/admin/laporanagingap/pdf', compact('arhistory','tanggal_awal','tanggal_akhir','date','ttd','nama','nama2','konek','dt', 'format_ttd', 'get_company','rekap','getven'));
                }else {
                    $pdf = PDF::loadView('/admin/laporanagingap/pdf2', compact('arhistory','tanggal_awal','tanggal_akhir','date','ttd','nama','nama2','konek','dt', 'format_ttd', 'get_company','rekap','getven'));
                }
                
                $pdf->setPaper('a4', 'landscape');

                return $pdf->stream('Laporan Aging AP Dari Tanggal '.$tanggal_awal.' s/d '.$tanggal_akhir.'.pdf');
            }else if ($tipe == 'excel') {
                return Excel::download(new AgingapExport($tanggal_awal, $tanggal_akhir, $getven), 'Laporan Aging AP dari tanggal '.$tanggal_awal.' sd '.$tanggal_akhir.'.xlsx');
            }
        }
    }
}
