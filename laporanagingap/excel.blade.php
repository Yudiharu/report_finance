<!DOCTYPE html>
<html lang="en">
<head>
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
	<style> 
        
     @page {
            border: solid 1px #0b93d5;

        }

        .title {
            margin-top: 0.5cm;
        }
        .title h1 {
            text-align: left;
            font-size: 14pt;
            
        }
        

        .header {
            margin-left: 50px;
            margin-right: 0px;
            /*font-size: 10pt;*/
            padding-top: 10px;
            /*border: solid 1px #0b93d5;*/
        }

        .left {
            float: left;
        }

        .right {
            float: right;
        }

        .clearfix {
            overflow: auto;
        }

        .content {
                margin-left: 10px;
            padding-top: 10px
        }
        .catatan {
            font-size: 10pt;
        }

        footer {
                position: fixed; 
                top: 19cm; 
                left: 0cm; 
                right: 0cm;
                height: 2cm;
            }

        /* Table desain*/
        table.grid {
            width: 100%;
        }
</style>
</head>
<body>

<table class="table_content" style="margin-bottom: 25px;width: 100%;">
    <thead>
        <tr class="border" style="background-color: #e6f2ff">
            <th>Vendor</th>
            <th>No Invoice</th>
            <th>No Seri</th>
            <th>Inv Vendor</th>
            <th>No Job</th>
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
                // $total_creditnote = $ar->total_creditnote;
                $grandtotal = $ar->gt_invoice - $ar->total_payment;
            }
        }
