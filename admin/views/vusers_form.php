<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = "<?echo $this->uri->segment(2)=='users2' ? active_module_url() : active_module_url('users');?>";
	});
});
</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>USERS</strong></a>
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
				<label class="control-label">User ID</label>
				<div class="controls">
					<input class="input-small" type="text" name="userid" value="<?=$dt['userid']?>" <?echo $this->uri->segment(2)=='users2' ? 'readonly' : '';?>>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Nama</label>
				<div class="controls">
					<input class="input-xlarge" type="text" name="nama" value="<?=$dt['nama']?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Password</label>
				<div class="controls">
					<input class="input-xlarge" type="password" name="passwd" value="<?=$dt['passwd']?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">NIP</label>
				<div class="controls">
					<input class="input-medium" type="text" name="nip" value="<?=$dt['nip']?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Jabatan</label>
				<div class="controls">
					<input class="input-xxlarge" type="text" name="jabatan" value="<?=$dt['jabatan']?>">
				</div>
			</div>
			<div class="control-group <?echo $this->uri->segment(2)=='users2' ? 'hide' : '';?>">
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