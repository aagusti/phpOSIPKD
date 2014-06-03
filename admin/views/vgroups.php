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
		"sDom": '<"toolbarx">frtip',

		"aoColumnDefs": [
			{ "bSearchable": false, "bVisible": false, "aTargets": [ 0 ]
            }
		],
		"aoColumns": [
			null,
			{ "sWidth": "20%"},
			{ "sWidth": "80%" }
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					/* mID = '';
					$(this).removeClass('row_selected');
					oTable2.fnReloadAjax("<?=active_module_url();?>users/grid2/"); */
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0];
					dID = '';
					
					oTable.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
					
					if($('#in_group').is(':checked')) {
						oTable2.fnReloadAjax("<?=active_module_url();?>users/grid2/"+mID+"/true");
					} else {
						oTable2.fnReloadAjax("<?=active_module_url();?>users/grid2/"+mID);
					}
				}
			})
		},
		"fnInitComplete": function(oSettings, json) {
			if (!mID) selecttopRow();
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
		"aaSorting": [[ 2, "asc" ]],
		"aoColumns": [
			null,
			{ "sWidth": "4%",  "sClass": "center"},
			{ "sWidth": "10%" },
			{ "sWidth": "20%" },
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
		"sAjaxSource": "<?=active_module_url();?>users/grid2/"
	});

	$("div.toolbar").html('<div class="btn-group"><button id="btn_tambah" class="btn" type="button">Tambah</button><button id="btn_edit" class="btn" type="button">Edit</button> <button id="btn_delete" class="btn" type="button">Hapus</button></div>');
	$("div.toolbar").attr('style', 'display:block; float: left; margin-bottom:6px; line-height:16px;');
	
	var tb2_array = [
		'<div class="btn-group pull-left">',
		'   <label class="checkbox"><input type="checkbox" id="in_group"> Show In-Group Only</label>',
		'</div>',
	];
	var tb2 = tb2_array.join(' ');	
	$("div.toolbar2").html(tb2);
	$("div.toolbar2").attr('style', 'display:block; float: left; margin-bottom:6px; line-height:16px;');
	
	$('#in_group').click(function() {
		if(mID) {
			if($('#in_group').is(':checked')) {
				oTable2.fnReloadAjax("<?=active_module_url();?>users/grid2/"+mID+"/true");
			}else{
				oTable2.fnReloadAjax("<?=active_module_url();?>users/grid2/"+mID);
			}
		}
	});
	
	$('#btn_tambah').click(function() {
		window.location = '<?=active_module_url();?>groups/add/';
	});

	$('#btn_edit').click(function() {
		if(mID) {
			window.location = '<?=active_module_url();?>groups/edit/'+mID;
		}else{
			alert('Silahkan pilih data yang akan diedit');
		}
	});

	$('#btn_delete').click(function() {
		if(mID) {
			var hapus = confirm('Hapus data ini?');
			if(hapus==true) {
				window.location = '<?=active_module_url();?>groups/delete/'+mID;
			};
		}else{
			alert('Silahkan pilih data yang akan dihapus');
		}
	});
	
	function selecttopRow() {
		var nTop = $('#table1 tbody tr')[0];
		var iPos = oTable.fnGetPosition( nTop );

		/* Use iPos to select the row */
		var data = oTable.fnGetData(iPos);
		mID = data[0];
		dID = '';
					
		$('#table1 tbody tr:eq(0)').addClass('row_selected');
		
		if($('#in_group').is(':checked')) {
			oTable2.fnReloadAjax("<?=active_module_url();?>users/grid2/"+mID+"/true");
		} else {
			oTable2.fnReloadAjax("<?=active_module_url();?>users/grid2/"+mID);
		}
	}
});

function update_stat(gid, id, a) {
	var val = Number(a);
	$.ajax({
	  url: '<?php echo active_module_url()?>users/update_stat/' + gid +'/' + id + '/' + val,
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
				<a href="#"><strong>GROUPS USER</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>
		
		<div class="row-fluid">
			<div class="span4">
				<div class="toolbar"></div>
			</div>
			<div class="span6">
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
							<th>Nama</th>
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
							<th>In Group</th>
							<th>User ID</th>
							<th>Nama</th>
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