if ($vendor != null) {
    $cust2 = $vendor->kode_vendor;
    $customer2 = Vendor::find($cust2);
    if (stripos($customer2->nama_vendor, '&') !== FALSE) {
        $ket = stripos($customer2->nama_vendor, '&');
        $keterangan2 = substr_replace($customer2->nama_vendor, '&amp;', $ket, 1);
    }else {
        $keterangan2 = $customer2->nama_vendor;
    }
?>
    <tr class="border">
        <?php if ($cust1 == null) { ?>
                <?php 
                    $cust1 = $vendor->kode_vendor; 
                    $customer1 = Vendor::find($cust1);
                    if (stripos($customer1->nama_vendor, '&') !== FALSE) {
                        $ket = stripos($customer1->nama_vendor, '&');
                        $keterangan1 = substr_replace($customer1->nama_vendor, '&amp;', $ket, 1);
                    }else {
                        $keterangan1 = $customer1->nama_vendor;
                    }
                ?>
                <td><?php echo $keterangan1 ?></td>
                <td><?php echo $no_invoice ?></td>
                <td><?php echo $no_seri ?></td>
            <?php
                if (stripos($invoice_vendor, '&') !== FALSE) {
                    $ket = stripos($invoice_vendor, '&');
                    $keterangan3 = substr_replace($invoice_vendor, '&amp;', $ket, 1);
                }else {
                    $keterangan3 = $invoice_vendor;
                }
            ?>
                <td><?php echo $keterangan3 ?></td>
                <td><?php echo $no_jo ?></td>
                <td style="text-align: right"><?php echo format_tgl2($tgl_invoice) ?></td>
                <td style="text-align: right"><?php echo $umur ?></td>
                <td style="text-align: right"><?php echo $nilai ?></td>
                <?php if ($umur <= 30) { ?>
                    <?php $periode1 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo $grandtotal ?></td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                <?php }else if ($umur > 30 && $umur <= 60) { ?>
                    <?php $periode2 += $grandtotal; ?>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right"><?php echo $grandtotal ?></td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                <?php }else if ($umur > 60 && $umur <=90) { ?>
                    <?php $periode3 += $grandtotal; ?>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right"><?php echo $grandtotal ?></td>
                    <td style="text-align: right">0</td>
                <?php }else if ($umur > 90) { ?>
                    <?php $periode4 += $grandtotal; ?>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right"><?php echo $grandtotal ?></td>
                <?php } ?>
                <td style="text-align: right"><?php echo $grandtotal ?></td>
            <?php }else if ($cust1 == $cust2) { ?>
                <td></td>
                <td><?php echo $no_invoice ?></td>
                <td><?php echo $no_seri ?></td>
                <?php
                if (stripos($invoice_vendor, '&') !== FALSE) {
                    $ket = stripos($invoice_vendor, '&');
                    $keterangan3 = substr_replace($invoice_vendor, '&amp;', $ket, 1);
                }else {
                    $keterangan3 = $invoice_vendor;
                }
            ?>
                <td><?php echo $keterangan3 ?></td>
                <td><?php echo $no_jo ?></td>
                <td style="text-align: right"><?php echo format_tgl2($tgl_invoice) ?></td>
                <td style="text-align: right"><?php echo $umur ?></td>
                <td style="text-align: right"><?php echo $nilai ?></td>
                <?php if ($umur <= 30) { ?>
                    <?php $periode1 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo $grandtotal ?></td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                <?php }else if ($umur > 30 && $umur <= 60) { ?>
                    <?php $periode2 += $grandtotal; ?>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right"><?php echo $grandtotal ?></td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                <?php }else if ($umur > 60 && $umur <=90) { ?>
                    <?php $periode3 += $grandtotal; ?>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right"><?php echo $grandtotal ?></td>
                    <td style="text-align: right">0</td>
                <?php }else if ($umur > 90) { ?>
                    <?php $periode4 += $grandtotal; ?>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right"><?php echo $grandtotal ?></td>
                <?php } ?>
                <td style="text-align: right"><?php echo $grandtotal ?></td>
            <?php }else { ?>
                <?php 
                    $cust1 = $vendor->kode_vendor; 
                    $customer1 = Vendor::find($cust1);
                    if (stripos($customer1->nama_vendor, '&') !== FALSE) {
                        $ket = stripos($customer1->nama_vendor, '&');
                        $keterangan1 = substr_replace($customer1->nama_vendor, '&amp;', $ket, 1);
                    }else {
                        $keterangan1 = $customer1->nama_vendor;
                    }
                ?>
                <td><?php echo $keterangan2 ?></td>
                <td><?php echo $no_invoice ?></td>
                <td><?php echo $no_seri ?></td>
                <?php
                if (stripos($invoice_vendor, '&') !== FALSE) {
                    $ket = stripos($invoice_vendor, '&');
                    $keterangan3 = substr_replace($invoice_vendor, '&amp;', $ket, 1);
                }else {
                    $keterangan3 = $invoice_vendor;
                }
            ?>
                <td><?php echo $keterangan3 ?></td>
                <td><?php echo $no_jo ?></td>
                <td style="text-align: right"><?php echo format_tgl2($tgl_invoice) ?></td>
                <td style="text-align: right"><?php echo $umur ?></td>
                <td style="text-align: right"><?php echo $nilai ?></td>
                <?php if ($umur <= 30) { ?>
                    <?php $periode1 += $grandtotal; ?>
                    <td style="text-align: right"><?php echo $grandtotal ?></td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                <?php }else if ($umur > 30 && $umur <= 60) { ?>
                    <?php $periode2 += $grandtotal; ?>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right"><?php echo $grandtotal ?></td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                <?php }else if ($umur > 60 && $umur <=90) { ?>
                    <?php $periode3 += $grandtotal; ?>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right"><?php echo $grandtotal ?></td>
                    <td style="text-align: right">0</td>
                <?php }else if ($umur > 90) { ?>
                    <?php $periode4 += $grandtotal; ?>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right"><?php echo $grandtotal ?></td>
                <?php } ?>
                <td style="text-align: right"><?php echo $grandtotal ?></td>
            <?php } ?>
            <?php 
                $total1 += $nilai;
                $grand += $grandtotal;
            ?>
            </tr>
        <?php } ?>
    <?php } ?>
</tbody>
</table>
</body>
</html>