<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {
	$("#btn_cari").click(function() {		
        $("#btn_cetak, #btn_cetak2, #btn_cetak3").attr('disabled', 'disabled');
                        
		var nop = $("#nop").val();
		var thn = $("#tahun").val();
		var ke = $("#ke").val();
		
		if (nop && thn) {
			$.ajax({
				url: "<?=active_module_url('salinan/cari')?>"+nop+'/'+thn+'/'+ke,
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
						$("#pembayaran").val(0);
						$("#sisa").val(data['pbb_terhutang_sppt']);
						$("#jthtempo").val(data['tgl_jatuh_tempo_sppt']);
						$("#denda").val(data['denda_sppt']);
						$("#utang").val(data['jml_sppt_yg_dibayar']);
						$("#terbilang").val(data['terbilang']);
						$("#btn_cetak, #btn_cetak2, #btn_cetak3").removeAttr('disabled');
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
	});
	
	$('#btn_cetak').click(function() {
		var nop = $("#nop").val();
		var thn = $("#tahun").val();	 
		var ke  = $("#ke").val(); 
		window.open("<?=active_module_url('salinan/cetak/')?>"+ nop+'/'+thn+'/'+ke, "Cetak");
	});
    
	$('#btn_cetak2').click(function() {
		var nop = $("#nop").val();
		var thn = $("#tahun").val();	 
		var ke  = $("#ke").val(); 
		window.open("<?=active_module_url('salinan/cetak_pdf')?>"+ nop+'/'+thn+'/'+ke, "Cetak PDF");
	});
    
	$('#btn_cetak3').click(function() {
		var nop = $("#nop").val();
		var thn = $("#tahun").val();	 
		var ke  = $("#ke").val(); 
		window.open("<?=active_module_url('salinan/cetak_bank')?>"+ nop+'/'+thn+'/'+ke, "Cetak Bank");
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
			<li class="active"><a data-toggle="tab" href="#transaksi"><strong>Salinan STTS</strong></a></li>
		</ul>
		
		<?=msg_block();?>
		
			<div class="row">
				<div class="span3">
					<label class="staticfont">Nomor Objek Pajak</label>
					<input class="span3" type="text" id="nop" name="nop">
				</div>
				<div class="span1" style="margin-left: 5px;">
					<label class="staticfont">Tahun</label>
					<input class="span1" type="text" id="tahun" name="tahun" />
				</div>
				
				<div class="span1" style="margin-left: 5px;">
					<label class="staticfont">Ke</label>
					<input class="span1" type="text" id="ke" name="ke" />
				</div>
				
				<div class="pull-left" style="margin-left: 5px;">
					<label class="staticfont">&nbsp;</label>
					<button type="button" class="btn btn-info" id="btn_cari" name="btn_cari">Cari</button>	
                    <button type="button" class="btn btn-success" id="btn_cetak" name="btn_cetak" disabled>Cetak (Draft)</button>
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
			
        
    </div>


</div>
<? $this->load->view('_foot'); ?>