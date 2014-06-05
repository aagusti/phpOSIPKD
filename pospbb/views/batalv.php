<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {
	$("#btn_cari").click(function() {
		var nop = $("#nop").val();
		var thn = $("#tahun").val();
		var ke = $("#ke").val();

		if (nop && thn) {
			$.ajax({
				url: "<?=active_module_url('batal/cari/')?>"+nop+'/'+thn+'/'+ke,
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
						if (data['jml_sppt_yg_dibayar']==0)
						  $("#btn_batal").attr('disabled','disabled');
					  else
						  $("#btn_batal").removeAttr('disabled');
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
						$("#btn_batal").attr('disabled', 'disabled');
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

	$('#btn_batal').click(function() {
		var nop = $("#nop").val();
		var thn = $("#tahun").val();
		var ke  = $("#ke").val();
		var sukses='no';
    if (confirm('Yakin dibatalkan'))
    {
  		$.ajax({
  			type: 'GET',
  			url: "<?=active_module_url('batal/proses/')?>"+ nop+'/'+thn+'/'+ke,
  			async: false,
  			beforeSend: function(){
  			},
  			success: function(msg) {
  				if(msg=='yes') {
  					alert('Data telah dibatalkan.');
  					$("#btn_cari").trigger('click');
  				} else
  					alert('Data gagal dibatalkan.');
  			}
  		});
  		return false;
    }

	});
});
</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a data-toggle="tab" href="#transaksi"><strong>Pembatalan STTS</strong></a></li>
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

            <div class="span2" style="margin-left: 5px;">
                <label class="staticfont">&nbsp;</label>
                <button type="button" class="btn btn-info" id="btn_cari" name="btn_cari">Cari</button>
                <button type="button" class="btn btn-primary" id="btn_batal" name="btn_batal" disabled>Batal STTS</button>
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