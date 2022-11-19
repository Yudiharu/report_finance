<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
use App\Models\InvoiceapDetail;
use App\Models\Invoiceap;
use App\Models\InvoiceappaymentDetail;
use App\Models\Debitnote;
use App\Models\Tb_ap_history;
use App\Models\Vendor;
use \DateTime as DT;
include 'fungsi/date.php'
?>
    <title>LAPORAN AGING AP</title>
    <style>
        .header, h1 {
            font-size: 11pt;
            margin-bottom: 0px;
        }

        .header, p {
            font-size: 10pt;
            margin-top: 0px;
        }
        .table_content {
            color: #232323;
            border-collapse: collapse;
            font-size: 8pt;
            margin-top: 15px;
        }

        .table_content, .border {
            border: 1px solid black;
            padding: 4px;
        }
        .table_content, thead, th {
            padding: 7px;
            text-align: center;

        }
        ul li {
            display:inline;
            list-style-type:none;
        }

        table.grid1 {
          font-family: sans-serif;
          border-collapse: collapse;
          width: 100%;
        }

        table.grid1 td, table.grid1 th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 4px;
        }

        table.grid1 tr:nth-child(even) {
          background-color: #dddddd;
        }

        body{        
            padding-top: 120px;
            font-family: sans-serif;
        }
        .fixed-header, .fixed-footer{
            width: 100%;
            position: fixed;       
            padding: 10px 0;
            text-align: center;
        }
        .fixed-header{
            top: 0;
        }
        .fixed-footer{
            bottom: 0;
        }

        #header .page:after {
          content: counter(page, decimal);
        }

        .page_break { page-break-after: always; }
    </style>
</head>
<body>

<div class="fixed-header">
        <div style="float: left">
            <img src="{{ asset('css/logo_gui.png') }}" alt="" height="25px" width="25px" align="left">
            <p id="color" style="font-size: 8pt;" align="left"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ($nama2) ?></b><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lokasi: <?php echo ($nama) ?></p>
        </div>

        <div id="header">
            <p class="page" style="float: right; font-size: 9pt;"><b>Date :</b> <?php echo date_format($dt,"d/m/Y") ?>&nbsp;&nbsp;&nbsp;
            <b>Time :</b> <?php echo date_format($dt,"H:i:s") ?>&nbsp;&nbsp;&nbsp;
            <b>Page :</b> </p>
        </div>

        <br><br>
            <?php if($getven != 'SEMUA'){ 
                $vend = Vendor::find($getven); ?>

                <p><b>Report Aging AP Detail Pada <?php echo $vend->nama_vendor_po ?></b><br>
                Periode: <?php echo format_tgl($tanggal_awal) ?> s.d <?php echo format_tgl($tanggal_akhir) ?></p>
            <?php }else { ?>
                <p><b>Report Aging AP Detail</b><br>
                Periode: <?php echo format_tgl($tanggal_awal) ?> s.d <?php echo format_tgl($tanggal_akhir) ?></p>
            <?php } ?>
        <br>
</div>

<?php
$grandtotalqty = 0;
$grandtotaljumlah = 0;
?>
    <table class="grid1" style="margin-bottom: 25px;width: 100%; font-size: 9px">
        <thead>
        <tr style="background-color: #e6f2ff">
            <?php if($getven == 'SEMUA'){ ?>
                <th>Vendor</th>
            <?php } ?>
            <th>No Invoice</th>
            <th>No Seri</th>
            <th>Inv Vendor</th>
            <th>Remark</th>
            <th style="text-align: right">Tgl Invoice</th>
            <th style="text-align: right">Umur</th>
            <th style="text-align: right">Nilai Invoice</th>
            <th style="text-align: right">0 - 30 Hari</th>
            <th style="text-align: right">31 - 60 Hari</th>
            <th style="text-align: right">61 - 90 Hari</th>
            <th style="text-align: right">> 90 Hari</th>
            <th style="text-align: right">Grand Total</th>
        </tr>
        </thead>

<?php $cust1 = null; ?>
<?php $cust2 = null; ?>

