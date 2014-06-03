<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = "<?echo active_module_url('pos_tp');?>";
	});
});
</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>TEMPAT_PEMBAYARAN</strong></a>
			</li>
		</ul>
		
		<?php
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		} ?>
		
		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal','enctype'=>'multipart/form-data'));?>
			<input type="hidden" name="id" value="<?=$dt['id']?>"/>
			<div class="control-group">
				<label class="control-label">Kode</label>
				<div class="controls">
          <? if (DEF_POS_TYPE==1) {?>
                <input class="input-small" type="text" name="kd_kanwil" value="<?=$dt['kd_kanwil'];?>">
                <input class="input-small" type="text" name="kd_kantor" value="<?=$dt['kd_kantor'];?>">
                <input class="input-small" type="text" name="kd_tp" value="<?=$dt['kd_tp'];?>">
          <?} else {?>
                <input class="input-small" type="text" name="kd_bank_tunggal" value="<?=$dt['kd_bank_tunggal'];?>">
                <input class="input-small" type="text" name="kd_bank_persepsi" value="<?=$dt['kd_bank_persepsi'];?>">
                <input class="input-small" type="text" name="kd_kanwil" value="<?=$dt['kd_kanwil'];?>">
                <input class="input-small" type="text" name="kd_kppbb" value="<?=$dt['kd_kppbb'];?>">
                <input class="input-small" type="text" name="kd_tp" value="<?=$dt['kd_tp'];?>">
          <?}?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Nama</label>
				<div class="controls">
					<input class="input-xlarge" type="text" name="nm_tp" value="<?=$dt['nm_tp']?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Alamat</label>
				<div class="controls">
					<input class="input-xlarge" type="text" name="alamat_tp" value="<?=$dt['alamat_tp']?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Rekening</label>
				<div class="controls">
					<input class="input-xlarge" type="text" name="no_rek_tp" value="<?=$dt['no_rek_tp']?>">
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary">Simpan</button>
					<button type="button" class="btn" id="btn_cancel">Batal</button>
				</div>
			</div>
		</form>
    </div>
</div>
<? $this->load->view('_foot'); ?>