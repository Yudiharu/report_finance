<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<?php use App\Models\tb_cashbank_history; ?>
<?php use App\Models\CashbankTransfer; ?>
<?php use App\Models\CashbankoutDetail; ?>
<?php use App\Models\CashbankinDetail; ?>
<?php use App\Models\Invoiceargut; ?>
<?php use App\Models\Invoicear; ?>
<?php use App\Models\Invoiceap; ?>
<?php use App\Models\Invoicearum; ?>
<?php use App\Models\InvoicearDetail; ?>
<?php use App\Models\InvoicearumDetail; ?>
<?php use App\Models\InvoicearumgutDetail; ?>
<?php use App\Model\InvoicearumpaymentgutDetail; ?>
<?php use App\Models\InvoicearumpaymentDetail; ?>
<?php use App\Models\DebitNoteDetail; ?>
<?php use App\Models\InvoiceapDetail; ?>
<?php use App\Models\Debitnote; ?>
<?php use App\Models\Customer; ?>
<?php use App\Models\Customergut; ?>
<?php use App\Models\Vendor; ?>

    <title>LAPORAN CASHBANK BALANCE</title>
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
            padding-top: 150px;
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
            <p><b>LAPORAN KAS BANK</b><br>
            Periode: <?php echo ($tanggal_awal) ?> s.d <?php echo ($tanggal_akhir) ?></p>
        <br>

        <div style="float: left; font-size: 10pt;">
            <b>Kas Bank:&nbsp;</b> <?php echo $cashbank; ?>&nbsp; <?php echo $nama3; ?>
        </div>
</div>

<?php
$grandtotalqty = 0;
$grandtotaljumlah = 0;
?>
    <table class="grid1" style="margin-bottom: 25px;width: 100%; font-size: 11px">
        <thead>
        <tr style="background-color: #e6f2ff">
            <th>Tanggal</th>
            <th>No Transaksi</th>
            <th>Description</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>Saldo Akhir</th>
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
                <td>0</td>
                <td>0</td>
                <?php if ($saldo_awal != null){ ?>
                    <td><?php echo number_format($saldo_awal->beginning_balance,'2',',','.') ?></td>
                <?php } else{ ?>
                    <td>0</td>
                <?php } ?>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td>SALDO TO DATE</td>
                <td><?php echo number_format($saldo_debet,'2',',','.') ?></td>
                <td><?php echo number_format($saldo_kredit,'2',',','.') ?></td>
                <?php if ($saldo_awal != null){ 
                    $saldo_akhir_awal = $saldo_debet - $saldo_kredit + $saldo_awal->beginning_balance; 
                } else{ 
                    $saldo_akhir_awal = 0;
                } ?>
                <td><?php echo number_format($saldo_akhir_awal,'2',',','.') ?></td>
            </tr>
