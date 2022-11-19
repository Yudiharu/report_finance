<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<?php use App\Models\tb_cashbank_history; ?>

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
                    <td><?php echo number_format($saldo_awal->beginning_balance,'0',',','.') ?></td>
                <?php } else{ ?>
                    <td>0</td>
                <?php } ?>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td>SALDO TO DATE</td>
                <td><?php echo number_format($saldo_debet,'0',',','.') ?></td>
                <td><?php echo number_format($saldo_kredit,'0',',','.') ?></td>
                <?php if ($saldo_awal != null){ 
                    $saldo_akhir_awal = $saldo_debet - $saldo_kredit + $saldo_awal->beginning_balance; 
                } else{ 
                    $saldo_akhir_awal = 0;
                } ?>
                <td><?php echo number_format($saldo_akhir_awal,'0',',','.') ?></td>
            </tr>
<?php 
$saldo_akhir1 = $saldo_akhir_awal; 
$tanggal1 = null;
$tanggal2 = null;
?>
            @foreach($cashbankbalance as $key => $row)
            <?php 
                $cbs = tb_cashbank_history::on($konek)->where('tanggal_transaksi', $row->tanggal_transaksi)->where('kode_cashbank', $row->kode_cashbank)->first(); 
            ?>
            <tr>
                <?php $tanggal2 = $cbs->tanggal_transaksi; ?>
                <?php if ($tanggal1 == null) { ?>
                    <?php $tanggal1 = $cbs->tanggal_transaksi; ?>
                    <td><?php echo $tanggal1 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <td></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo number_format($row->harga_transaksi,'0',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($row->harga_transaksi,'0',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'0',',','.') ?></td>
                <?php } else if ($tanggal1 == $tanggal2) { ?>
                    <td></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <td></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo number_format($row->harga_transaksi,'0',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($row->harga_transaksi,'0',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'0',',','.') ?></td>
                <?php } else { ?>
                    <?php $tanggal1 = $cbs->tanggal_transaksi; ?>
                    <td><?php echo $tanggal2 ?></td>
                    <td><?php echo $row->no_transaksi ?></td>
                    <td></td>
                    <?php if ($row->dbkr_type == 'D') { ?>
                        <?php $debet += $row->harga_transaksi; ?>
                        <td><?php echo number_format($row->harga_transaksi,'0',',','.') ?></td>
                        <td>0</td>
                    <?php } else if ($row->dbkr_type == 'K') { ?>
                        <?php $kredit += $row->harga_transaksi; ?>
                        <td>0</td>
                        <td><?php echo number_format($row->harga_transaksi,'0',',','.') ?></td>
                    <?php } ?>
                    <td><?php echo number_format($saldo_akhir = $saldo_akhir1 - $kredit + $debet,'0',',','.') ?></td>
                <?php } ?>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #F5D2D2">
                <td></td>
                <td></td>
                <td><b>Grand Total:</b></td>
                <td><b><?php echo number_format($debet + $saldo_debet,'0',',','.') ?></b></td>
                <td><b><?php echo number_format($kredit + $saldo_kredit,'0',',','.') ?></b></td>
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
                        <td style="text-align: left;">&nbsp;<?php echo number_format($debet + $saldo_debet,'0',',','.');?></td>
                        <td style="text-align: left;">&nbsp;<?php echo number_format($kredit + $saldo_kredit,'0',',','.');?></td>
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