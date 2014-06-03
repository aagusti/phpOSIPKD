<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>apps';
	});
});
</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>APLIKASI</strong></a>
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
				<label class="control-label">Aplikasi</label>
				<div class="controls">
					<input class="input-xlarge" type="text" name="nama" value="<?=$dt['nama']?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Direktori</label>
				<div class="controls">
					<input class="input-xlarge" type="text" name="app_path" value="<?=$dt['app_path']?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Disabled</label>
				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" name="disabled" <?=$dt['disabled']?>>
					</label>
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