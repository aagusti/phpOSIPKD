<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
var mID;
var dID;
var oTable;
var oTable2;

$(document).ready(function() {
	oTable = $('#table1').dataTable({
		/* "sScrollY": "380px", */
		"bScrollCollapse": true,
		"bPaginate": false,
		"bJQueryUI": true,
		"sDom": '<"toolbar">frtip',

		"aaSorting": [[ 0, "asc" ]],
		"aoColumnDefs": [
			{ "bSearchable": false, "bVisible": false, "aTargets": [ 0 ] }
		],
		"aoColumns": [
			null,
			{ "sWidth": "30%"},
			{ "sWidth": "70%" }
            
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					/* 
					mID = '';
					$(this).removeClass('row_selected');
					oTable2.fnReloadAjax("<?=active_module_url();?>privileges/grid/");
					 */
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0];
					dID = '';
					
					oTable.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
					
					oTable2.fnReloadAjax("<?=active_module_url();?>privileges/grid/"+$('#mod_id').val()+'/'+mID);
				}
			})
		},
		"fnInitComplete": function(oSettings, json) {
			if (!mID) $('#mod_id').trigger('change');
		},
		"bSort": true,
		"bInfo": false,
		"bFilter": false,
		"bProcessing": false,
		"sAjaxSource": "<?=active_module_url();?>groups/grid"
	});

	oTable2 = $('#table2').dataTable({
		/* "sScrollY": "380px", */
		"bScrollCollapse": true,
		"bPaginate": false,
		"bJQueryUI": true,
		"sDom": '<"toolbar2x">frtip',

		"aoColumnDefs": [
			{ "bSearchable": false, "bVisible": false, "aTargets": [ 0 ] }
		],
		"aoColumns": [
			null,
			null,
			{ "sWidth": "12%" },
			{ "sWidth": "14%" },
			{ "sWidth": "12%" },
			{ "sWidth": "12%" }
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					/* dID = '';
					$(this).removeClass('row_selected'); */
				} else {
					var data = oTable2.fnGetData( this );
					dID = data[0];
					
					oTable2.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
				}
			})
		},
		"bSort": true,
		"bInfo": false,
		"bProcessing": false,
		"bFilter": false,
		"sAjaxSource": "<?=active_module_url();?>privileges/grid/"
	});

	var tb2_array = [
		'<div class="btn-group">',
		'	<button id="btn_tambah" class="btn" type="button">Tambah</button>',
		'	<button id="btn_edit" class="btn" type="button">Edit</button>',
		'	<button id="btn_delete" class="btn" type="button">Hapus</button>',
		'</div>', 
	];
	var tb2 = tb2_array.join(' ');	
	$("div.toolbar2").html(tb2);
	
	$('#btn_tambah').click(function() {
		window.location = '<?=active_module_url();?>privileges/add/'+$('#mod_id').val();
	});

	$('#btn_edit').click(function() {
		if(dID) {
			window.location = '<?=active_module_url();?>privileges/edit/'+dID;
		}else{
			alert('Silahkan pilih data yang akan diedit');
		}
	});

	$('#btn_delete').click(function() {
		if(dID) {
			var hapus = confirm('Hapus data ini?');
			if(hapus==true) {
				window.location = '<?=active_module_url();?>privileges/delete/'+dID;
			};
		}else{
			alert('Silahkan pilih data yang akan dihapus');
		}
	});
	
	$('#mod_id').change(function() {
		if (!mID) selecttopRow();
		dID = '';
		oTable2.fnReloadAjax("<?=active_module_url();?>privileges/grid/"+$('#mod_id').val()+'/'+mID);
	});
		
	function selecttopRow() {
		var nTop = $('#table1 tbody tr')[0];
		var iPos = oTable.fnGetPosition( nTop );

		/* Use iPos to select the row */
		var data = oTable.fnGetData(iPos);
		mID = data[0];
					
		$('#table1 tbody tr:eq(0)').addClass('row_selected');
	}
});

function update_stat(gid, mid, fld, a) {
	var val = Number(a);
	$.ajax({
	  url: '<?php echo active_module_url()?>privileges/update_stat/' + gid +'/' + mid +'/' + fld + '/' + val,
	  success: function(data) {
	  }
	});
}
</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>GROUP PRIVILEGES</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>
		
		<div class="row-fluid">
			<div class="span4">
				<strong>Aplikasi : </strong><select name="mod_id" id="mod_id"><?=$app_data;?></select>
			</div>
			<div class="span4">
				<div class="toolbar2"></div>
			</div>
		</div>
		
		<div class="row-fluid">
			<div class="span4">
				<table class="table" id="table1">
					<thead>
						<tr>
							<th>Index</th>
							<th>Kode</th>
                            <th>Nama Group</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="span6">
				<table class="table" id="table2">
					<thead>
						<tr>
							<th>Index</th>
							<th>Module</th>
							<th>Baca</th>
							<th>Tambah</th>
							<th>Edit</th>
							<th>Hapus</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<? $this->load->view('_foot'); ?>