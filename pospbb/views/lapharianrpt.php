<html>
<head>
</head>
<body>
<?
//  Result := false;
  $nomor = 0;
  $nom   = 0;
  $hal   = 1;
  $jml1  = 0;
  $jml2  = 0;
  $jml3  = 0;

  
?>
<pre>
&nbsp;<br>
<?
	echo str_repeat('&nbsp;',28)."DAFTAR PENERIMAAN HARIAN TANGGAL $tgl <br>";
	echo "<br>";
	echo "Kelurahan:$kelnm, Buku:$bukunm<br>";
	echo "Tempat Pembayaran : ".str_replace(" ","&nbsp;",str_pad($banknm,30,' ',STR_PAD_RIGHT)).
	     str_replace(" ","&nbsp;",str_pad("Halaman : ".number_format($hal,0,',','.'),48,' ',STR_PAD_LEFT))."<br>";
	echo "==================================================================================================<br>";
	echo "NO.    NOP                 NAMA WP                        THN.    KET. PBB      DENDA        TOTAL  <br>";
	echo "==================================================================================================<br>";
	if ($rows)
	{
		foreach ($rows as $row)
		{
			$nop = $row['kd_kecamatan'].'.'.
				$row['kd_kelurahan'].'.'.
				$row['kd_blok'].'-'.
				$row['no_urut'].'.'.
				$row['kd_jns_op'];
			$nomor +=  1;
			$nom   +=  1;
			$jml1 += $row['pbb_yg_harus_dibayar_sppt'];
			$jml2 += $row['denda_sppt'];
			$jml3 += $row['jml_sppt_yg_dibayar'];
			echo str_replace(" ","&nbsp;",str_pad(number_format($nomor,0,',','.'),6," ",STR_PAD_LEFT))."&nbsp;$nop&nbsp;".
					  str_replace(" ","&nbsp;",str_pad($row['nm_wp_sppt'],31,' ',STR_PAD_RIGHT)) .
					  $row['thn_pajak_sppt'].
					  str_replace(" ","&nbsp;",str_pad(number_format($row['pbb_yg_harus_dibayar_sppt'],0,',','.'),12,' ',STR_PAD_LEFT)) .
					  str_replace(" ","&nbsp;",str_pad(number_format($row['denda_sppt'],0,',','.'),11,' ',STR_PAD_LEFT)) .
					  str_replace(" ","&nbsp;",str_pad(number_format($row['jml_sppt_yg_dibayar'],0,',','.'),13,' ',STR_PAD_LEFT))."<br>";
			if ($nom % 50 == 0 )
			{
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				$hal+=1;
				echo str_repeat('&nbsp;',28)."DAFTAR PENERIMAAN HARIAN TANGGAL $tgl <br>";
				echo "<br>";
				echo "  Kelurahan:$kelnm, Buku:$bukunm<br>";
				echo "  Tempat Pembayaran : ".str_pad($banknm,30,"&nbsp;",STR_PAD_LEFT).str_repeat('&nbsp;',30)."Halaman : ".number_format($hal,0,',','.')."<br>";
				echo "==================================================================================================<br>";
				echo "NO.  NOP                 NAMA WP                        THN.    KET. PBB      DENDA        TOTAL  <br>";
				echo "==================================================================================================<br>";
			}
		}
	}else
	{
		echo "TIDAK ADA DATA UNTUK PARAMETER YANG DIPILIH<br>";
	}
    echo  "==================================================================================================<br>";
    echo  str_repeat('&nbsp;',7)."JUMLAH :".str_repeat('&nbsp;',46).
                  str_replace(" ","&nbsp;",str_pad(number_format($jml1,0,',','.'),12,' ',STR_PAD_LEFT)) .
                  str_replace(" ","&nbsp;",str_pad(number_format($jml2,0,',','.'),11,' ',STR_PAD_LEFT)) .
                  str_replace(" ","&nbsp;",str_pad(number_format($jml3,0,',','.'),13,' ',STR_PAD_LEFT))."<br>";
    echo  "==================================================================================================<br>";

?>
</pre>
</body>
<html>  

