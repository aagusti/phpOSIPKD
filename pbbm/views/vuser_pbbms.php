<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style>
.dataTables_processing {
    top: 50%;
    border: 0;
	background: none;
}
.dataTables_wrapper .ui-toolbar {
    padding: 0px;
    border: 0;
}
div.DTTT_container {
    margin-top: 2px;
}
</style>

<script>
var mID;
var oTable;

$(document).ready(function() {
	oTable = $('#table1').dataTable({
        "bJQueryUI" : true,
		"sScrollY": "380px",
		"bScrollCollapse": true,
		"bPaginate": false,
		"sDom": '<"toolbar">frtip',

		"aoColumnDefs": [
			{ "bSearchable": false, "bVisible": false, "aTargets": [ 0 ] }
		],
		"aoColumns": [
			null,
			null,
			null,
            null,
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					/* mID = '';
					$(this).removeClass('row_selected'); */
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0];
					
					oTable.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
				}
			})
		},
		"bSort": true,
		"bInfo": false,
		"bProcessing": false,
		"sAjaxSource": "<?=active_module_url();?>user_pbbms/grid"
	});

	$("div.toolbar").html('<div class="btn-group pull-left"><button id="btn_tambah" class="btn pull-left" type="button">Tambah</button><button id="btn_edit" class="btn pull-left" type="button">Edit</button> <button id="btn_delete" class="btn pull-left" type="button">Hapus</button></div>');

	$('#btn_tambah').click(function() {
		window.location = '<?=active_module_url();?>user_pbbms/add/';
	});

	$('#btn_edit').click(function() {
		if(mID) {
			window.location = '<?=active_module_url();?>user_pbbms/edit/'+mID;
		}else{
			alert('Silahkan pilih data yang akan diedit');
		}
	});

	$('#btn_delete').click(function() {
		if(mID) {
			var hapus = confirm('Hapus data ini?');
			if(hapus==true) {
				window.location = '<?=active_module_url();?>user_pbbms/delete/'+mID;
			};
		}else{
			alert('Silahkan pilih data yang akan dihapus');
		}
	});
});

function update_unit(id, a) {
	var val = Number(a);
	$.ajax({
	  url: '<?php echo active_module_url()?>user_pbbms/update_unit/' + id + '/' + val,
	  success: function(data) {
		/*  */
	  }
	});
}

function disable_user(id, a) {
	var val = Number(a);
	$.ajax({
	  url: '<?php echo active_module_url()?>user_pbbms/disable_user/' + id + '/' + val,
	  success: function(data) {
		/*  */
	  }
	});
}
</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>USER PBBM</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>
		
		<table class="table" id="table1">
			<thead>
                <tr>
                <th>Index</th>
                <th>Nama</th>
                <th>Kecamatan</th>
                <th>Kelurahan</th>
                <!--th>Disabled</th-->
                <!--th>Tgl</th-->
                </tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<? $this->load->view('_foot'); ?>