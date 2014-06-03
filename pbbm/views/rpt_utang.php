<html>
<head>
</head>
<body>
<pre>
<?
$hal   = 1;
$nomor = 0;
$nom   = 0;
$jml_sppt1  = 0;
$jml_sppt2  = 0;
$jml_sppt3  = 0;
$jml_sppt4  = 0;
$jml_sppt5  = 0;

$jml_amount1  = 0;
$jml_amount2  = 0;
$jml_amount3  = 0;
$jml_amount4  = 0;
$jml_amount5  = 0;

$tot_sppt1  = 0;
$tot_sppt2  = 0;
$tot_sppt3  = 0;
$tot_sppt4  = 0;
$tot_sppt5  = 0;

$tot_amount1  = 0;
$tot_amount2  = 0;
$tot_amount3  = 0;
$tot_amount4  = 0;
$tot_amount5  = 0;

$jml_prsn1 = 0;
$jml_prsn2 = 0;

$tot_prsn1 = 0;
$tot_prsn2 = 0;

$baris = 47;
$lebar = 91;
$title0= str_replace(" ", "&nbsp;", str_pad('PIUTANG PAJAK BUMI DAN BANGUNAN PERDESAAN DAN PERKOTAAN', $lebar, ' ', STR_PAD_BOTH))."<br>".
        str_replace(" ", "&nbsp;", str_pad(LICENSE_TO, $lebar, ' ', STR_PAD_BOTH))."<br>".
        str_replace(" ", "&nbsp;", str_pad('BUKU '.$buku, $lebar, ' ', STR_PAD_BOTH))."<br>".
        str_replace(" ", "&nbsp;", str_pad("TAHUN PAJAK $tahun S.D $tahun2", $lebar, ' ', STR_PAD_BOTH))."<br>";
$ncount=0;
$kec ='';        
if  ($kec_nm){      
  $kec =  $kec_nm[0]->kd_kecamatan. " " .$kec_nm[0]->nm_kecamatan;
  $title0 .= str_replace(" ", "&nbsp;", str_pad("KECAMATAN: $kec", 30, ' ', STR_PAD_RIGHT))  ;
  $ncount+=30;
}
$kel='';
if ($kel_nm){
 $kel =  $kel_nm[0]->kd_kecamatan .".".$kel_nm[0]->kd_kelurahan ." ". $kel_nm[0]->nm_kelurahan;
 $title0 .= str_replace(" ", "&nbsp;", str_pad("KELURAHAN : $kel", 30, ' ', STR_PAD_RIGHT));
 $ncount+=30;
}

//$title .= "<br>";
  //      "         1         2         3         4         5         6         7         8         9         0         1         2         3         4         5         6         7         2";
  //       "123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
//          32.79.001.001|1234567890123456789|100.000|100.000.000.000|100.000|100.000.000.000|100.000|100.000.000.000|100.000|100.000.000.000|100.0|100.000|100.000.000.000|100.0
$title1  = "===========================================================================================<br>";
$title1 .= "KODE           |URAIAN                                            |  SPPT |    POKOK      <br>";
$title1 .= "===========================================================================================<br>";


echo $title0.str_replace(" ", "&nbsp;", str_pad("Halaman : " . number_format($hal, 0, ',', '.'), $lebar-$ncount, ' ', STR_PAD_LEFT)) . "<br>";
echo $title1;

if ($detail) {
    foreach ($detail as $row) {
      $nomor += 1;
      $nom   += 1;
      if (strlen($row->kode)>13) 
           $kode    = substr($row->kode,14,25);
      else $kode    = $row->kode;
      $uraian   = $row->uraian;
      $sppt1   = $row->transaksi;
      $amount1 = $row->amount;
      $jml_sppt1  += $sppt1;
      $jml_amount1  += $amount1;
      $tot_sppt1  +=$sppt1;
      $tot_amount1  +=$amount1;
      echo str_replace(" ", "&nbsp;", str_pad("$kode", 13, " ", STR_PAD_RIGHT)) ."|". 
      str_replace(" ", "&nbsp;", str_pad(substr($uraian,0,26), 50 , ' ', STR_PAD_RIGHT))."|".
      str_replace(" ", "&nbsp;", str_pad(number_format($sppt1,0, ',', '.'), 7, ' ', STR_PAD_LEFT))."|".
      str_replace(" ", "&nbsp;", str_pad(number_format($amount1, 0,',', '.'), 15, ' ', STR_PAD_LEFT));
      echo "<br>";
        
      if ($nom % $baris == 0) {
        echo "===========================================================================================<br>";
        echo str_repeat('&nbsp;', 0) . "JUMLAH HALAMAN INI:" . str_repeat('&nbsp;', 48)."|". 
            str_replace(" ", "&nbsp;", str_pad(number_format($jml_sppt1,0, ',', '.'), 7, ' ', STR_PAD_LEFT))."|".
            str_replace(" ", "&nbsp;", str_pad(number_format($jml_amount1, 0,',', '.'), 15, ' ', STR_PAD_LEFT)).
            "<br>";
        echo "===========================================================================================<br>";
        echo str_repeat('&nbsp;', 0) . "JUMLAH SAMPAI DENGAN HALAMAN INI:" . str_repeat('&nbsp;', 34)."|" . 
            str_replace(" ", "&nbsp;", str_pad(number_format($tot_sppt1,0, ',', '.'), 7, ' ', STR_PAD_LEFT))."|".
            str_replace(" ", "&nbsp;", str_pad(number_format($tot_amount1, 0,',', '.'), 15, ' ', STR_PAD_LEFT)). "<br>";
        echo "===========================================================================================<br>";
        $hal  += 1;

        echo $title0.str_replace(" ", "&nbsp;", str_pad("Halaman : " . number_format($hal, 0, ',', '.'), 91-$ncount, ' ', STR_PAD_LEFT)) . "<br>";
        echo $title1;

        $jml_sppt1  = 0;
        $jml_sppt2  = 0;
        $jml_sppt3  = 0;
        $jml_sppt4  = 0;
        $jml_sppt5  = 0;

        $jml_amount1  = 0;
        $jml_amount2  = 0;
        $jml_amount3  = 0;
        $jml_amount4  = 0;
        $jml_amount5  = 0;

     }
  }
} else {
    echo "TIDAK ADA DATA<br>";
}

echo "===========================================================================================<br>";
echo str_repeat('&nbsp;', 0) . "JUMLAH HALAMAN INI:" . str_repeat('&nbsp;', 48) ."|". 
            str_replace(" ", "&nbsp;", str_pad(number_format($jml_sppt1,0, ',', '.'), 7, ' ', STR_PAD_LEFT))."|".
            str_replace(" ", "&nbsp;", str_pad(number_format($jml_amount1, 0,',', '.'), 15, ' ', STR_PAD_LEFT)).
            "<br>";
echo "===========================================================================================<br>";
echo str_repeat('&nbsp;', 0) . "JUMLAH SAMPAI DENGAN HALAMAN INI:" . str_repeat('&nbsp;', 34)."|" . 
            str_replace(" ", "&nbsp;", str_pad(number_format($tot_sppt1,0, ',', '.'), 7, ' ', STR_PAD_LEFT))."|".
            str_replace(" ", "&nbsp;", str_pad(number_format($tot_amount1, 0,',', '.'), 15, ' ', STR_PAD_LEFT)). "<br>";
echo "===========================================================================================<br>";

?>
</pre>
</body>
<html>  

