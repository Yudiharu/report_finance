<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'fungsi/date.php'?>
<?php use App\Models\tb_cashbank_history; ?>
<?php use App\Models\CashbankTransfer; ?>
<?php use App\Models\CashbankoutDetail; ?>
<?php use App\Models\CashbankinDetail; ?>
<?php use App\Models\Invoicearpbm; ?>
<?php use App\Models\Invoicearsub; ?>
<?php use App\Models\Invoiceargut; ?>
<?php use App\Models\Invoicear; ?>
<?php use App\Models\Invoiceap; ?>
<?php use App\Models\InvoicearDetail; ?>
<?php use App\Models\Invoicearum; ?>
<?php use App\Models\Invoicearumgut; ?>
<?php use App\Models\Invoicearumsub; ?>
<?php use App\Models\Invoicearumpbm; ?>
<?php use App\Models\InvoicearumDetail; ?>
<?php use App\Models\InvoicearumgutDetail; ?>
<?php use App\Models\InvoicearumsubDetail; ?>
<?php use App\Models\InvoicearumpbmDetail; ?>
<?php use App\Models\InvoicearumpaymentgutDetail; ?>
<?php use App\Models\InvoicearumpaymentDetail; ?>
<?php use App\Models\InvoicearumpaymentsubDetail; ?>
<?php use App\Models\InvoicearpaymentpbmDetail; ?>
<?php use App\Models\InvoicearumpaymentpbmDetail; ?>
<?php use App\Models\ReturPenjualan; ?>
<?php use App\Models\ReturPenjualanDetail; ?>
<?php use App\Models\ReturPenjualanPayment; ?>
<?php use App\Models\DebitNoteDetail; ?>
<?php use App\Models\InvoiceapDetail; ?>
<?php use App\Models\Debitnote; ?>
<?php use App\Models\Customer; ?>
<?php use App\Models\Customergut; ?>
<?php use App\Models\Customersub; ?>
<?php use App\Models\Customerpbm; ?>
<?php use App\Models\Customerguis; ?>
<?php use App\Models\Vendor; ?>
<?php libxml_use_internal_errors($internalErrors); ?>
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
            <th class="border" width="">Tanggal</th>
            <th class="border" width="">No Transaksi</th>
            <th class="border" width="">Description</th>
            <th class="border" width="">Debit</th>
            <th class="border" width="">Kredit</th>
            <th class="border" width="">Saldo Akhir</th>
        </tr>
    </thead>

<?php
    $debet = 0;
    $kredit = 0;