<?php 
    $total1 = 0;
    $grand = 0;

    $periode1 = 0;
    $periode2 = 0;
    $periode3 = 0;
    $periode4 = 0;
    $grand1 = 0;
    $grand2 = 0;
    $grand3 = 0;
    $grand4 = 0;
    $grandfinal = 0;

    $data = array();
            
    foreach ($arhistory as $rowdata){
        $kode_vendor = $rowdata->kode_vendor;
        $no_transaksi = $rowdata->no_transaksi;
                            
        $data[] = array(
            'kode_vendor'=>$kode_vendor,
            'no_transaksi'=>$no_transaksi,
        );
    }

    $leng = count($arhistory);
?>
    <tbody>
<?php 
    for ($i = 0; $i < $leng; $i++) { 
        $vendor = Tb_ap_history::on($konek)->select('ap_history.*')
            ->join('invoice_ap','ap_history.no_transaksi','=','invoice_ap.no_invoice')
            ->where('ap_history.kode_vendor',$data[$i]['kode_vendor'])->where('no_transaksi',$data[$i]['no_transaksi'])->whereBetween('tanggal_transaksi', array($tanggal_awal, $tanggal_akhir))->where('invoice_ap.status','<>','CLOSED')->first();

        if(stripos($data[$i]['no_transaksi'], 'AP') !== FALSE){
            $ar = Invoiceap::on($konek)->with('vendor')->where('no_invoice', $data[$i]['no_transaksi'])->where('status','<>','CLOSED')->first();
            if($ar != null){
                $kode_customer = $ar->kode_vendor;
                $no_invoice = $ar->no_invoice;
                $no_seri = $ar->no_seri;
                $invoice_vendor = $ar->invoice_vendor;
                $no_jo = $ar->remark;
                $tgl_invoice = $ar->tgl_invoice;

                $sekarang = time();
                $diff   = $sekarang - strtotime($ar->tgl_invoice);
                $umur = floor($diff / (60 * 60 * 24));

                $nilai = $ar->gt_invoice;
                $grandtotal = $ar->gt_invoice - $ar->total_payment;
            }
        }

        if ($vendor != null) {
            $cust2 = $vendor->kode_vendor;
            $customer2 = Vendor::find($cust2);
        ?>

        <tr class="border">
            <?php if ($cust1 == null) { 
                $cust1 = $vendor->kode_vendor; 
                $customer1 = Vendor::find($cust1);    
            ?>
                <?php if($getven == 'SEMUA'){ ?>
                    <td><?php echo $customer1->nama_vendor_po ?></td>
                <?php } ?>
                <td><?php echo $no_invoice ?></td>
                <td><?php echo $no_seri ?></td>
                <td><?php echo $invoice_vendor ?></td>
                <td><?php echo $no_jo ?></td>
                <td style="text-align: right"><?php echo format_tgl($tgl_invoice) ?></td>
                <td style="text-align: right"><?php echo number_format($umur,'0',',','.') ?></td>
                <td style="text-align: right"><?php echo number_format($nilai,'2',',','.') ?></td>
                <?php if ($umur <= 30) { ?>
                    <?php $periode1 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                <?php }else if ($umur > 30 && $umur <= 60) { ?>
                    <?php $periode2 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                <?php }else if ($umur > 60 && $umur <=90) { ?>
                    <?php $periode3 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                <?php }else if ($umur > 90) { ?>
                    <?php $periode4 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
                <?php } ?>
                <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
            <?php }else if ($cust1 == $cust2) { ?>
                <?php if($getven == 'SEMUA'){ ?>
                    <td></td>
                <?php } ?>
                <td><?php echo $no_invoice ?></td>
                <td><?php echo $no_seri ?></td>
                <td><?php echo $invoice_vendor ?></td>
                <td><?php echo $no_jo ?></td>
                <td style="text-align: right"><?php echo format_tgl($tgl_invoice) ?></td>
                <td style="text-align: right"><?php echo number_format($umur,'0',',','.') ?></td>
                <td style="text-align: right"><?php echo number_format($nilai,'2',',','.') ?></td>
                <?php if ($umur <= 30) { ?>
                    <?php $periode1 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                <?php }else if ($umur > 30 && $umur <= 60) { ?>
                    <?php $periode2 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                <?php }else if ($umur > 60 && $umur <=90) { ?>
                    <?php $periode3 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                <?php }else if ($umur > 90) { ?>
                    <?php $periode4 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
                <?php } ?>
                <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
            <?php }else { ?>
                <?php 
                    $cust1 = $vendor->kode_vendor; 
                    $customer1 = Vendor::find($cust1);
                ?>
                <?php if($getven == 'SEMUA'){ ?>
                    <td><?php echo $customer2->nama_vendor_po ?></td>
                <?php } ?>
                <td><?php echo $no_invoice ?></td>
                <td><?php echo $no_seri ?></td>
                <td><?php echo $invoice_vendor ?></td>
                <td><?php echo $no_jo ?></td>
                <td style="text-align: right"><?php echo format_tgl($tgl_invoice) ?></td>
                <td style="text-align: right"><?php echo number_format($umur,'0',',','.') ?></td>
                <td style="text-align: right"><?php echo number_format($nilai,'2',',','.') ?></td>
                <?php if ($umur <= 30) { ?>
                    <?php $periode1 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                <?php }else if ($umur > 30 && $umur <= 60) { ?>
                    <?php $periode2 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                <?php }else if ($umur > 60 && $umur <=90) { ?>
                    <?php $periode3 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                <?php }else if ($umur > 90) { ?>
                    <?php $periode4 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format(0,'2',',','.') ?></td>
                    <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
                <?php } ?>
                <td style="text-align: right"><?php echo number_format($grandtotal,'2',',','.') ?></td>
            <?php } ?>
            <?php 
                $total1 += $nilai;
                $grand += $grandtotal;
            ?>
    </tr>
<?php } ?>
</tbody>
<?php 
    $j = $i + 1;
    if($j >= $leng){
        $j = 0;
    }

    if ($data[$i]['kode_vendor'] != $data[$j]['kode_vendor']) {
?>
    <tfoot>
    <?php if ($vendor != null) { ?>
        <tr style="background-color: #F5D2D2">
            <td colspan="8" style="font-weight: bold; text-align: right">Sub Total : </td>
            <td style="text-align: right"><?php echo number_format($periode1,'2',',','.') ?></td>
            <td style="text-align: right"><?php echo number_format($periode2,'2',',','.') ?></td>
            <td style="text-align: right"><?php echo number_format($periode3,'2',',','.') ?></td>
            <td style="text-align: right"><?php echo number_format($periode4,'2',',','.') ?></td>
            <td style="text-align: right"><?php echo number_format($grand,'2',',','.') ?></td>
        </tr>
    <?php } ?>
    </tfoot>
    <?php if($getven == 'SEMUA'){
                $grand1 += $periode1;
                $grand2 += $periode2;
                $grand3 += $periode3;
                $grand4 += $periode4;
                $grandfinal += $grand;

                $total1 = 0;
                $grand = 0;
                $periode1 = 0;
                $periode2 = 0;
                $periode3 = 0;
                $periode4 = 0;
            ?>
            <?php } ?>
        <?php } ?>
            <?php if($getven != 'SEMUA'){
                $grand1 += $periode1;
                $grand2 += $periode2;
                $grand3 += $periode3;
                $grand4 += $periode4;
                $grandfinal += $grand;

                $total1 = 0;
                $grand = 0;
                $periode1 = 0;
                $periode2 = 0;
                $periode3 = 0;
                $periode4 = 0;
            } ?>
    <?php } ?>
        <tfoot>
            <tr style="background-color: #50504F">
                <td colspan="13"></td>
            </tr>
            <tr style="background-color: #FCC54E">
                <?php if($getven == 'SEMUA'){ ?>
                    <td colspan="8" style="font-weight: bold; text-align: right">Grand Total : </td>
                <?php } else { ?>
                    <td colspan="7" style="font-weight: bold; text-align: right">Grand Total : </td>
                <?php } ?>
                <td style="text-align: right"><?php echo number_format($grand1,'2',',','.') ?></td>
                <td style="text-align: right"><?php echo number_format($grand2,'2',',','.') ?></td>
                <td style="text-align: right"><?php echo number_format($grand3,'2',',','.') ?></td>
                <td style="text-align: right"><?php echo number_format($grand4,'2',',','.') ?></td>
                <td style="text-align: right"><?php echo number_format($grandfinal,'2',',','.') ?></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>