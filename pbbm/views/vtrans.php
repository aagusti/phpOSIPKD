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
echo str_replace(" ", "&nbsp;", str_pad('DAFTAR PENERIMAAN HARIAN', 91, ' ', STR_PAD_BOTH))."<br>";
echo str_replace(" ", "&nbsp;", str_pad('PAJAK BUMI DAN BANGUNAN PERDESAAN DAN PERKOTAAN', 91, ' ', STR_PAD_BOTH))."<br>";
echo str_replace(" ", "&nbsp;", str_pad(LICENSE_TO, 91, ' ', STR_PAD_BOTH))."<br>";
echo "<br>";
echo "KODE PEMBAYARAN : ".$header->kode_pmb."<br>";
echo "KECAMATAN       : " . str_replace(" ", "&nbsp;", str_pad($header->kec, 26, ' ', STR_PAD_RIGHT)) . 
"KELURAHAN : ".str_replace(" ", "&nbsp;", str_pad($header->kel, 23, ' ', STR_PAD_RIGHT)) . 
str_replace(" ", "&nbsp;", str_pad("Halaman : " . number_format($hal, 0, ',', '.'), 9, ' ', STR_PAD_LEFT)) . "<br>";

//   "         1         2         3         4         5         6         7         8         9         0         1    ";
//   "1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789";
echo "===========================================================================================<br>";
echo "NO.  URAIAN                                          TAHUN      POKOK     DENDA       JUMLAH<br>";
echo "                                                     PAJAK                                 <br>";
echo "===========================================================================================<br>";
if ($detail) {
    foreach ($detail as $row) {
        $nomor += 1;
        $nom   += 1;
		
        $nop    = $row->nop;
        $nama   = $row->pemilik;
        $tahunp = $row->thn_pajak_sppt;
        $pokok  = $row->pokok;
        $denda  = $row->denda;
        $bayar  = $row->bayar;
        
        $jml1  += $row->pokok;
        $jml2  += $row->denda;
        $jml3  += $row->bayar;
        
        $tot1  += $row->pokok;
        $tot2  += $row->denda;
        $tot3  += $row->bayar;
        
        echo str_replace(" ", "&nbsp;", str_pad(number_format($nomor, 0, ',', '.'), 4, " ", STR_PAD_RIGHT)) . 
			"&nbsp;{$nop}&nbsp;" .
			str_replace(" ", "&nbsp;", str_pad(substr($nama,0,22), 23, ' ', STR_PAD_RIGHT)) .
			$tahunp .
			str_replace(" ", "&nbsp;", str_pad(number_format($pokok, 0, ',', '.'), 12, ' ', STR_PAD_LEFT)) .
			str_replace(" ", "&nbsp;", str_pad(number_format($denda, 0, ',', '.'), 10, ' ', STR_PAD_LEFT)) . 
			str_replace(" ", "&nbsp;", str_pad(number_format($bayar, 0, ',', '.'), 12, ' ', STR_PAD_LEFT)) ;
		echo "<br>";
			
    if ($nom % $baris == 0) {
			echo "===========================================================================================<br>";
			echo str_repeat('&nbsp;', 0) . "JUMLAH HALAMAN INI:" . str_repeat('&nbsp;', 38) . str_replace(" ", "&nbsp;", str_pad(number_format($jml1, 0, ',', '.'), 12, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($jml2, 0, ',', '.'), 10, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($jml3, 0, ',', '.'), 12, ' ', STR_PAD_LEFT)) . "<br>";
			echo "===========================================================================================<br>";
			echo str_repeat('&nbsp;', 0) . "JUMLAH SAMPAI DENGAN HALAMAN INI:" . str_repeat('&nbsp;', 24) . str_replace(" ", "&nbsp;", str_pad(number_format($tot1, 0, ',', '.'), 12, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($tot2, 0, ',', '.'), 10, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($tot3, 0, ',', '.'), 12, ' ', STR_PAD_LEFT)) . "<br>";
			echo "===========================================================================================<br>";

            /*echo "<br>";
            echo "<br>";
            echo "<br>";
            echo "<br>";
            echo "<br>";
            echo "<br>";
            echo "<br>";
            echo "<br>";
            echo "<br>";*/
			
			$hal  += 1;
			echo str_replace(" ", "&nbsp;", str_pad('DAFTAR PENERIMAAN HARIAN', 91, ' ', STR_PAD_BOTH))."<br>";
			echo str_replace(" ", "&nbsp;", str_pad('PAJAK BUMI DAN BANGUNAN PERDESAAN DAN PERKOTAAN', 91, ' ', STR_PAD_BOTH))."<br>";
			echo str_replace(" ", "&nbsp;", str_pad(LICENSE_TO, 91, ' ', STR_PAD_BOTH))."<br>";
			echo "<br>";
			echo "KODE PEMBAYARAN : ".$header->kode_pmb."<br>";
			echo "KECAMATAN       : " . str_replace(" ", "&nbsp;", str_pad($header->kec, 26, ' ', STR_PAD_RIGHT)) . 
			"KELURAHAN : ".str_replace(" ", "&nbsp;", str_pad($header->kel, 23, ' ', STR_PAD_RIGHT)) . 
			str_replace(" ", "&nbsp;", str_pad("Halaman : " . number_format($hal, 0, ',', '.'), 9, ' ', STR_PAD_LEFT)) . "<br>";
			
			echo "============================================================================================<br>";
			echo "NO.  NOP                           NAMA WP            TAHUN      POKOK     DENDA    PBB YANG<br>";
			echo "                                                      PAJAK                          DIBAYAR<br>";
			echo "============================================================================================<br>";
			
			$jml1  = 0;
			$jml2  = 0;
			$jml3  = 0;

        }
    }
} else {
    echo "TIDAK ADA DATA<br>";
}
echo "===========================================================================================<br>";
echo str_repeat('&nbsp;', 0) . "JUMLAH HALAMAN INI:" . str_repeat('&nbsp;', 38) . str_replace(" ", "&nbsp;", str_pad(number_format($jml1, 0, ',', '.'), 12, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($jml2, 0, ',', '.'), 10, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($jml3, 0, ',', '.'), 12, ' ', STR_PAD_LEFT)) . "<br>";
echo "===========================================================================================<br>";
echo str_repeat('&nbsp;', 0) . "JUMLAH SAMPAI DENGAN HALAMAN INI:" . str_repeat('&nbsp;', 24) . str_replace(" ", "&nbsp;", str_pad(number_format($tot1, 0, ',', '.'), 12, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($tot2, 0, ',', '.'), 10, ' ', STR_PAD_LEFT)) . str_replace(" ", "&nbsp;", str_pad(number_format($tot3, 0, ',', '.'), 12, ' ', STR_PAD_LEFT)) . "<br>";
echo "===========================================================================================<br>";

