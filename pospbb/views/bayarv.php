<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {
	$("#btn_cari").click(function() {
        $("#btn_bayar,#btn_cetak,#btn_cetak2,#btn_cetak3").attr('disabled', 'disabled');
                        
		var nop = $("#prefix").val()+$("#nop").val();
		var thn = $("#tahun").val();
        if (nop && thn) {
			$.ajax({
				url: "<?=active_module_url('bayar/cari/')?>"+nop+'/'+thn,
				success: function (json) {
					data = JSON.parse(json);
					if(data['found']!=0) {
						$("#nm_wp").val(data['nm_wp_sppt']);
						$("#jln_wp").val(data['jln_wp_sppt']);
						$("#rt_wp").val(data['rt_wp_sppt']);
						$("#rw_wp").val(data['rw_wp_sppt']);
						$("#lurah_wp").val(data['kelurahan_wp_sppt']);
						$("#kota_wp").val(data['kota_wp_sppt']);
						$("#npwp").val(data['npwp_sppt']);
						$("#terhutang").val(data['pbb_terhutang_sppt']);
						$("#pengurangan").val(data['faktor_pengurang_sppt']);
						$("#pembayaran").val(data['jml_sppt_yg_dibayar']);
						$("#sisa").val(data['sisa']);
						$("#jthtempo").val(data['tgl_jatuh_tempo_sppt']);
						$("#denda").val(data['denda']);
						$("#utang").val(data['utang']);
						$("#terbilang").val(data['terbilang']);
						if (data['utang']>0)
						    $("#btn_bayar").removeAttr('disabled');
						else $("#btn_bayar").attr('disabled', 'disabled');
					} else {
						$("#nm_wp").val("");
						$("#jln_wp").val("");
						$("#rt_wp").val("");
						$("#rw_wp").val("");
						$("#lurah_wp").val("");
						$("#kota_wp").val("");
						$("#npwp").val("");
						$("#terhutang").val("");
						$("#pengurangan").val("");
						$("#pembayaran").val("");
						$("#sisa").val("");
						$("#jthtempo").val("");
						$("#denda").val("");
						$("#utang").val("");
						$("#terbilang").val("");

						alert('Data tidak ditemukan');
						$("#nop").focus();
					}
				},
				error: function (xhr, desc, er) {
					alert(er);
				}
			});
		} else {
			alert ('Harap mengisi NOP dan Tahun dengan benar!');
		}
		return false;
	});

	$('#myform').submit(function() {
		var sukses='no';
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: $(this).serialize(),
			async: false,
			beforeSend: function(){
			},
			success: function(msg) {
			  data = JSON.parse(msg);
				if(data['yes']=='yes') {
					alert('Data telah disimpan.');
					$("#ke").val(data['ke']);
				} else
					alert('Data gagal disimpan.');
			}
		});
		return false;
	});

	$('#btn_bayar').click(function() {
        $('#myform').submit();
        $("#btn_cetak,#btn_cetak2,#btn_cetak3").removeAttr('disabled');
		$(this).attr('disabled', 'disabled');
	});
    
	$('#btn_cetak').click(function() {
		var nop = $("#prefix").val()+$("#nop").val();
		var thn = $("#tahun").val();
		var ke  = $("#ke").val();
		window.open("<?=active_module_url('bayar/cetak')?>"+ nop+'/'+thn+'/'+ke, "Cetak");
		$(this).attr('disabled', 'disabled');
	});

	$('#btn_cetak2').click(function() {
		var nop = $("#prefix").val()+$("#nop").val();
		var thn = $("#tahun").val();
		var ke  = $("#ke").val();
		window.open("<?=active_module_url('bayar/cetak_pdf')?>"+ nop+'/'+thn+'/'+ke, "Cetak PDF");
		$(this).attr('disabled', 'disabled');
	});
    
	$('#btn_cetak3').click(function() {
		var nop = $("#prefix").val()+$("#nop").val();
		var thn = $("#tahun").val();
		var ke  = $("#ke").val();
		window.open("<?=active_module_url('bayar/cetak_bank')?>"+ nop+'/'+thn+'/'+ke, "Cetak Bank");
		$(this).attr('disabled', 'disabled');
	});
});

