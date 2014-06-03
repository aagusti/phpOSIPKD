<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>
<script>
    $(document).ready(function() {

      $("#kec_kd, #kel_kd, #tahun, #buku").change(function(){
         var tahun = $("#tahun").val();
         var buku = $("#buku").val();
         var kec_kd = $("#kec_kd").val();
         var kel_kd = $("#kel_kd").val();
         window.location = "<?=active_module_url().'/'?>?buku="+buku+ "&kec_kd=" + kec_kd +"&kel_kd=" + kel_kd;
    
      });
    });
</script>
<div class="content">
	<div class="container-fluid">		
		<?=msg_block();?>
  		
		
		<div class="well">
            <center>
				<div class="span12">
                    <div class="row">
                           <div class="control-group">
                                <div class="controls">
                                    Kecamatan 
                                    <select id="kec_kd" name="kec_kd" <?=($user_kec_kd!='000'?" disabled" :"")?>>
                                    <?php
                                        if ($user_kec_kd=='000')
                                           echo "<option value=\"000\">Semua</option>\n";
                                        
                                        foreach ($kecamatan as $kec) 
                                        {
                                         $selected='';
                                         if ($kec->kd_kecamatan==$kec_kd) $selected=" selected";
                                         echo "<option value=\"".$kec->kd_kecamatan ."\" $selected>".$kec->nm_kecamatan."</option>\n";
                                        }
                                        ?>
                                    </select> 
                                        Kelurahan
                                        <select id="kel_kd" name="kel_kd">
                                        <?php
                                            if ($user_kel_kd=='000')
                                                echo "<option value=\"000\">Semua</option>\n";
                                            print_r($kelurahan);
                                            foreach ($kelurahan as $kel) 
                                            {
                                              $selected='';
                                              if ($kel->kd_kelurahan==$kel_kd) $selected=" selected";
                                              echo "<option value=\"".$kel->kd_kelurahan."\" $selected>".$kel->nm_kelurahan."</option>\n";
                                            }
                                            ?>
                                        </select> 
                                        <!--Buku
                            <select id="buku" name="buku" style="width:125px;">
                            <?for ($i=1; $i<=5; $i++){
                                for ($j=$i;$j<=5;$j++){
                                    $r="";
                                    for ($k=$i;$k<=$j;$k++) $r.="$k,";
                                    $r=substr($r,0,strlen($r)-1);
                                    if ($buku=="$i$j") $selected="selected";
                                    else $selected="";
                                    echo "<option value=\"$i$j\" $selected>Buku $r</option>\n";
                                }
                                }
                                ?>
                            </select-->
                            
                                    </div>
                                </div>
                        
                    </div>
                </div>
            </center>
            
        <center>
			<div class="row">
				<div class="span12">
					<div class="row">
						<div class="span2">
							<!--img src="<?=MY_BASE_URL?>assets/img/logo.png" alt="logo" style="height:280px;"-->
							&nbsp;
						</div>
						
						<div class="span8">
							<center>
								<h3>Penerimaan Pembayaran PBB</h3>
								<h3>Tahun <?=$tahun;?></h3>
							</center>
							
							<div class="row">
								<div class="span4">
									<div class="alert alert-success">
										<h4><u>Hari ini</u></h4>
										<h2>Rp. <?=$amt_daily;?></h2>
									</div>				
								</div>
								<div class="span4">
									<div class="alert alert-info">
										<h4><u>Minggu ini</u></h4>
										<h2>Rp. <?=$amt_weekly;?></h2>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="span4">
									<div class="alert alert-warning">
										<h4><u>Bulan ini</u></h4>
										<h2>Rp. <?=$amt_monthly;?></h2>
									</div>				
								</div>
								<div class="span4">
									<div class="alert alert-error">
										<h4><u>Tahun ini</u></h4>
										<h2>Rp. <?=$amt_yearly;?></h2>
									</div>
								</div>
							</div>
						</div>
                    </div>
                    
					<div class="row">
						<div class="span2">
							<!--img src="<?=MY_BASE_URL?>assets/img/logo.png" alt="logo" style="height:280px;"-->
							&nbsp;
						</div>
                        <div class="span8">
							<center>
								<h3>Kelompok</h3>
							</center>
							
							<div class="row">
								<div class="span4">
									<div class="alert alert-error">
										<h4><u>Ketetapan <?=$tahun;?></u></h4>
										<h2>Rp. <?=$tetap;?></h2>
									</div>
								</div>

								<div class="span4">
									<div class="alert alert-success">
										<h4><u>Realisasi Pokok <?=$tahun;?></u></h4>
										<h2>Rp. <?=$pokok;?></h2>
									</div>				
								</div>
							</div>
							<div class="row">
								<div class="span4">
									<div class="alert alert-info">
										<h4><u>Realisasi Piutang</u></h4>
										<h2>Rp. <?=$piutang;?></h2>
									</div>
								</div>
								<div class="span4">
									<div class="alert alert-warning">
										<h4><u>Realisasi Denda</u></h4>
										<h2>Rp. <?=$denda;?></h2>
									</div>				
								</div>
							</div>
						</div>
                        
						
					</div>
				</div>
			</div>
            </center>
		</div>
	</div>
	
</div>
<? $this->load->view('_foot'); ?>
