<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
var oTableDtl;

$(document).ready(function() {
	oTableDtl = $('#tableDtl').dataTable({
		"aoColumnDefs": [			
			{ "aTargets": [7,8,9,10,11,12,13,14,15,16,17], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [3,4,5], "bSearchable": false, "bVisible": true, "sWidth": "", "sClass": "right" },
		],
		"iDisplayLength": 99,	
		// "bJQueryUI": true,
		"bFilter": false,
		"bInfo": false,
		"sDom": '<"toolbar">frtip',
		"sAjaxSource": "<? echo active_module_url($this->uri->segment(2)).'grid_detail/'.$dt['id']; ?>",
	});
	
	$('#btn_cancel').click(function() {
		var kec_kd = $("#kd_kecamatan").val();
		var kel_kd = $("#kd_kelurahan").val();
		window.location = '<?echo active_module_url($this->uri->segment(2));?>?kec_kd=' + kec_kd +'&kel_kd=' + kel_kd;
	});
	
	$( "#tgl_bayar" ).datepicker({
		dateFormat:'dd-mm-yy', 
		changeMonth:true, 
		changeYear:true
	});
	
	$("#kd_kecamatan").change(function() {
		var kec_kd = $("#kd_kecamatan").val();
		$.ajax({
			url: "<?php echo active_module_url($this->uri->segment(2))?>get_kelurahan/"+kec_kd,
			success: function (data) {
				var select = $('#kd_kelurahan');
				select.html(data);
				
				$("#kd_kelurahan").trigger('change');
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
	});
	
	$("#kd_kelurahan").change(function() {
		var kec_kd = $("#kd_kecamatan").val();
		var kel_kd = $("#kd_kelurahan").val();
		$.ajax({
			url: "<?php echo active_module_url($this->uri->segment(2))?>get_pejabat/"+kec_kd+"/"+kel_kd,
			success: function (data) {
				var select = $('#pejabat1_id');
				select.html(data);
				var select = $('#pejabat2_id');
				select.html(data);
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
	});
	
	$('#btn_dth_new').click( function (e) {
		e.preventDefault();
		
		var pd = $('#propdati').val();
		var r1 = $('#range1').val();
		var r2 = $('#range2').val();
		var th = $('#tahun1').val();
		var is_nop = 0;
		
		if (r1=='' || th=='') {
			alert ('Harap isi kolom Range NOP/Blok dan Tahun dengan benar!'); 
			$("#range1").focus();
			return;
		}
			
		// if (!(pd.length+r1.length==22 || pd.length+r1.length==17)) {
		if (!(pd.length+r1.length==24 || pd.length+r1.length==17)) {
			alert ('Range NOP/Blok data tidak benar!'); 
			$("#range1").focus();
			return;
		} else {
			if (pd.length+r1.length==24) is_nop = 1;
		}
		
		$.ajax({
			url: "<?php echo active_module_url($this->uri->segment(2))?>get_nop_blok/"+th+"/"+is_nop+"/"+pd+r1+"/"+r2,
			async: false,
			success: function (j) {
				if (j==false) {
					alert('Data SPPT tidak ditemukan.');
					$("#range1").focus();
					return;
				}
				
				var data = $.parseJSON(j);
				$.each(data, function(i, val){
					var rows = oTableDtl.fnGetNodes();
					for(var i=0;i<rows.length;i++)
						if ($(rows[i]).find("td:eq(0)").html()==val['nop_thn']) return true;; 
					
					var c;
					$.ajax({
						url: "<?php echo active_module_url($this->uri->segment(2))?>cek_nop_thn/"+val['nop_thn'],
						async: false,
						success: function (ret) {
							c=ret;
						}
					});
					if (c=='ada') return true;
					
					var aiNew = oTableDtl.fnAddData( [ 
						val['nop_thn'], 
						val['pemilik'], 
						val['tanggal'], 
						val['pokok'], 
						val['denda1'], 
						val['bayar'],
						'<a class="delete" href="">Hapus</a>',

						val['kd_kecamatan'],
						val['kd_kelurahan'],
						val['kd_blok'],
						val['no_urut'],
						val['kd_jns_op'],
						val['thn_pajak_sppt'],
						val['pembayaran_ke'],
						val['denda'],
						val['jml_yg_dibayar'],
						val['tgl_rekam_byr'],
						val['nip_rekam_byr'],
					] );
					var nRow = oTableDtl.fnGetNodes( aiNew[0] );
					
				});
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
		$("#range1").focus();
	});
		
	$('#tableDtl a.delete').live('click', function (e) {
		e.preventDefault();

		var nRow = $(this).parents('tr')[0];
		oTableDtl.fnDeleteRow( nRow );
	});
	
	$("#range1").focus(function(e) {
		// e.preventDefault();
	});
	
	$("#range1").keyup(function(e) {
		$("#range2").val($(this).val());
		if (e.which == '13' && $(this).val() != '' && $(this).is(":focus")) {
			e.preventDefault();
			$("#range2").focus();
		}
	});
	
	$("#range2").keypress(function(e) {
		if (e.which == '13' && $(this).val() != '') {
			e.preventDefault();
			$("#tahun1").focus();
		}
	});
	/* 
	$("#tahun1").keypress(function(e) {
		e.preventDefault();
		if (e.which == '13' && $(this).val() != '') {
			$("#btn_dth_new").trigger('click');
		}
	});
	 */
	var keckel_change = function() {
		var pd = $('#propdati').val();
		var kc = $('#kd_kecamatan').val();
		var kl = $('#kd_kelurahan').val();
		var new_val = pd.substr(0,6) + kc + '.' + kl + '.';
		$("#propdati").val( new_val );
	}
	
	$("#kd_kecamatan, #kd_kelurahan").change(keckel_change).keypress(keckel_change);
	
	$("#myform").submit(function(eventObj){
		if ($('#nama').val()=='' || $('#tgl_bayar').val()==''  || $('#pejabat1_id').val()==''  || $('#pejabat2_id').val()=='' ) {
			alert('Harap melengkapi isian data!');
			return false;
		}
		if ($('#kd_kecamatan').val()=='000' || $('#kd_kelurahan').val()=='000' ) {
			alert('Silahkan pilih data kecamatan/kelurahan!');
			return false;
		}
        $('#kd_kecamatan').removeAttr('disabled');
        $('#kd_kelurahan').removeAttr('disabled');
		
		var data = JSON.stringify({ "dtDetail" : oTableDtl.fnGetData() });
		$('<input type="hidden" name="dtDetail"/>').val(data).appendTo('#myform');
		return true;
	});
});

$(document).keypress(function(e){
	if (e.which == '13') {
		e.preventDefault();
	}
});
</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong><? echo $this->uri->segment(2)=='dph' ? 'DPH - Entri Data' : 'DPH - Entri Data'; ?></strong></a>
			</li>
		</ul>
		
		<?php
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		} ?>
		
		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal'));?>
			<input type="hidden" name="id" value="<?=$dt['id']?>"/>
			
			<div class="row">
				<div class="span4">
					<div class="control-group">
						<label class="control-label" for="kode">No. Urut</label>
						<div class="controls">
							<input style="width:70px;" type="text" name="tahun" id="tahun" value="<?=!empty($dt['tahun']) ? $dt['tahun'] : date('Y'); ?>" readonly />
							<input style="width:70px;" type="text" name="kode" id="kode" value="<?=$dt['kode']?>" readonly />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="kd_kecamatan">Kecamatan</label>
						<div class="controls">
							<select id="kd_kecamatan" name="kd_kecamatan" <?echo get_user_kec_kd() != '000' ? "disabled" : "";?> <?echo $this->uri->segment(3)=='add' ? 'autofocus' : '' ?>>
								<?php
								if (get_user_kec_kd() == '000') echo "<option value=\"000\">Semua</option>\n";

								foreach ($kecamatan as $kec) 
								{
									$selected='';
									if ($kec->kd_kecamatan==$kec_kd) $selected=" selected";
									echo "<option value=\"".$kec->kd_kecamatan ."\" $selected>".$kec->nm_kecamatan."</option>\n";
								}
								?>
							</select> 
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="kd_kelurahan">Kelurahan</label>
						<div class="controls">
							<select id="kd_kelurahan" name="kd_kelurahan">
								<?php
								if (get_user_kel_kd() == '000') echo "<option value=\"000\">Semua</option>\n";
								foreach ($kelurahan as $kel) 
								{
									$selected='';
									if ($kel->kd_kelurahan==$kel_kd) $selected=" selected";
									echo "<option value=\"".$kel->kd_kelurahan."\" $selected>".$kel->nm_kelurahan."</option>\n";
								}
								?>
							</select> 
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="nama">Uraian</label>
						<div class="controls">
							<input class="input" type="text" name="nama" id="nama" value="<?=$dt['nama']?>" />
						</div>
					</div>
				</div>
				
				<div class="span4">
					<div class="control-group">
						<label class="control-label" for="tgl_bayar">&nbsp;</label>
					</div>
					<div class="control-group">
						<label class="control-label" for="tgl_bayar">Tanggal</label>
						<div class="controls">
							<input style="width:70px;" type="text" name="tgl_bayar" id="tgl_bayar" value="<?=!empty($dt['tgl_bayar']) ? $dt['tgl_bayar'] : date('d-m-Y');?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Penanggungjawab</label>
						<div class="controls">
							<select id="pejabat1_id" name="pejabat1_id" class="input-medium">
							<?php
//
								foreach ($users as $r) 
								{
									$selected='';
									if ($r->id==$dt['pejabat1_id']) $selected=" selected";
									echo "<option value=\"".$r->id ."\" $selected>".$r->nama."</option>\n";
								}
								?>
							</select> 
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Petugas</label>
						<div class="controls">
							<select id="pejabat2_id" name="pejabat2_id" class="input-medium">
							<?php
								foreach ($users as $r) 
								{
									$selected='';
									if ($r->id==$dt['pejabat2_id']) $selected=" selected";
									echo "<option value=\"".$r->id ."\" $selected>".$r->nama."</option>\n";
								}
								?>
							</select> 
						</div>
					</div>
				</div>
			</div>

			<!--- Detail -->
			<div class="tabbable">
				<ul id="myTab" class="nav nav-tabs">
					<li class="active"><a href="#detail" data-toggle="tab"><strong>Data Detail</strong></a></li>
				</ul>
				
				<div class="tab-content">
					<div class="tab-pane fade in active" id="detail">
					<div class="control-group">
						<label class="control-label" for="nama">Range NOP/Blok</label>
						<div class="controls">
							<input style="width:100px;" type="text" name="propdati" id="propdati" value="<?=KD_PROPINSI.".".KD_DATI2.".".$kec_kd.".".$kel_kd.".";?>" readonly />
							<input style="width:70px;" type="text" name="range1" id="range1" value="" placeholder='blok.no_urut' <?echo $this->uri->segment(3)=='edit' ? 'autofocus' : '' ?> /> s.d 
							<input style="width:70px;" type="text" name="range2" id="range2" value="" placeholder='blok.no_urut' /> Tahun 
							<input style="width:40px;" type="text" name="tahun1" id="tahun1" value="<?=date('Y');?>" />
							<button type="button" class="btn btn-success" id="btn_dth_new">Tambahkan ke Daftar</button>
							<span id="info_nop" class="label label-info hide">Info NOP</span>
						</div>
					</div>
					<div class="row">
						<div class="span10">
							<table class="table" id="tableDtl">
								<thead>
									<tr>
										<th>NOP - Tahun</th>
										<th>Pemilik</th>
										<th>Tgl Jth Tempo</th>
										<th>Pokok</th>
										<th>Denda</th>
										<th>Bayar</th>
										<th>Batal</th>
										
										<th>d1</th>
										<th>d2</th>
										<th>d3</th>
										<th>d4</th>
										<th>d5</th>
										<th>d6</th>
										<th>d7</th>
										<th>d8</th>
										<th>d9</th>
										<th>d10</th>
										<th>d11</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<button type="submit" class="btn btn-primary">Simpan</button>
			<button type="button" class="btn" id="btn_cancel">Batal</button>
		<?php echo form_close();?>
    </div>
<? $this->load->view('_foot'); ?>