<?php 
$saldo_akhir1 = $saldo_akhir_awal; 
$tanggal1 = null;
$tanggal2 = null;
?>
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
                <?php $tanggal2 = $row->tanggal_transaksi; ?>
                <?php if ($tanggal1 == null) { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal1 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <td><?php echo $rowcbo->keterangan ?></td>
                        <?php if ($row->dbkr_type == 'D') { ?>
                            <?php $debet += $rowcbo->sub_total; ?>
                            <td><?php echo number_format($rowcbo->sub_total,'2',',','.') ?></td>
                            <td>0</td>
                        <?php } else if ($row->dbkr_type == 'K') { ?>
                            <?php $kredit += $rowcbo->sub_total; ?>
                            <td>0</td>
                            <td><?php echo number_format($rowcbo->sub_total,'2',',','.') ?></td>
                        <?php } ?>
                        <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                    <?php } else if ($tanggal1 == $tanggal2) { ?>
                        <td></td>
                        <td><?php echo $row->no_transaksi ?></td>
                        <td><?php echo $rowcbo->keterangan ?></td>
                        <?php if ($row->dbkr_type == 'D') { ?>
                            <?php $debet += $rowcbo->sub_total; ?>
                            <td><?php echo number_format($rowcbo->sub_total,'2',',','.') ?></td>
                            <td>0</td>
                        <?php } else if ($row->dbkr_type == 'K') { ?>
                            <?php $kredit += $rowcbo->sub_total; ?>
                            <td>0</td>
                            <td><?php echo number_format($rowcbo->sub_total,'2',',','.') ?></td>
                        <?php } ?>
                        <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                    <?php } else { ?>
                        <?php $tanggal1 = $row->tanggal_transaksi; ?>
                        <td><?php echo $tanggal2 ?></td>
                        <td><?php echo $row->no_transaksi ?></td>
                        <td><?php echo $rowcbo->keterangan ?></td>
                        <?php if ($row->dbkr_type == 'D') { ?>
                            <?php $debet += $rowcbo->sub_total; ?>
                            <td><?php echo number_format($rowcbo->sub_total,'2',',','.') ?></td>
                            <td>0</td>
                        <?php } else if ($row->dbkr_type == 'K') { ?>
                            <?php $kredit += $rowcbo->sub_total; ?>
                            <td>0</td>
                            <td><?php echo number_format($rowcbo->sub_total,'2',',','.') ?></td>
                        <?php } ?>
                        <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                    <?php } ?>
            </tr>
        @endforeach
    <?php } else if (stripos($row->no_transaksi, 'CBI') !== FALSE) { ?>
        <?php 
            $cekdetail = CashbankinDetail::on($konek)->select('cashbankin_detail.*','tb_cashbank_history.*')->join('tb_cashbank_history','cashbankin_detail.no_cashbank_in','=','tb_cashbank_history.no_transaksi')->where('no_cashbank_in', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get();
        ?>
        @foreach($cekdetail as $key => $rowcbi)
            <tr>
                <?php $tanggal2 = $row->tanggal_transaksi; ?>
                <?php if ($tanggal1 == null) { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal1 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <td><?php echo $rowcbi->keterangan ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowcbi->sub_total; ?>
                        <td><?php echo number_format($rowcbi->sub_total,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowcbi->sub_total; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowcbi->sub_total,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else if ($tanggal1 == $tanggal2) { ?>
                    <td></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <td><?php echo $rowcbi->keterangan ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowcbi->sub_total; ?>
                        <td><?php echo number_format($rowcbi->sub_total,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowcbi->sub_total; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowcbi->sub_total,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal2 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <td><?php echo $rowcbi->keterangan ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowcbi->sub_total; ?>
                        <td><?php echo number_format($rowcbi->sub_total,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowcbi->sub_total; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowcbi->sub_total,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } ?>
            </tr>
        @endforeach
    <?php } else if (stripos($row->no_transaksi, 'AR') !== FALSE) { ?>
        <?php 
            $cekdetail = Invoicear::on($konek)->select('invoice_ar.*','tb_cashbank_history.*')->join('tb_cashbank_history','invoice_ar.no_invoice','=','tb_cashbank_history.no_transaksi')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get();
            // $cekdetail = Invoiceargut::on($konek)->select('invoicear_gut.*','tb_cashbank_history.*')->join('tb_cashbank_history','invoicear_gut.no_invoice','=','tb_cashbank_history.no_transaksi')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->whereBetween('tb_cashbank_history.tanggal_transaksi', array($tanggal_awal, $tanggal_akhir))->get(); 
        ?>
        @foreach($cekdetail as $key => $rowar)
            <tr>
                <?php $tanggal2 = $row->tanggal_transaksi; ?>
                <?php if ($tanggal1 == null) { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal1 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' && $get_company == '0401') {
                            $namacust = Customergut::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }else {
                            $namacust = Customer::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }
                    ?>
                    <td><?php echo $ket_nama ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else if ($tanggal1 == $tanggal2) { ?>
                    <td></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' && $get_company == '0401') {
                            $namacust = Customergut::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }else {
                            $namacust = Customer::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }
                    ?>
                    <td><?php echo $ket_nama ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal2 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' && $get_company == '0401') {
                            $namacust = Customergut::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }else {
                            $namacust = Customer::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }
                    ?>
                    <td><?php echo $ket_nama ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } ?>
            </tr>
        @endforeach
    <?php } else if (stripos($row->no_transaksi, 'IUM') !== FALSE) { ?>
        <?php 
        if ($get_company == '04' || $get_company == '0401') {
            $cekdetail = InvoicearumpaymentgutDetail::on($konek)->select('invoicearumpaymentgut_detail.*','tb_cashbank_history.*')->join('tb_cashbank_history','invoicearumpaymentgut_detail.no_journal','=','tb_cashbank_history.no_journal')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get(); 
        }else {
            $cekdetail = InvoicearumpaymentDetail::on($konek)->select('invoicearumpayment_detail.*','tb_cashbank_history.*')->join('tb_cashbank_history','invoicearumpayment_detail.no_journal','=','tb_cashbank_history.no_journal')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get(); 
        }
        ?>
        @foreach($cekdetail as $key => $rowar)
            <?php
            if ($get_company == '04' || $get_company == '0401') {
                $namas = Invoicearumgut::on($konek)->where('no_invoice', $rowar->no_invoice)->first();
                $nama = Customergut::where('id', $namas->kode_customer)->first();
            }else {
                $namas = Invoicearum::on($konek)->where('no_invoice', $rowar->no_invoice)->first();
                $nama = Customer::where('id', $namas->kode_customer)->first();
            }
            ?>
            <tr>
                <?php $tanggal2 = $row->tanggal_transaksi; ?>
                <?php if ($tanggal1 == null) { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal1 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <td><?php echo $nama->nama_customer ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else if ($tanggal1 == $tanggal2) { ?>
                    <td></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <td><?php echo $nama->nama_customer ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal2 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <td><?php echo $nama->nama_customer ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } ?>
            </tr>
        @endforeach
    <?php } else if ($nomor == 'DN') { ?>
        <?php $cekdetail = DebitNote::on($konek)->select('debit_note.*','tb_cashbank_history.*')->join('tb_cashbank_history','debit_note.no_invoice','=','tb_cashbank_history.no_transaksi')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get();
        ?>
        @foreach($cekdetail as $key => $rowar)
            <tr>
                <?php $tanggal2 = $row->tanggal_transaksi; ?>
                <?php if ($tanggal1 == null) { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal1 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' || $get_company == '0401') {
                            $namacust = Customergut::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }else {
                            $namacust = Customer::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }
                    ?>
                    <td><?php echo $ket_nama ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else if ($tanggal1 == $tanggal2) { ?>
                    <td></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' || $get_company == '0401') {
                            $namacust = Customergut::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }else {
                            $namacust = Customer::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }
                    ?>
                    <td><?php echo $ket_nama ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal2 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' || $get_company == '0401') {
                            $namacust = Customergut::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }else {
                            $namacust = Customer::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }
                    ?>
                    <td><?php echo $ket_nama ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } ?>
            </tr>
        @endforeach
    <?php } else if (stripos($row->no_transaksi, 'AP') !== FALSE) { ?>
        <?php 
            $cekdetail = Invoiceap::on($konek)->where('no_invoice', $row->no_transaksi)->first();
        ?>
            <tr>
                <?php $tanggal2 = $row->tanggal_transaksi; ?>
                <?php if ($tanggal1 == null) { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal1 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                            $namacust = Vendor::where('id', $cekdetail->kode_vendor)->first();
                            $ket_nama = $namacust->nama_vendor;
                    ?>
                    <td><?php echo $ket_nama ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo number_format($row->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($row->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else if ($tanggal1 == $tanggal2) { ?>
                    <td></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                            $namacust = Vendor::where('id', $cekdetail->kode_vendor)->first();
                            $ket_nama = $namacust->nama_vendor;
                    ?>
                    <td><?php echo $ket_nama ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo number_format($row->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($row->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal2 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                            $namacust = Vendor::where('id', $cekdetail->kode_vendor)->first();
                            $ket_nama = $namacust->nama_vendor;
                    ?>
                    <td><?php echo $ket_nama ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo number_format($row->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($row->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } ?>
            </tr>
    <?php } else if (stripos($row->no_transaksi, 'ISP') !== FALSE || stripos($row->no_transaksi, 'IHE') !== FALSE || stripos($row->no_transaksi, 'ISC') !== FALSE ) { ?>
        <?php
            $cekdetail = Invoiceargut::on($konek)->select('invoicear_gut.*','tb_cashbank_history.*')->join('tb_cashbank_history','invoicear_gut.no_invoice','=','tb_cashbank_history.no_transaksi')->where('no_invoice', $row->no_transaksi)->where('tb_cashbank_history.kode_cashbank', $row->kode_cashbank)->where('tb_cashbank_history.tanggal_transaksi', $row->tanggal_transaksi)->get(); 
        ?>
        @foreach($cekdetail as $key => $rowar)
            <tr>
                <?php $tanggal2 = $row->tanggal_transaksi; ?>
                <?php if ($tanggal1 == null) { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal1 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' && $get_company == '0401') {
                            $namacust = Customergut::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }else {
                            $namacust = Customer::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }
                    ?>
                    <td><?php echo $ket_nama ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else if ($tanggal1 == $tanggal2) { ?>
                    <td></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' && $get_company == '0401') {
                            $namacust = Customergut::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }else {
                            $namacust = Customer::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }
                    ?>
                    <td><?php echo $ket_nama ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal2 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php 
                        if ($get_company == '04' && $get_company == '0401') {
                            $namacust = Customergut::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }else {
                            $namacust = Customer::where('id', $rowar->kode_customer)->first();
                            $ket_nama = $namacust->nama_customer;
                        }
                    ?>
                    <td><?php echo $ket_nama ?></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $rowar->harga_transaksi; ?>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $rowar->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($rowar->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } ?>
            </tr>
        @endforeach
    <?php } else { ?>
        <tr>
            <?php $tanggal2 = $row->tanggal_transaksi; ?>
            <?php if ($tanggal1 == null) { ?>
                <?php $tanggal1 = $row->tanggal_transaksi; ?>
                <td><?php echo $tanggal1 ?></td>
                <td><?php echo $row->no_transaksi ?></td>
                <?php if (stripos($row->no_transaksi, 'CBT') !== FALSE) { ?>
                    <?php $ketcbt = CashbankTransfer::on($konek)->where('no_cbt', $row->no_transaksi)->first(); ?>
                    <td><?php echo $ketcbt->ket_dari ?></td>
                <?php } else { ?>
                    <?php 
                        if ($get_company == '04' || $get_company == '0401'){
                            $cek2 = Invoiceargut::on($konek)->where('no_invoice', $row->no_transaksi)->first();
                            $cust = Customergut::where('id', $cek2->kode_customer)->first();
                            $keterangan = $cust->nama_customer;
                        }
                    ?>
                    <td><?php echo $keterangan ?></td>
                <?php } ?>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo number_format($row->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($row->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else if ($tanggal1 == $tanggal2) { ?>
                    <td></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php if (stripos($row->no_transaksi, 'CBT') !== FALSE) { ?>
                        <?php $ketcbt = CashbankTransfer::on($konek)->where('no_cbt', $row->no_transaksi)->first(); ?>
                        <td><?php echo $ketcbt->ket_dari ?></td>
                    <?php } else { ?>
                        <?php 
                            if ($get_company == '04' || $get_company == '0401'){
                                $cek2 = Invoiceargut::on($konek)->where('no_invoice', $row->no_transaksi)->first();
                                $cust = Customergut::where('id', $cek2->kode_customer)->first();
                                $keterangan = $cust->nama_customer;
                            }
                        ?>
                        <td><?php echo $keterangan ?></td>
                    <?php } ?>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo number_format($row->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($row->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } else { ?>
                    <?php $tanggal1 = $row->tanggal_transaksi; ?>
                    <td><?php echo $tanggal2 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <?php if (stripos($row->no_transaksi, 'CBT') !== FALSE) { ?>
                        <?php $ketcbt = CashbankTransfer::on($konek)->where('no_cbt', $row->no_transaksi)->first(); ?>
                        <td><?php echo $ketcbt->ket_dari ?></td>
                    <?php } else { ?>
                        <?php 
                            if ($get_company == '04' || $get_company == '0401'){
                                $cek2 = Invoiceargut::on($konek)->where('no_invoice', $row->no_transaksi)->first();
                                $cust = Customergut::where('id', $cek2->kode_customer)->first();
                                $keterangan = $cust->nama_customer;
                            }
                        ?>
                        <td><?php echo $keterangan ?></td>
                    <?php } ?>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo number_format($row->harga_transaksi,'2',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($row->harga_transaksi,'2',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'2',',','.') ?></td>
                <?php } ?>
        </tr>
    <?php } ?>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #F5D2D2">
                <td></td>
                <td></td>
                <td><b>Grand Total:</b></td>
                <td><b><?php echo number_format($debet + $saldo_debet,'2',',','.') ?></b></td>
                <td><b><?php echo number_format($kredit + $saldo_kredit,'2',',','.') ?></b></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <?php
        if ($format_ttd != 1) {?>
            <br><br>
            <div class="footer" style="font-size: 10pt;">
                <br><br>
                <div class="tgl">
                    &nbsp;Palembang, <?php echo date_format($date,'d F Y');?>
                </div>

                <table width="100%" style="font-size:10pt; text-align:center;padding-left: -100px; margin:0px; border-collapse:collapse" border="0">
                    <tr style="padding:0px; margin:0px">
                        <td width="30%">Dibuat oleh,</td>
                        <td width="30%">Diperiksa oleh,</td>
                    </tr>
                    <tr style="padding:0px; margin:0px"><td colspan="3"><br><br><br></td></tr>
                    <tr style="padding:0px; margin:0px">
                        <td><?php echo $ttd; ?></td>
                        <td>Finance</td>
                    </tr>
                    <tr style="padding:0px; margin:0px">
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
    <?php } 
        else{?>
            <div class="page_break"></div>
            <table class="grid1" style="margin-left: auto; margin-right: auto; width: 50%; font-size: 11px;">
                <tfoot>
                    <tr style="background-color: #e6f2ff">
                        <th>Grand Total Debit</th>
                        <th>Grand Total Kredit</th>
                    </tr>
                    <tr style="background-color: #F5D2D2">
                        <td style="text-align: left;">&nbsp;<?php echo number_format($debet + $saldo_debet,'2',',','.');?></td>
                        <td style="text-align: left;">&nbsp;<?php echo number_format($kredit + $saldo_kredit,'2',',','.');?></td>
                    </tr>
                </tfoot>
            </table>

            <br><br>
                <div class="tgl"style="font-size: 10pt;">
                    &nbsp;Palembang, <?php echo date_format($date,'d F Y');?>
                </div>

                <table width="100%" style="font-size:10pt; text-align:center;padding-left: -100px; margin:0px; border-collapse:collapse" border="0">
                    <tr style="padding:0px; margin:0px">
                        <td width="30%">Dibuat oleh,</td>
                        <td width="30%">Diperiksa oleh,</td>
                    </tr>
                    <tr style="padding:0px; margin:0px"><td colspan="3"><br><br><br></td></tr>
                    <tr style="padding:0px; margin:0px">
                        <td><?php echo $ttd; ?></td>
                        <td>Finance</td>
                    </tr>
                    <tr style="padding:0px; margin:0px">
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
    <?php } ?>
</body>
</html>