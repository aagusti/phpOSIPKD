<html>
<head>
</head>
<body>
<pre>
<?
$hal   = 1;
$nomor = 0;
$nom   = 0;
$baris = 47;
$lebar = 108;
$ncount= 0;

$title0 = str_replace(" ", "&nbsp;", str_pad('DATA OBJEK PAJAK DAN SUBJEK PAJAK', $lebar, ' ', STR_PAD_BOTH))."<br>".
          str_replace(" ", "&nbsp;", str_pad('PAJAK BUMI DAN BANGUNAN PERDESAAN DAN PERKOTAAN ', $lebar, ' ', STR_PAD_BOTH))."<br>".
          str_replace(" ", "&nbsp;", str_pad(LICENSE_TO, $lebar, ' ', STR_PAD_BOTH))."<br><br><br>";

//        "         1         2         3         4         5         6         7         8         9         0         1         2         3         4         5         6         7         2";
//        "123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
$title1  = "===========================================================================================================<br>";
$title1 .= "THN  |  LUAS   |    NJOP     |  LUAS   |    NJOP     |   PBB YG    |    BAYAR    |   SISA     | TGL BAYAR |<br>";
$title1 .= "     |  BUMI   |    BUMI     |BANGUNAN |  BANGUNAN   |  HARUS DI   |    (Rp)     |   (Rp)     |   AKHIR   |<br>";
$title1 .= "     |  (M2)   |    (Rp)     |  (M2)   |    (Rp)     |   BAYAR     |             |            |           |<br>";
$title1 .= "     |         |             |         |             |   (Rp)      |             |            |           |<br>";
$title1 .= "===========================================================================================================<br>";


echo $title0 ;

echo str_replace(" ", "&nbsp;", str_pad('NOP : '.$data_source[0]['nop'], $lebar, ' ', STR_PAD_RIGHT))."<br><br>";

echo str_replace(" ", "&nbsp;", str_pad('OBJEK PAJAK : ', $lebar/2, ' ', STR_PAD_RIGHT))                                 .str_replace(" ", "&nbsp;", str_pad('SUBJEK PAJAK : ', $lebar/2, ' ', STR_PAD_RIGHT))."<br>";
echo str_replace(" ", "&nbsp;", str_pad('Letak OP    : '.$data_source[0]['alamat_op'], $lebar/2, ' ', STR_PAD_RIGHT))    .str_replace(" ", "&nbsp;", str_pad('Nama WP      : '.$data_source[0]['nm_wp_sppt'], $lebar/2, ' ', STR_PAD_RIGHT))."<br>";
echo str_replace(" ", "&nbsp;", str_pad('RT/RW       : '.$data_source[0]['rt_rw_op'], $lebar/2, ' ', STR_PAD_RIGHT))     .str_replace(" ", "&nbsp;", str_pad('Letak OP     : '.$data_source[0]['alamat_wp'], $lebar/2, ' ', STR_PAD_RIGHT))."<br>";
echo str_replace(" ", "&nbsp;", str_pad('Kelurahan   : '.$data_source[0]['kelurahan_op'], $lebar/2, ' ', STR_PAD_RIGHT)) .str_replace(" ", "&nbsp;", str_pad('RT/RW        : '.$data_source[0]['rt_rw_wp'], $lebar/2, ' ', STR_PAD_RIGHT))."<br>";
echo str_replace(" ", "&nbsp;", str_pad('Kecamatan   : '.$data_source[0]['kecamatan_op'], $lebar/2, ' ', STR_PAD_RIGHT)) .str_replace(" ", "&nbsp;", str_pad('Kelurahan    : '.$data_source[0]['kelurahan_wp'], $lebar/2, ' ', STR_PAD_RIGHT))."<br>";
echo str_replace(" ", "&nbsp;", str_pad('Kota        : '.LICENSE_TO, $lebar/2, ' ', STR_PAD_RIGHT))                           .str_replace(" ", "&nbsp;", str_pad('Kab./Kota    : '.$data_source[0]['kota_wp'], $lebar/2, ' ', STR_PAD_RIGHT))."<br><br>";

echo str_replace(" ", "&nbsp;", str_pad('SPPT : ', $lebar, ' ', STR_PAD_RIGHT))."<br>";
echo $title1 ;


if ($data_source) {
	foreach ($data_source as $row) {
		$nomor += 1;
		$nom   += 1;
		
		$thn_sppt  = $row['thn_pajak_sppt'];
		$luas_bumi = number_format ($row['luas_tanah'], 0 ,  ',' , '.' );
		$njop_bumi = number_format ($row['njop_tanah'], 0 ,  ',' , '.' );
		$luas_bng  = number_format ($row['luas_bng'], 0 ,  ',' , '.' );
		$njop_bng  = number_format ($row['njop_bng'], 0 ,  ',' , '.' );
		$ketetapan = number_format ($row['ketetapan'], 0 ,  ',' , '.' );
		$jml_bayar = number_format ($row['jml_bayar'], 0 ,  ',' , '.' );
		$sisa      = number_format ($row['ketetapan']-$row['jml_bayar'], 0 ,  ',' , '.' );
		$tgl_bayar = $row['tgl_bayar'];
		
		echo str_replace(" ", "&nbsp;", str_pad($thn_sppt   , 5, ' ', STR_PAD_RIGHT)) ."|";
		echo str_replace(" ", "&nbsp;", str_pad($luas_bumi  , 9, ' ', STR_PAD_LEFT))  ."|";
		echo str_replace(" ", "&nbsp;", str_pad($njop_bumi  ,13, ' ', STR_PAD_LEFT))  ."|";
		echo str_replace(" ", "&nbsp;", str_pad($luas_bng   , 9, ' ', STR_PAD_LEFT))  ."|";
		echo str_replace(" ", "&nbsp;", str_pad($njop_bng   ,13, ' ', STR_PAD_LEFT))  ."|";
		echo str_replace(" ", "&nbsp;", str_pad($ketetapan  ,13, ' ', STR_PAD_LEFT))  ."|";
		echo str_replace(" ", "&nbsp;", str_pad($jml_bayar  ,13, ' ', STR_PAD_LEFT))  ."|";
		echo str_replace(" ", "&nbsp;", str_pad($sisa       ,12, ' ', STR_PAD_LEFT))  ."|";
		echo str_replace(" ", "&nbsp;", str_pad($tgl_bayar  ,11, ' ', STR_PAD_LEFT))  ."|";
		
		echo "<br>";
	}
} else {
	echo str_replace(" ", "&nbsp;", str_pad('TIDAK ADA DATA', $lebar, ' ', STR_PAD_BOTH))."<br>";
};

echo "===========================================================================================================<br>";

?>
</pre>
</body>
<html>  

