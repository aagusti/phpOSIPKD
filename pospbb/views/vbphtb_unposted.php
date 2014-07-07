<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script type="text/javascript">
$.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback, bStandingRedraw )
{
	if ( typeof sNewSource != 'undefined' && sNewSource != null ) {
		oSettings.sAjaxSource = sNewSource;
	}

	/* Server-side processing should just call fnDraw */
	if ( oSettings.oFeatures.bServerSide ) {
		this.fnDraw();
		return;
	}

	this.oApi._fnProcessingDisplay( oSettings, true );
	var that = this;
	var iStart = oSettings._iDisplayStart;
	var aData = [];

	this.oApi._fnServerParams( oSettings, aData );

	oSettings.fnServerData.call( oSettings.oInstance, oSettings.sAjaxSource, aData, function(json) {
		/* Clear the old information from the table */
		that.oApi._fnClearTable( oSettings );

		/* Got the data - add it to the table */
		var aData =  (oSettings.sAjaxDataProp !== "") ?
			that.oApi._fnGetObjectDataFn( oSettings.sAjaxDataProp )( json ) : json;

		for ( var i=0 ; i<aData.length ; i++ )
		{
			that.oApi._fnAddData( oSettings, aData[i] );
		}

		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();

		if ( typeof bStandingRedraw != 'undefined' && bStandingRedraw === true )
		{
			oSettings._iDisplayStart = iStart;
			that.fnDraw( false );
		}
		else
		{
			that.fnDraw();
		}

		that.oApi._fnProcessingDisplay( oSettings, false );

		/* Callback user function - for event handlers etc */
		if ( typeof fnCallback == 'function' && fnCallback != null )
		{
			fnCallback( oSettings );
		}
	}, oSettings );
};

function reload_grid() {
	oTable.fnReloadAjax("<? echo active_module_url($this->uri->segment(2)); ?>grid/");
}

function ceklik() {
    if($(".cek_sspd_id").length == $(".cek_sspd_id:checked").length) 
        $("#cek_selectall").attr("checked", "checked");
    else
        $("#cek_selectall").removeAttr("checked");
}
    
$(function(){
    $("#cek_selectall").click(function () {
          $('.cek_sspd_id').attr('checked', this.checked);
    });
 
    /*
    $(".cek_sspd_id").click(function(){
        if($(".cek_sspd_id").length == $(".cek_sspd_id:checked").length) {
            alert('a');
            $("#cek_selectall").attr("checked", "checked");
        } else {
            alert('b');
            $("#cek_selectall").removeAttr("checked");
        }
            alert('c');
 
    });
    */
});

var mID;
var oTable;
var xRow;
var canEditDelete=true;

$(document).ready(function() {    
	oTable = $('#table1').dataTable({
		"iDisplayLength": 13,
		"bJQueryUI": true,
		"bSort": true,
		"bInfo": true,
	
		"bPaginate": true,
		"bLengthChange": false,

		"sPaginationType": "full_numbers",
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 1, "desc" ]],
        
		"aoColumnDefs": [
			{ "bSearchable": false, "bVisible": false, "aTargets": [ 0, 10 ] },
			// { "bSearchable": false, "bVisible": false, "aTargets": [ 0, 9, 10 ] },
            // { "bSearchable": true, "bVisible": false, "aTargets": [ 11, 12 ] }
		],
		"aoColumns": [
            null,
            { "sWidth": "4%", "bSearchable": false, "bSortable": false},
			{ "sWidth": "8%", "bSearchable": false},
            null,
            { "sWidth": "12%" },
            { "sWidth": "6%", "sClass": "center" },
            { "sWidth": "8%", "bSearchable": false, "sClass": "right"},
            { "sWidth": "8%", "bVisible": false, "bSearchable": false, "sClass": "right"},
			{ "sWidth": "8%",  "bSearchable": false},
            null,
            null,
			{ "sWidth": "8%",  "bSearchable": false},
            { "sWidth": "8%", "bSearchable": false},
            { "sWidth": "4%", "bSearchable": false, "bSortable": false},
		],
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
			});
            $(nRow).children("td.cek_sspd_id").on("click", function(event) {
                if($(nRow).children("td.cek_sspd_id").length > 0)
                    alert('a');
                else
                    alert('b');
            });
            
		},
		"fnDrawCallback": function ( oSettings ) {
			/* Need to redo the counters if filtered or sorted */
			if ( oSettings.bSorted || oSettings.bFiltered ) 
				for ( var i=0, iLen=oSettings.aiDisplay.length ; i<iLen ; i++ )
					$('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( i+1 );
		},
		"oLanguage": {
			"sLengthMenu":   "Tampilkan _MENU_",
			"sZeroRecords":  "Tidak ada data",
			"sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
			"sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
			"sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
			"sInfoPostFix":  "",
			"sSearch":       "Cari : ",
			"sUrl":          "",
		},
		"bSort": true,
		"bInfo": true,
		"bProcessing": false,
		"sAjaxSource": "<?=active_module_url($this->uri->segment(2));?>grid"
	});
    
    var mytoolbar = '';
    var mytoolbarx = '<div class="btn-group pull-left">' +
                    '<button id="btn1" class="btn btn-primary" type="button">Mutasi</button>' +
                    '<button id="btn2" class="btn btn-primary" type="button">Pemecahan</button>' +
                    '<button id="btn3" class="btn btn-primary" type="button">Penggabungan</button>' +
                    '</div>';

	$("div.toolbar").html(mytoolbar);

	$('#btn_edit').click(function() {
		if(mID) {
            //
		}else{
			alert('Silahkan pilih data yang akan diedit');
		}
	});
});
</script>

<div class="content">
    <div class="container-fluid"> 
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#main_grid"><strong>BPHTB Unposteds</strong></a></li>
        </ul>
        <?=msg_block();?>
    
        <table class="table display" id="table1">
			<thead>
				<tr>
					<th>ID</th>
					<th>No</th>
					<th>No Daftar</th>
					<th>Nama WP</th>
					<th>Nomor OP</th>
					<th>Tahun</th>
					<th>Jumlah</th>
					<th>Bayar</th>
					<th>Tgl. Input</th>
					<th>PPAT</th>
					<th>PPAT KD</th>
					<th>Status</th>
					<th>Keterangan</th>
					<th><input type="checkbox" id="cek_selectall" /></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

<? $this->load->view('_foot'); ?>