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
                    <li <?echo $current=='beranda' ? 'class="active"' : '';?>><a class="brand" href="<?=active_module_url();?>"><?=strtoupper(active_module());?></a></li>
                    <?if ((isset($tpnm) && $tpnm) || (is_super_admin())) :?>
                    
                    <li class="dropdown <?echo $current=='stts' ? 'active' : '';?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">STTS <strong class="caret"></strong></a>
                        <ul class="dropdown-menu">
                            <!--li><a href="<?=active_module_url();?>status">Status Pembayaran</a></li-->
                            <li><a href="<?=active_module_url();?>op">Status Pembayaran</a></li>
                            <li><a href="<?=active_module_url();?>bayar">Cetak STTS</a></li>
                            <li><a href="<?=active_module_url();?>range">Cetak STTS - Per Range</a></li>
                            <li><a href="<?=active_module_url();?>blok">Cetak STTS - Per Blok</a></li>
                            <li><a href="<?=active_module_url();?>upload_nop">Cetak STTS - Upload NOP</a></li>
                            <li><a href="<?=active_module_url();?>salinan">Salinan STTS</a></li>
                            <li><a href="<?=active_module_url();?>salinan_masal">Salinan STTS Masal</a></li>
                            <li><a href="<?=active_module_url();?>batal">Pembatalan STTS</a></li>
                        </ul>
                    </li>
                    
                    <? if($this->session->userdata('groupkd')=='posspv' || is_super_admin()) : ?>
                    <li class="dropdown <?echo $current=='transaksi' ? 'active' : '';?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Transaksi <strong class="caret"></strong></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown"><a href="<?=active_module_url();?>tranmonths">Rekap Bulanan</a></li>
                            <li class="dropdown"><a href="<?=active_module_url();?>transaksi/2">Rekap Harian</a></li>    
                            <li class="dropdown"><a href="<?=active_module_url();?>transaksi/1">Rincian Harian</a></li>
                        </ul>
                    </li>
                    <? endif; ?>
                    
                    <li class="dropdown <?echo $current=='laporan' ? 'active' : '';?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Laporan<strong class="caret"></strong></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?=active_module_url();?>laporan">Harian</a></li>
                            <!--li><a href="<?=active_module_url();?>laporan/bulanan">Bulanan</a></li-->
                        </ul>
                    </li>
                    
                    <?if (is_super_admin()) :?>
                    <li class="dropdown <?echo $current=='master' ? 'active' : '';?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Users<strong class="caret"></strong></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?=active_module_url('pos_user');?>">POSPBB Users</a></li>
                        </ul>
                    </li>
                    <?endif;?>
                    <?endif;?>
                    
                    <li class="">
                        <a href="#"><span class="label-important" style="padding:3px;"><strong><?echo !empty($tpnm) ? "TP. ".$tpnm : 'TP Anda Tidak Valid';?></strong></span></a>
                    </li>
                    
                </ul>
            </div>
			<? endif; ?>
      </div>
    </div>
  </div>