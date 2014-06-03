<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
var mID;
var oTable;

$(document).ready(function() {
	oTable = $('#table1').dataTable({
		/* "sScrollY": "380px", */
		/* "iDisplayLength": 100, */
		"bScrollCollapse": true,
		"bJQueryUI": true,
		"bPaginate": false,
		/* "sPaginationType": "full_numbers", */
		"sDom": '<"toolbar">frtip',

		"aoColumnDefs": [
			{ "bSearchable": false, "bVisible": false, "aTargets": [ 0 ] }
		],
		"aoColumns": [
			null,
			null,
			{ "sWidth": "20%" },
			{ "sWidth": "10%" ,"sClass": "center"}
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
		"bProcessing": true,
        "bFilter": true,
        "bAutoWidth": false,
		"sAjaxSource": "<?=active_module_url();?>apps/grid"
	});

	var tb_array = [
		'<div class="btn-group pull-left">',
		'	<button id="btn_tambah" class="btn pull-left" type="button">Tambah</button>',
		'	<button id="btn_edit" class="btn pull-left" type="button">Edit</button>',
		'	<button id="btn_delete" class="btn pull-left" type="button">Hapus</button>',
		'</div>',
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);
	
	$('#btn_tambah').click(function() {
		window.location = '<?=active_module_url();?>apps/add/';
	});

	$('#btn_edit').click(function() {
		if(mID) {
			window.location = '<?=active_module_url();?>apps/edit/'+mID;
		}else{
			alert('Silahkan pilih data yang akan diedit');
		}
	});

	$('#btn_delete').click(function() {
		if(mID) {
			var hapus = confirm('Hapus data ini?');
			if(hapus==true) {
				window.location = '<?=active_module_url();?>apps/delete/'+mID;
			};
		}else{
			alert('Silahkan pilih data yang akan dihapus');
		}
	});
});

function update_stat(id, a) {
	var val = Number(a);
	$.ajax({
	  url: '<?php echo active_module_url()?>apps/update_stat/' + id + '/' + val,
	  success: function(data) {
		$('#app_id').html(data);
	  }
	});
}
</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>APLIKASI</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>
		
		<div class="row-fluid">
			<!--div class="span4"-->
				<table class="table" id="table1">
					<thead>
						<tr>
							<th>Index</th>
							<th>Nama Aplikasi</th>
							<th>Direktori</th>
							<th>Disabled</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			<!--/div-->
		</div>
	</div>
</div>
<? $this->load->view('_foot'); ?>