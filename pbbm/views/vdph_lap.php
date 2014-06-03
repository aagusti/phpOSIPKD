<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>
<style type="text/css">@import "<?=base_url()?>assets/css/pbbm.css";</style>
</style>
<script>
var mID;
var oTable;
var xRow;

function num_thousand(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function num_clean(x) {
    return x.toString().replace( /^\D+/g, '');;
}

function reload_grid() {
	var tahun = $("#tahun").val();
	var kec_kd = $("#kd_kecamatan").val();
	var kel_kd = $("#kd_kelurahan").val();
	window.location = "<? echo active_module_url($this->uri->segment(2));?>?tahun="+ tahun + "&kec_kd=" + kec_kd +"&kel_kd=" + kel_kd ;
}

$(document).ready(function() {
	oTable = $('#table1').dataTable({
        "iDisplayLength": 100,
        "sScrollY": "320px",
        "bJQueryUI" : true,
        "bAutoWidth": true,
        "bScrollCollapse": false,
        "bLengthChange": false,
        "bPaginate": true,
        "bFilter": true,
        "sPaginationType" : "full_numbers",
        "bSort": false,
        "bInfo": true,
        "bServerSide": false,
        "bProcessing": true,
        "sAjaxSource": "<?=$data_source?>",
        "sDom":'<"toolbar">fTl<"clear">rtip',
        // "sDom": '<"H"lfr>t<"F"ip>T',
		// "sDom": '<"toolbar">frtip',
        
		"aoColumnDefs": [
			{ "bSearchable": false, "bVisible": false, "aTargets": [ 0,8,9,10 ] },
		],
		"aoColumns": [
			null,
			{ "sWidth": "140px", "sClass": "left"},
			null,
			{ "sWidth": "125px", "sClass": "left"},
			{ "sWidth": "60px", "sClass": "right"},
			{ "sWidth": "90px", "sClass": "right"},
			{ "sWidth": "60px", "sClass": "right"},
			{ "sWidth": "90px", "sClass": "center"},
            
			null,
			null,
			null,
		],
        "oTableTools": {
            "sSwfPath": "<?=base_url()?>assets/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
        },
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if(aData[0]!=xRow) {
					if ($(this).hasClass('row_selected')) {
						$(this).removeClass('row_selected');
					} else {
						oTable.$('tr.row_selected').removeClass('row_selected');
						$(this).addClass('row_selected');
					}

					var data = oTable.fnGetData( this );
					mID = data[0];
				}
				xRow = aData[0];
			})
		},
		"oLanguage": {
        			"sProcessing":   "<img border='0' src='<?=base_url('assets/img/ajax-loader-big-circle-ball.gif')?>' />",
			"sLengthMenu":   "Tampilkan _MENU_",
			// "sZeroRecords":  "Tidak ada data",
			"sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
			"sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
			"sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
			"sInfoPostFix":  "",
			"sSearch":       "Cari : ",
			"sUrl":          "",
		},
        "fnFooterCallback" : function(nRow, aaData, iStart, iEnd, aiDisplay) {
            var pokok = 0;
            var denda = 0;
            var total = 0;
            if (aaData.length > 0) {
                for ( var i = 0; i < aaData.length; i++) {
                    pokok += parseFloat(aaData[i][8]);
                    denda += parseFloat(aaData[i][9]);
                    total += parseFloat(aaData[i][10]);
                }
            }
            
            var nCells = nRow.getElementsByTagName('td');
            nCells[1].innerHTML = num_thousand(pokok);
            nCells[2].innerHTML = num_thousand(denda);
            nCells[3].innerHTML = num_thousand(total);
        },
	});
	
	var tb_array = [
		'<div class="btn-group pull-left">',
		'	<button id="btn_cetak" class="btn btn-success" type="button">Cetak</button>',
		'</div>',
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);

	$('#btn_cetak').click(function() {
		if(mID) {
            /*
			var url = '<?=active_module_url($this->uri->segment(2));?>cetak/'+mID;
			window.open(url);
            */
			var rpt = 'dph';
			var winparams = 'location=1,status=1,scrollbars=1,resizable=no,width='+screen.width+',height='+screen.height+',menubar=no,toolbar=no,fullscreen=no';
			window.open('<?=active_module_url($this->uri->segment(2));?>cetak/pdf/'+rpt+'/'+mID, 'Laporan', winparams);
		}else{
			alert('Silahkan pilih data yang akan dicetak');
		}
	});
	
	$("#btngo").click(function(){
		reload_grid();
	});
	
	$("#kd_kecamatan, #kd_kelurahan").change(function() {
        if($(this).attr('name')=='kd_kecamatan') $("#kd_kelurahan").val('000');
		reload_grid();
	});
	
	/* Init */
});
</script>

<!--buat download -->
<form id="download_form" method="post" action="" class="hide" >
	<input type="hidden" id="download" name="download" />
</form>

<div class="content">
    <div class="container-fluid">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#main_grid"><strong>DPH – Cetak File Keluaran</strong></a></li>
        </ul>
        <?=msg_block();?>
        <div class="form-horizontal">
            <div class="control-group">
                <label class="control-label">Tahun Bayar</label> 
                <div class="controls">
					<input style="width:30px;" id="tahun" name="tahun" type="text" value="<?echo isset($tahun) ? $tahun : date('Y');?>"/>
                    <button class="btn" id="btngo" name="btngo">Go</button>
                </div>
               </div>
            <div class="control-group">
				<label class="control-label">Kecamatan</label> 
                <div class="controls">
					<select id="kd_kecamatan" name="kd_kecamatan" class="input-medium" <?echo get_user_kec_kd() != '000' ? "disabled" : "";?>>
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
                    Kelurahan 
					<select id="kd_kelurahan" name="kd_kelurahan" class="input-medium">
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
        </div>
		<hr />
		
		<table class="display dataTables" id="table1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Uraian</th>
                    <th>Tanggal</th>
                    <th>Pokok</th>
                    <th>Denda</th>
                    <th>Bayar</th>
                    <th>Posting</th>
                    
                    <th>Pokok</th>
                    <th>Denda</th>
                    <th>Bayar</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">TOTAL</td>
                    <td><span id="pokok">&nbsp;</span></td>
                    <td><span id="denda">&nbsp;</span></td>
                    <td><span id="total">&nbsp;</span></td>
                    <td colspan="3">&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<? $this->load->view('_foot'); ?>