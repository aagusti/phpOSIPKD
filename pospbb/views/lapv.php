<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>
<script>
$(function() {
	$( "#tgl" ).datepicker({
        dateFormat:'dd-mm-yy', 
        changeMonth:true, 
        changeYear:true
    });
});

$(document).ready(function() {
	$('#btn_cetak').click(function() {
        $.ajax({
            url: "<?=$faction;?>",
            type: "POST",
            data: $('#myform').serialize(),
            success: function (msg) {
                if(msg!='No Data') {
                    var rpt = window.open("", "Cetak");
                    if (!rpt) 
                        alert('You have a popup blocker enabled. Please allow popups for this site.');
                    else 
                        $(rpt.document.body).html(msg);
                } else alert(msg);
            }
        });
	});
    
	$('#btn_cetak2').click(function() {
        var data= $('#myform').serialize();
		window.open("<?=active_module_url('laporan/cetak_pdf')?>?"+ data, "Cetak PDF");
	});
    
	$('#btn_csv').click(function() {
        var url = '<?=active_module_url($this->uri->segment(2));?>csv_download';
                
        $('#myform').attr('action', url);
        $('#myform').submit();
        return false;
	});
});

</script>

<div class="content">
	<div class="container-fluid">
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a data-toggle="tab" href="#transaksi"><strong>Laporan Harian</strong></a></li>
		</ul>
		
		<?=msg_block();?>
		
		<?php echo form_open($faction, array('id'=>'myform'));?>
		    <div class="container-fluid">
				<?php
				if(validation_errors()){
					echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
					echo validation_errors('<small>','</small>');
					echo '</blockquote>';
				} ?>
			</div>

			<div class="row">
				<span class="span2">Tanggal</span><input class="input-small" type="text" id="tgl" name="tgl" value="<?=date('d-m-Y');?>">
			</div>
			<div class="row">
				<span class="span2">Buku</span>
				<select id="buku" name="buku">
					<option value="01" selected>1</option>
					<option value="02">1,2</option>
					<option value="03">1,2,3</option>
					<option value="04">1,2,3,4</option>
					<option value="05">1,2,3,4,5</option>
					<option value="06">2</option>
					<option value="07">2,3</option>
					<option value="08">2,3,4</option>
					<option value="09">2,3,4,5</option>
					<option value="10">3</option>
					<option value="11">3,4</option>
					<option value="12">3,4,5</option>
					<option value="13">4</option>
					<option value="14">4,5</option>
					<option value="15">5</option>
				</select>
			</div>
			
			<div class="row">
				<span class="span2">Urut</span>
				
				<select id="urut" name="urut">
					<option value="1" selected>Nama Wajib Pajak</option>
					<option value="2">NOP</option>
					<option value="3">Tahun</option>
					<option value="4">Jumlah Pembayaran</option>
				</select>
				
			</div>
			
			<div class="row">
				<span class="span2">Seleksi</span>
				<select id="kel" name="kel">
					<option value="000.000" selected>000.000 SELURUH KELURAHAN</option>
					<?
						foreach ($keldata as $fld)
						{
						//value=\"".$fld['kd_kecamatan'].".".$fld['kd_kelurahan']."\"
							echo "<option>".$fld['kd_kecamatan'].".".$fld['kd_kelurahan']."-".$fld['nm_kelurahan']."</option>\n";
						}
					?>
				</select>
			</div>
			
			<div class="row">
				<span class="span2">&nbsp;</span>
                <button type="button" class="btn btn-success" id="btn_cetak" name="btn_cetak">Cetak (Draft)</button>
                <button type="button" class="btn btn-success" id="btn_cetak2" name="btn_cetak2">Cetak (PDF)</button>
                <button type="button" class="btn btn-success" id="btn_csv" name="btn_csv">Download (CSV)</button>
			</div>
		</form>
	</div>
</div>
<? $this->load->view('_foot'); ?>