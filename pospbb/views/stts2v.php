<html>
<head>
</head>
<body>

<? 
foreach ($dtCetak as $data) : 

    $nm_tp = $data[8];
    $thn_pajak_sppt = $data[9];
    $nm_wp_sppt = $data[10];
    $nm_kecamatan = $data[11];
    $nm_kelurahan = $data[12];
    $kode = $data[13];
    $jml_sppt_yg_dibayar = $data[14];
    $denda_sppt = $data[15];
    $tgl_jatuh_tempo_sppt = $data[16];
    $tgl_pembayaran_sppt = $data[17];
    $jml_sppt_yg_dibayar = $data[18];

    $luas_bumi_sppt = $data[19];
    $luas_bng_sppt = $data[20];
    ?>


<pre>
<!--
&nbsp;
&nbsp;
&nbsp;-->
&nbsp;
<?=str_repeat('&nbsp;',16).$nm_tp?>&nbsp;
<?=str_repeat('&nbsp;',26).$thn_pajak_sppt?>&nbsp;
<?=str_repeat('&nbsp;',16).substr($nm_wp_sppt,0,30)?>&nbsp;
<?=str_repeat('&nbsp;',23).substr($nm_kecamatan,0,30)?>&nbsp;
<?=str_repeat('&nbsp;',23).substr($nm_kelurahan,0,30)?>&nbsp;
<?=str_repeat('&nbsp;',16).$kode?>&nbsp;
<?=str_repeat('&nbsp;',25).number_format($jml_sppt_yg_dibayar-$denda_sppt,0,',','.')?>&nbsp;
&nbsp; 
<?=str_repeat('&nbsp;',20).date('d/m/Y',strtotime($tgl_jatuh_tempo_sppt))?>&nbsp;
&nbsp;
&nbsp;
<?=str_repeat('&nbsp;',8) . 'TGL PEMBAYARAN    :   ' .str_pad(date('d/m/Y',strtotime($tgl_pembayaran_sppt)),17," ",STR_PAD_LEFT)?>&nbsp;
<?=str_repeat('&nbsp;',8) . 'PEMBAYARAN        :Rp.' .str_pad(number_format($jml_sppt_yg_dibayar-$denda_sppt,0,',','.'), 17, " ", STR_PAD_LEFT)?>&nbsp;
<?=str_repeat('&nbsp;',8) . 'DENDA ADMINISTRSI :Rp.' .str_pad(number_format($denda_sppt,0,',','.'), 17, " ", STR_PAD_LEFT)?>&nbsp;
<?=str_repeat('&nbsp;',8) . 'TOTAL PEMBAYARAN  :Rp.' .str_pad(number_format($jml_sppt_yg_dibayar,0,',','.'), 17, " ", STR_PAD_LEFT)?>&nbsp;
&nbsp;
&nbsp;
&nbsp;
&nbsp;
&nbsp;
<?
  $sn=date('dmY',strtotime($tgl_pembayaran_sppt));
  $sn.=$kode;
?>  
<?=str_repeat('&nbsp;',8) . 'SN : '. md5($sn)?>&nbsp;
<?=str_repeat('&nbsp;',18) . str_pad(date('d/m/Y',strtotime($tgl_pembayaran_sppt)),12," ",STR_PAD_RIGHT).str_pad(number_format($luas_bumi_sppt,0,',','.'),10," ",STR_PAD_LEFT)?>&nbsp;
<?=str_repeat('&nbsp;',30) . str_pad(number_format($luas_bng_sppt,0,',','.'),10," ",STR_PAD_LEFT)?>&nbsp;
<?=str_repeat('&nbsp;',18) . str_pad(number_format($jml_sppt_yg_dibayar,0,',','.'),20," ",STR_PAD_RIGHT)?>

<!--Lembar 2-->
2
&nbsp;
&nbsp;
&nbsp;
<?=str_repeat('&nbsp;',16).$nm_tp?>&nbsp;
<?=str_repeat('&nbsp;',26).$thn_pajak_sppt?>&nbsp;
<?=str_repeat('&nbsp;',16).substr($nm_wp_sppt,0,30)?>&nbsp;
<?=str_repeat('&nbsp;',23).substr($nm_kecamatan,0,30)?>&nbsp;
<?=str_repeat('&nbsp;',23).substr($nm_kelurahan,0,30)?>&nbsp;
<?=str_repeat('&nbsp;',16).$kode?>&nbsp;
<?=str_repeat('&nbsp;',16).number_format($jml_sppt_yg_dibayar,0,',','.')?>&nbsp;
<?=str_repeat('&nbsp;',16).date('d/m/Y',strtotime($tgl_pembayaran_sppt))?>&nbsp;
<?=str_repeat('&nbsp;',19).number_format($jml_sppt_yg_dibayar,0,',','.')?>&nbsp;

<!--Lembar 3 Request Tambahan utk BUD-->
3
<?=str_repeat('&nbsp;',16). 'PEMBAYARAN PBB KETETAPAN TAHUN ' .$thn_pajak_sppt?>&nbsp;
<?=str_repeat('&nbsp;',16).substr($nm_wp_sppt,0,30)?>&nbsp;
<?=str_repeat('&nbsp;',16).substr($nm_kecamatan,0,30)?>&nbsp;
<?=str_repeat('&nbsp;',16).substr($nm_kelurahan,0,30)?>&nbsp;
<?=str_repeat('&nbsp;',16).$kode?>&nbsp;
<?=str_repeat('&nbsp;',16).date('d/m/Y',strtotime($tgl_pembayaran_sppt))?>&nbsp;
<?=str_repeat('&nbsp;',19).number_format($jml_sppt_yg_dibayar,0,',','.')?>&nbsp;

<!--Lembar Bank -->
4
&nbsp;
&nbsp;
&nbsp;
&nbsp;
<?=str_repeat('&nbsp;',16).$nm_tp?>&nbsp;
<?=str_repeat('&nbsp;',26).$thn_pajak_sppt?>&nbsp;
<?=str_repeat('&nbsp;',16).substr($nm_wp_sppt,0,30)?>&nbsp;
<?=str_repeat('&nbsp;',23).substr($nm_kecamatan,0,30)?>&nbsp;
<?=str_repeat('&nbsp;',23).substr($nm_kelurahan,0,30)?>&nbsp;
<?=str_repeat('&nbsp;',16).$kode?>&nbsp;
<?=str_repeat('&nbsp;',25).number_format($jml_sppt_yg_dibayar,0,',','.')?>&nbsp;
<?=str_repeat('&nbsp;',25).date('d/m/Y',strtotime($tgl_pembayaran_sppt))?>&nbsp;
<?=str_repeat('&nbsp;',25).number_format($jml_sppt_yg_dibayar,0,',','.')?>&nbsp;
&nbsp;
&nbsp;
&nbsp;
&nbsp;
&nbsp;
&nbsp;
</pre>

<? endforeach; ?>

</body>
</html>