?>

    <tbody>
            <tr>
                <td>00/00/0000</td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td>SALDO AWAL</td>
                <?php if ($saldo_awal != null){ ?>
                    <td>0</td>
                    <td>0</td>
                    <?php if ($bulan == '1') { ?>
                        <td><?php echo $saldo_awal->ending_balance ?></td>
                    <?php } else { ?>
                        <td><?php echo $saldo_awal->ending_balance ?></td>
                    <?php } ?>
                <?php } else{ ?>
                    <td>0</td>
                    <td>0</td>
                    <?php if ($bulan == '1') { ?>
                        <td><?php echo $saldo_awal3->beginning_balance ?></td>
                    <?php }else { ?>
                        <td><?php echo $saldo_awal2->beginning_balance ?></td>
                    <?php } ?>
                <?php } ?>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td>SALDO TO DATE</td>
                <td><?php echo $saldo_debet ?></td>
                <td><?php echo $saldo_kredit ?></td>
                <?php if ($bulan == '01') { ?>
                    <?php if ($saldo_awal2 != null){ 
                        if ($saldo_debet_flex == null){
                            $saldo_debet_flex = 0;
                        }
                        
                        if ($saldo_kredit_flex == null){
                            $saldo_kredit_flex = 0;
                        }
                        
                        if ($saldo_awal == null){
                            $saldo_awal = 0;
                        }else {
                            $saldo_awal = $saldo_awal->ending_balance;
                        }
                        // $saldo_akhir_awal = $saldo_debet - $saldo_kredit + $saldo_awal2->beginning_balance;
                        $saldo_akhir_awal = $saldo_debet_flex - $saldo_kredit_flex + $saldo_awal;
                    } else{ 
                        $saldo_akhir_awal = 0;
                    } ?>
                    <td><?php echo $saldo_akhir_awal ?></td>
                <?php } else { ?>
                    <?php if ($saldo_awal != null){ 
                        // $saldo_akhir_awal = $saldo_debet - $saldo_kredit + $saldo_awal->ending_balance; 
                        $saldo_akhir_awal = $saldo_debet_flex - $saldo_kredit_flex + $saldo_awal->ending_balance;
                    } else{ 
                    if ($saldo_awal3 != null){
                        $saldo_akhir_awal = $saldo_debet_flex - $saldo_kredit_flex + $saldo_awal3->beginning_balance;
                    }else {
                        $saldo_akhir_awal = 0;
                    }
                } ?>
                    <td><?php echo $saldo_akhir_awal ?></td>
                <?php } ?>
            </tr>
            <?php $saldo_akhir1 = $saldo_akhir_awal; ?>
            @foreach($cashbankbalance as $key => $row)
            <?php 
                $cbs = tb_cashbank_history::on($konek)->where('tanggal_transaksi', $row->tanggal_transaksi)->where('kode_cashbank', $row->kode_cashbank)->first(); 
                if ($get_company == '0401') {
                    $nomor = substr($row->no_transaksi, 4,2);
                }else {
                    $nomor = substr($row->no_transaksi, 2,2);
                }
            ?>
            <?php if (stripos($row->no_transaksi, 'CBO') !== FALSE) { ?>
                <?php 
                    $cekdetail = CashbankoutDetail::on($konek)->select('cashbankout_detail.*','tb_cashbank_history.*')->join('tb_cashbank_history','cashbankout_detail.no_cashbank_out','=','tb_cashbank_history.no_transaksi')->where('no_cashbank_out', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get();
                ?>
                @foreach($cekdetail as $key => $rowcbo)
                <tr>
                    <td><?php echo format_tgl2($row->tanggal_transaksi) ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if (stripos($rowcbo->keterangan, '&') !== FALSE ) { 
                            $ket = stripos($rowcbo->keterangan, '&');
                            $keterangan = substr_replace($rowcbo->keterangan, '&amp;', $ket, 1);
                            if (stripos($keterangan, '&') !== FALSE) {
                                $ket = stripos($keterangan, '&');
                                $keterangan = substr_replace($keterangan, '&amp;', $ket, 1);
                            }else {
                                $keterangan = substr_replace($rowcbo->keterangan, '&amp;', $ket, 1);
                            }
                        } else {
                            $keterangan = $rowcbo->keterangan;
                        }
                    ?>
                    <td><?php echo $keterangan ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowcbo->sub_total; ?>
                        <td><?php echo $rowcbo->sub_total ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowcbo->sub_total; ?>
                        <td>0</td>
                        <td><?php echo $rowcbo->sub_total ?></td>
                    <?php } ?>
                    <td><?php echo $saldo_akhir = $saldo_akhir1 - $kredit + $debet ?></td>
                </tr>
                @endforeach
            <?php } else if (stripos($row->no_transaksi, 'CBI') !== FALSE) { ?>
                <?php 
                    $cekdetail = CashbankinDetail::on($konek)->select('cashbankin_detail.*','tb_cashbank_history.*')->join('tb_cashbank_history','cashbankin_detail.no_cashbank_in','=','tb_cashbank_history.no_transaksi')->where('no_cashbank_in', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get();
                ?>
                @foreach($cekdetail as $key => $rowcbo)
                <tr>
                    <td><?php echo format_tgl2($row->tanggal_transaksi) ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if (stripos($rowcbo->keterangan, '&') !== FALSE ) { 
                            $ket = stripos($rowcbo->keterangan, '&');
                            $keterangan = substr_replace($rowcbo->keterangan, '&amp;', $ket, 1);
                            if (stripos($keterangan, '&') !== FALSE) {
                                $ket = stripos($keterangan, '&');
                                $keterangan = substr_replace($keterangan, '&amp;', $ket, 1);
                            }
                        } else {
                            $keterangan = $rowcbo->keterangan;
                        }
                    ?>
                    <td><?php echo $keterangan ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowcbo->sub_total; ?>
                        <td><?php echo $rowcbo->sub_total ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowcbo->sub_total; ?>
                        <td>0</td>
                        <td><?php echo $rowcbo->sub_total ?></td>
                    <?php } ?>
                    <td><?php echo $saldo_akhir = $saldo_akhir1 - $kredit + $debet ?></td>
                </tr>
                @endforeach
            <?php } else if (stripos($row->no_transaksi, 'AR') !== FALSE) { ?>
                <?php 
                    $cekdetail = Invoicear::on($konek)->select('invoice_ar.*','tb_cashbank_history.*')->join('tb_cashbank_history','invoice_ar.no_invoice','=','tb_cashbank_history.no_transaksi')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get();
                ?>
                @foreach($cekdetail as $key => $rowar)
                <tr>
                    <td><?php echo format_tgl2($row->tanggal_transaksi) ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' && $get_company == '0401') {
                            $namacust = Customergut::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                        }else {
                            $namacust = Customer::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                        }
                    ?>
                    <td><?php echo $keterangan ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo $rowar->harga_transaksi ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo $rowar->harga_transaksi ?></td>
                    <?php } ?>
                    <td><?php echo $saldo_akhir = $saldo_akhir1 - $kredit + $debet ?></td>
                </tr>
                @endforeach
            <?php } else if (stripos($row->no_transaksi, 'IUM') !== FALSE) { ?>
                <?php 
                    if ($get_company == '04' || $get_company == '0401') {
                        $cekdetail = InvoicearumpaymentgutDetail::on($konek)->select('invoicearumpaymentgut_detail.*','tb_cashbank_history.*')->join('tb_cashbank_history','invoicearumpaymentgut_detail.no_journal','=','tb_cashbank_history.no_journal')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get();
                    }else if ($get_company == '03') {
                        $cekdetail = InvoicearumpaymentDetail::on($konek)->select('invoicearumpayment_detail.*','tb_cashbank_history.*')->join('tb_cashbank_history','invoicearumpayment_detail.no_journal','=','tb_cashbank_history.no_journal')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get();
                    }else if ($get_company == '02' || $get_company == '06') {
                        $cekdetail = InvoicearumpaymentpbmDetail::on($konek)->select('invoicearumpaymentpbm_detail.*','tb_cashbank_history.*')->join('tb_cashbank_history','invoicearumpaymentpbm_detail.no_journal','=','tb_cashbank_history.no_journal')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get();
                    }else if ($get_company == '05') {
                        $cekdetail = InvoicearumpaymentsubDetail::on($konek)->select('invoicearumpaymentsub_detail.*','tb_cashbank_history.*')->join('tb_cashbank_history','invoicearumpaymentsub_detail.no_journal','=','tb_cashbank_history.no_journal')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get();
                    }
                ?>
                @foreach($cekdetail as $key => $rowar)
                <?php
                    if ($get_company == '04' || $get_company == '0401') {
                        $namas = Invoicearumgut::on($konek)->where('no_invoice', $rowar->no_invoice)->first();
                        $nama = Customergut::where('id', $namas->kode_customer)->first();
                    }else if ($get_company == '03') {
                        $namas = Invoicearum::on($konek)->where('no_invoice', $rowar->no_invoice)->first();
                        $nama = Customer::where('id', $namas->kode_customer)->first();
                    }else if ($get_company == '05') {
                        $namas = Invoicearumsub::on($konek)->where('no_invoice', $rowar->no_invoice)->first();
                        $nama = Customersub::where('id', $namas->kode_customer)->first();
                    }else if ($get_company == '02' || $get_company =='06') {
                        $namas = Invoicearumpbm::on($konek)->where('no_invoice', $rowar->no_invoice)->first();
                        $nama = Customerpbm::on($konek2)->where('id', $namas->kode_customer)->first();
                    }
                    ?>
                <tr>
                    <td><?php echo format_tgl2($row->tanggal_transaksi) ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        // if (stripos($rowar->keterangan, '&') !== FALSE ) { 
                        //     $ket = stripos($rowar->keterangan, '&');
                        //     $keterangan = substr_replace($rowar->keterangan, '&amp;', $ket, 1);
                        //     if (stripos($keterangan, '&') !== FALSE) {
                        //         $ket = stripos($keterangan, '&');
                        //         $keterangan = substr_replace($keterangan, '&amp;', $ket, 1);
                        //     }
                        // } else {
                        //     $keterangan = $rowar->keterangan;
                        // }
                    ?>
                    <td><?php echo $nama->nama_customer ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo $rowar->harga_transaksi ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo $rowar->harga_transaksi ?></td>
                    <?php } ?>
                    <td><?php echo $saldo_akhir = $saldo_akhir1 - $kredit + $debet ?></td>
                </tr>
                @endforeach
            <?php } else if ($nomor == 'DN') { ?>
                <?php 
                    $cekdetail = DebitNote::on($konek)->select('debit_note.*','tb_cashbank_history.*')->join('tb_cashbank_history','debit_note.no_invoice','=','tb_cashbank_history.no_transaksi')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get();
                ?>
                @foreach($cekdetail as $key => $rowar)
                <tr>
                    <td><?php echo format_tgl2($row->tanggal_transaksi) ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' || $get_company == '0401') {
                            $namacust = Customergut::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                        }else if ($get_company == '05') {
                            $namacust = Customersub::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                        }else if ($get_company == '03') {
                            $namacust = Customer::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                        }else if ($get_company == '02' || $get_company == '06') {
                            $namacust = Customerpbm::on($konek2)->where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                        }
                    ?>
                    <td><?php echo $keterangan ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo $rowar->harga_transaksi ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo $rowar->harga_transaksi ?></td>
                    <?php } ?>
                    <td><?php echo $saldo_akhir = $saldo_akhir1 - $kredit + $debet ?></td>
                </tr>
                @endforeach
            <?php } else if (stripos($row->no_transaksi, 'AP') !== FALSE) { ?>
                <?php 
                    $cekdetailx = Invoiceap::on($konek)->select('invoice_ap.*','tb_cashbank_history.*')->join('tb_cashbank_history','invoice_ap.no_invoice','=','tb_cashbank_history.no_transaksi')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get(); 
                    $cekdetail = Invoiceap::on($konek)->where('no_invoice', $row->no_transaksi)->first();
                ?>
                
                <tr>
                    <td><?php echo format_tgl2($row->tanggal_transaksi) ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                            $namacust = Vendor::where('id', $cekdetail->kode_vendor)->first();
                            $ket_nama = $namacust->nama_vendor;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                    ?>
                    <td><?php echo $keterangan ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo $row->harga_transaksi ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo $row->harga_transaksi ?></td>
                    <?php } ?>
                    <td><?php echo $saldo_akhir = $saldo_akhir1 - $kredit + $debet ?></td>
                </tr>
            <?php } else if (stripos($row->no_transaksi, 'ISP') !== FALSE || stripos($row->no_transaksi, 'IHE') !== FALSE || stripos($row->no_transaksi, 'ISC') !== FALSE ) { ?>
                <?php 
                    $cekdetailx = Invoiceargut::on($konek)->select('invoicear_gut.*','tb_cashbank_history.*')->join('tb_cashbank_history','invoicear_gut.no_invoice','=','tb_cashbank_history.no_transaksi')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get();
                    $cekdetail = Invoiceargut::on($konek)->where('no_invoice', $row->no_transaksi)->first();
                ?>
                <tr>
                    <td><?php echo format_tgl2($row->tanggal_transaksi) ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' || $get_company == '0401') {
                            $namacust = Customergut::where('id', $cekdetail->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                        }else {
                            $namacust = Customer::where('id', $cekdetail->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                        }
                    ?>
                    <td><?php echo $keterangan ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo $row->harga_transaksi ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo $row->harga_transaksi ?></td>
                    <?php } ?>
                    <td><?php echo $saldo_akhir = $saldo_akhir1 - $kredit + $debet ?></td>
                </tr>
            <?php } else if (stripos($row->no_transaksi, 'NRJ') !== FALSE) { ?>
                <?php $cekdetail = ReturPenjualan::on($konek)->where('no_retur_jual', $row->no_transaksi)->first(); ?>
                <tr>
                    <td><?php echo format_tgl2($row->tanggal_transaksi) ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' || $get_company == '0401') {
                            $namacust = Customergut::where('id', $cekdetail->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                        }else {
                            $namacust = Customer::where('id', $cekdetail->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                        }
                    ?>
                    <td><?php echo $keterangan ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo $row->harga_transaksi ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo $row->harga_transaksi ?></td>
                    <?php } ?>
                    <td><?php echo $saldo_akhir = $saldo_akhir1 - $kredit + $debet ?></td>
                </tr>
            <?php } else if (stripos($row->no_transaksi, 'IST') !== FALSE || stripos($row->no_transaksi, 'IFT') !== FALSE) { ?>
                <?php 
                    $cekdetail = Invoicearsub::on($konek)->where('no_invoice', $row->no_transaksi)->first();
                ?>
                
                <tr>
                    <td><?php echo format_tgl2($row->tanggal_transaksi) ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                            $namacust = Customersub::where('id', $cekdetail->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                    ?>
                    <td><?php echo $keterangan ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo $row->harga_transaksi ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo $row->harga_transaksi ?></td>
                    <?php } ?>
                    <td><?php echo $saldo_akhir = $saldo_akhir1 - $kredit + $debet ?></td>
                </tr>
            <?php } else if (stripos($row->no_transaksi, 'INV') !== FALSE) { ?>
                <?php 
                    $cekdetail = Invoicearpbm::on($konek)->where('no_invoice', $row->no_transaksi)->first();
                ?>
                
                <tr>
                    <td><?php echo format_tgl2($row->tanggal_transaksi) ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                            $namacust = Customerpbm::on($konek2)->where('id', $cekdetail->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                            if (stripos($ket_nama, '&') !== FALSE ) { 
                                $ket = stripos($ket_nama, '&');
                                $keterangan = substr_replace($ket_nama, '&amp;', $ket, 1);
                            } else {
                                $keterangan = $ket_nama;
                            }
                    ?>
                    <td><?php echo $keterangan ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo $row->harga_transaksi ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo $row->harga_transaksi ?></td>
                    <?php } ?>
                    <td><?php echo $saldo_akhir = $saldo_akhir1 - $kredit + $debet ?></td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td><?php echo format_tgl2($row->tanggal_transaksi) ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php if (stripos($row->no_transaksi, 'CBT') !== FALSE) { ?>
                        <?php $ketcbt = CashbankTransfer::on($konek)->where('no_cbt', $row->no_transaksi)->first(); ?>
                        <?php 
                            if (stripos($ketcbt->ket_dari, '&') !== FALSE ) { 
                                $ket = stripos($ketcbt->ket_dari, '&');
                                $keterangan = substr_replace($ketcbt->ket_dari, '&amp;', $ket, 1);
                                if (stripos($keterangan, '&') !== FALSE) {
                                    $ket = stripos($keterangan, '&');
                                    $keterangan = substr_replace($keterangan, '&amp;', $ket, 1);
                                }
                            } else {
                                $keterangan = $ketcbt->ket_dari;
                            }
                        ?>
                        <td>Ket.Dari/Tujuan:&nbsp;<?php echo $keterangan ?></td>
                    <?php } else { ?>
                        <td></td>
                    <?php } ?>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo $row->harga_transaksi ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo $row->harga_transaksi ?></td>
                    <?php } ?>
                    <td><?php echo $saldo_akhir = $saldo_akhir1 - $kredit + $debet ?></td>
                </tr>
            <?php } ?>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #F5D2D2">
                <td></td>
                <td></td>
                <td><b>Grand Total:</b></td>
                <td><b><?php echo $debet + $saldo_debet ?></b></td>
                <td><b><?php echo $kredit + $saldo_kredit ?></b></td>
                <td></td>
            </tr>
        </tfoot>
</table>
<hr>
</body>
</html>