echo "<br>";

echo str_replace(" ", "&nbsp;", str_pad(LICENSE_TO. ", ................", 91, ' ', STR_PAD_LEFT))."<br>";
echo str_replace(" ", "&nbsp;", str_pad("Mengetahui : ", 45, ' ', STR_PAD_BOTH)) . 
	 str_replace(" ", "&nbsp;", str_pad("Petugas Pemungut" , 46, ' ', STR_PAD_BOTH)) . "<br>";
echo "<br>";
echo str_replace(" ", "&nbsp;", str_pad("Lurah / Kepala Desa ....................", 45, ' ', STR_PAD_BOTH)) . "<br>";

echo "<br>";
echo "<br>";
echo "<br>";

echo str_replace(" ", "&nbsp;", str_pad("_____________________________", 45, ' ', STR_PAD_BOTH)) . 
	 str_replace(" ", "&nbsp;", str_pad("_____________________________", 45, ' ', STR_PAD_BOTH)) . "<br>";
echo "<br>";
echo str_replace(" ", "&nbsp;", str_pad("NIP. ........................", 45, ' ', STR_PAD_BOTH)) . 
	 str_replace(" ", "&nbsp;", str_pad("NIP. ........................", 45, ' ', STR_PAD_BOTH)) . "<br>";
/* echo 
	Mengetahui :	Petugas Pemungut
	Lurah / Kepala Desa …………….


	……………………………………… 	…………………………………….
	 NIP. ………………………………	NIP. …………………………….. */

?>
</pre>
</body>
<html>  

