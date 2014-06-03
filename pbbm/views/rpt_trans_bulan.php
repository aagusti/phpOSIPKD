<html>
<head>
</head>
<body>
<pre>
<?
$hal   = 1;
$nomor = 0;
$nom   = 0;
$jml1  = 0;
$jml2  = 0;
$jml3  = 0;
$tot1  = 0;
$tot2  = 0;
$tot3  = 0;
$baris = 47;
$title = str_replace(" ", "&nbsp;", str_pad('REKAPITULASI BULANAN', 91, ' ', STR_PAD_BOTH))."<br>".
        str_replace(" ", "&nbsp;", str_pad('PAJAK BUMI DAN BANGUNAN PERDESAAN DAN PERKOTAAN', 91, ' ', STR_PAD_BOTH))."<br>".
        str_replace(" ", "&nbsp;", str_pad(LICENSE_TO, 91, ' ', STR_PAD_BOTH))."<br>".
        str_replace(" ", "&nbsp;", str_pad('BUKU '.$buku, 91, ' ', STR_PAD_BOTH))."<br>".
        str_replace(" ", "&nbsp;", str_pad('TRANSAKSI PEMBAYARAN '.$tahun, 91, ' ', STR_PAD_BOTH))."<br>".
        str_replace(" ", "&nbsp;", str_pad('TAHUN PAJAK '.$tahun_sppt1." SAMPAI DENGAN TAHUN PAJAK $tahun_sppt2", 91, ' ', STR_PAD_BOTH))."<br>".

        "<br>";
$ncount=0;
$kec ='';        
if  ($kec_nm){      
  $kec =  $kec_nm[0]->nm_kecamatan;
  $title .= str_replace(" ", "&nbsp;", str_pad("KECAMATAN: $kec", 30, ' ', STR_PAD_RIGHT))  ;
  $ncount+=30;
}
$kel='';
if ($kel_nm){
 $kel =  $kel_nm[0]->nm_kelurahan;
 $title .= str_replace(" ", "&nbsp;", str_pad("KELURAHAN : $kel", 30, ' ', STR_PAD_RIGHT));
 $ncount+=30;
}

$title .= str_replace(" ", "&nbsp;", str_pad("Halaman : " . number_format($hal, 0, ',', '.'), 91-$ncount, ' ', STR_PAD_LEFT)) . "<br>";
//$title .= "<br>";
  //      "         1         2         3         4         5         6         7         8         9         0         1    ";
  //      "1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789";
$title .= "===========================================================================================<br>";
$title .= "KODE         URAIAN                                     POKOK          DENDA         JUMLAH<br>";
$title .= "                                                                                           <br>";
$title .= "===========================================================================================<br>";


echo $title;

if ($detail) {
    foreach ($detail as $row) {
        $nomor += 1;
        $nom   += 1;
		
        $kode    = $row->kode;
        $uraian   = $row->uraian;
        $pokok  = $row->pokok;
        $denda  = $row->denda;
        $bayar  = $row->bayar;
        
        $jml1  += $row->pokok;
        $jml2  += $row->denda;
        $jml3  += $row->bayar;
        
        $tot1  += $row->pokok;
        $tot2  += $row->denda;
        $tot3  += $row->bayar;
        $namabulan=namabulan($kode);
        echo str_replace(" ", "&nbsp;", str_pad("$kode :$namabulan", 13, " ", STR_PAD_RIGHT)) . 
			str_replace(" ", "&nbsp;", str_pad(substr($uraian,0,30), 33, ' ', STR_PAD_RIGHT)) .
			str_replace(" ", "&nbsp;", str_pad(number_format($pokok, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) .
			str_replace(" ", "&nbsp;", str_pad(number_format($denda, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . 
			str_replace(" ", "&nbsp;", str_pad(number_format($bayar, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) ;
		echo "<br>";
			
    if ($nom % $baris == 0) {
			echo "===========================================================================================<br>";
			echo str_repeat('&nbsp;', 0) . "JUMLAH HALAMAN INI:" . str_repeat('&nbsp;', 30) . str_replace(" ", "&nbsp;", str_pad(number_format($jml1, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($jml2, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($jml3, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . "<br>";
			echo "===========================================================================================<br>";
			echo str_repeat('&nbsp;', 0) . "JUMLAH SAMPAI DENGAN HALAMAN INI:" . str_repeat('&nbsp;', 24) . str_replace(" ", "&nbsp;", str_pad(number_format($tot1, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($tot2, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($tot3, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . "<br>";
			echo "===========================================================================================<br>";
			$hal  += 1;

      title();
			
			$jml1  = 0;
			$jml2  = 0;
			$jml3  = 0;

        }
    }
} else {
    echo "TIDAK ADA DATA<br>";
}
echo "===========================================================================================<br>";
echo str_repeat('&nbsp;', 0) . "JUMLAH HALAMAN INI:" . str_repeat('&nbsp;', 27) . str_replace(" ", "&nbsp;", str_pad(number_format($jml1, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($jml2, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($jml3, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . "<br>";
echo "===========================================================================================<br>";
echo str_repeat('&nbsp;', 0) . "JUMLAH SAMPAI DENGAN HALAMAN INI:" . str_repeat('&nbsp;', 13) . str_replace(" ", "&nbsp;", str_pad(number_format($tot1, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($tot2, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($tot3, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)) . "<br>";
echo "===========================================================================================<br>";

?>
</pre>
</body>
<html>  