$(document).keypress(function(event){
	if (event.which == '13') {
		event.preventDefault();
	}
});
</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs" id="myTab">
		  <li class="active"><a data-toggle="tab" href="#transaksi"><strong>Cetak STTS</strong></a></li>
		</ul>

		<?php
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		} ?>

		<?=msg_block();?>

		<?php echo form_open($faction, array('id'=>'myform'));?>
			<div class="row">
				<div class="span3">
					<label class="staticfont">Nomor Objek Pajak</label>
					<input class="span1" type="text" id="prefix" name="prefix" value="<?=$prefix;?>" readonly>
                    <input class="span2" type="text" id="nop" name="nop">
				</div>
				<div class="span1" style="margin-left: 5px;">
					<label class="staticfont">Tahun</label>
					<input class="span1" type="text" id="tahun" name="tahun" />
				</div>
				<div class="pull-left" style="margin-left: 5px;">
					<label class="staticfont">&nbsp;</label>
					<button class="btn btn-info" id="btn_cari" name="btn_cari">Cari</button>
                    <button type="button" class="btn btn-primary" id="btn_bayar" name="btn_bayar" disabled>Bayar</button>
                    <button type="button" class="btn btn-success" id="btn_cetak" name="btn_cetak" disabled>Cetak (Draft))</button>
                    <button type="button" class="btn btn-success" id="btn_cetak2" name="btn_cetak2" disabled>Cetak (PDF)</button>
                    <button type="button" class="btn btn-success" id="btn_cetak3" name="btn_cetak3" disabled>Cetak (BANK)</button>
				</div>
			</div>
			<hr/>

			<div class="row">
				<div class="span3">
					<label class="staticfont">Nama Wajib Pajak</label>
					<input class="span3" type="text" id="nm_wp" name="nm_wp" readonly />
				</div>
				<div class="span4" style="margin-left: 5px;">
					<label class="staticfont">Alamat Wajib Pajak</label>
					<input class="span4" type="text" id="jln_wp" name="jln_wp" readonly />
				</div>
				<div class="span1" style="margin-left: 5px;">
					<label class="staticfont">RT</label>
					<input class="span1" type="text" id="rt_wp" name="rt_wp" readonly />
				</div>
				<div class="span1" style="margin-left: 5px;">
					<label class="staticfont">RW</label>
					<input class="span1" type="text" id="rw_wp" name="rw_wp" readonly />
				</div>
			</div>

			<div class="row">
				<div class="span3">
					<label class="staticfont">Kelurahan</label>
					<input class="span3" type="text" id="lurah_wp" name="lurah_wp" readonly />
				</div>
				<div class="span3" style="margin-left: 5px;">
					<label class="staticfont">Kota</label>
					<input class="span3" type="text" id="kota_wp" name="kota_wp" readonly />
				</div>
				<div class="span2" style="margin-left: 5px;">
					<label class="staticfont">NPWP</label>
					<input class="span2" type="text" id="npwp" name="npwp" readonly />
				</div>
			</div>

			<div class="row">
				<div class="span2">
					<label class="staticfont">PBB Terhutang</label>
					<input class="span2" type="text" id="terhutang" name="terhutang" readonly />
				</div>
				<div class="span2" style="margin-left: 5px;">
					<label class="staticfont">Pengurangan</label>
					<input class="span2" type="text" id="pengurangan" name="pengurangan" readonly />
				</div>
				<div class="span3" style="margin-left: 5px;">
					<label class="staticfont">PBB Yang Sudah Dibayar</label>
					<input class="span2" type="text" id="pembayaran" name="pembayaran" readonly />
				</div>
			</div>

			<div class="row">
				<div class="span2">
					<label class="staticfont">PBB Harus Dibayar</label>
					<input class="span2" type="text" id="sisa" name="sisa" readonly />
				</div>
				<div class="span2" style="margin-left: 5px;">
					<label class="staticfont">Jatuh Tempo</label>
					<input class="span2" type="text" id="jthtempo" name="jthtempo" readonly />
				</div>
				<div class="span2" style="margin-left: 5px;">
					<label class="staticfont">Denda Administrasi</label>
					<input class="span2" type="text" id="denda" name="denda" readonly />
				</div>
			</div>

			<div class="row">
				<div class="span3">
					<label class="staticfont">PBB Yang harus di bayar</label>
					<input class="span2" type="text" id="utang" name="utang" readonly />
				</div>
			</div>
			<div class="row">
				<div class="span8">
					<label class="staticfont">Dengan Huruf</label>
					<input class="span8" type="text" id="terbilang" name="terbilang" readonly />
				</div>
			</div>
			<input type="hidden" id="ke" name="ke"/>
		</form>
    </div>


</div>
<? $this->load->view('_foot'); ?>