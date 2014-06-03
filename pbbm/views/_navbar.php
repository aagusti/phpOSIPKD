<style>
	@media (min-width: 979px) {
		.wekeke{
			 margin-top: -2px !important;
			 width:100%;
			 position:fixed;
		}
		.navbar-inner {
			 border: 0px !important;
			 border-radius: 0px !important;
		}
	}
	.nav-tabs {
		margin-bottom: 6px;
	}
	.content {
		padding-top: 45px;
	}
</style>

<div class="navbar navbar-inverse wekeke" style="z-index:1029; ">
    <div class="navbar-inner">
        <div class="container-fluid">
			
			<?if($this->session->userdata('login')) :?>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <!--li <?echo $current=='home' ? 'class="active"' : '';?>><a class="brand" href="<?=active_module_url();?>"><?=strtoupper(active_module());?></a></li-->
                    <li <?echo $current=='home' ? 'class="active"' : '';?>><a class="brand" href="<?=active_module_url();?>">PBB Monitoring</a></li>
                    <li class="dropdown <?echo $current=='dph' ? 'active' : '';?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">DPH<strong class="caret"></strong></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?=active_module_url();?>dph">Entri Data</a></li>
                            <li><a href="<?=active_module_url();?>dph_posting">Download dan Posting</a></li>
                            <li><a href="<?=active_module_url();?>dph_laporan">Cetak File Keluaran</a></li>
                        </ul>
                    </li>
                    
                    <li class="dropdown <?echo $current=='transaksi' ? 'active' : '';?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Transaksi <strong class="caret"></strong></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown"><a href="<?=active_module_url();?>tranmonths">Rekap Bulanan</a></li>
                            <li class="dropdown"><a href="<?=active_module_url();?>transaksi/2">Rekap Harian</a></li>    
                            <li class="dropdown"><a href="<?=active_module_url();?>transaksi/1">Rincian Harian</a></li>
                        </ul>
                    </li>

                    <li class="dropdown <?echo $current=='realisasi' ? 'active' : '';?>">
                        <a href="#"  class="dropdown-toggle" data-toggle="dropdown">Realisasi <strong class="caret"></strong></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown"><a href="<?=active_module_url();?>realisasi">Semua</a></li>
                            <li class="dropdown"><a href="<?=active_module_url();?>lb">Lebih Bayar</a></li>
                            <li class="dropdown"><a href="<?=active_module_url();?>kb">Kurang Bayar</a></li>
                        </ul>
                    </li>
                    <li class="dropdown <?echo $current=='piutang' ? 'active' : '';?>">
                        <a href="<?=active_module_url();?>piutang" class="">Piutang </strong></a>
                    </li>
                    <li class="dropdown <?echo $current=='op' ? 'active' : '';?>">
                        <a href="<?=active_module_url();?>op" class="">Objek Pajak </strong></a>
                    </li>
                    <li class="dropdown <?echo $current=='ref' ? 'active' : '';?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Referensi <strong class="caret"></strong></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown"><a href="<?=active_module_url();?>user_pbbms">User PBB</a></li>
                        </ul>
                    </li>
                  </ul>
            </div>
			<? endif; ?>
		</div>
    </div>
  </div>