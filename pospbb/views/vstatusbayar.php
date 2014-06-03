<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
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

var mID;
var dID;
var oTable;
var oTable2;
var xRow;
var xRow2;
var asInitVals = new Array();
$(document).ready(function() {
	oTable = $('#table1').dataTable({
		"iDisplayLength": 15,
		"bJQueryUI" : true, 
		// "sScrollY": "100px",
		//"bScrollCollapse": true,
		//"bPaginate": true,
		"sPaginationType": "full_numbers",
		"bFilter": true,
		"bLengthChange": false,
		// "sDom": '<"top">rt<"bottom"ilp><"clear">', 
		"sDom":'fT<"clear">lrtip<"clear">',
		"aoColumnDefs": [
			{ "bSortable": true, "bSearchable": true, "bVisible": true, "aTargets": [ 0 ] },
			{ "bSortable": true, "bSearchable": true, "bVisible": true, "aTargets": [ 1 ] },
			{ "bSortable": true, "bSearchable": true, "bVisible": true, "aTargets": [ 2 ] },
			{ "bSortable": true, "bSearchable": true, "bVisible": true, "aTargets": [ 3 ] },
			{ "bSortable": true, "bSearchable": true, "bVisible": true, "aTargets": [ 4 ] }
		],
		"aoColumns": [
			{ "sWidth": "20%" },
			{ "sWidth": "4%" },
			{ "sWidth": ""},
			{ "sWidth": "10%", "sClass": "right" },
			{ "sWidth": "10%" }

      ],
        "oTableTools": {
            "sSwfPath": "<?=base_url()?>assets/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
        },
        "oLanguage": {
            "sProcessing":   "<img border='0' src='<?=base_url('assets/img/ajax-loader-big-circle-ball.gif')?>' />",
        },
    /*
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
					oTable2.fnReloadAjax("<?=active_module_url();?>status/grid_pmb/"+mID);
				}
				xRow = aData[0];
			})
		},*/
    
		"bSort": true,
		"bInfo": true,
    "bServerSide": true,
		"bProcessing": true,
		"sAjaxSource": "<?=active_module_url();?>status/grid/<?=$filter;?>"
	});

	oTable2 = $('#table2').dataTable({
		//"sScrollY": "100px",
		//"bScrollCollapse": true,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": false,
		"aoColumnDefs": [
			{ "bSearchable": true, "bVisible": true, "aTargets": [ 0 ] }
		],
		"aoColumns": [
			{ "sWidth": "6%" },
			{ "sClass": "right" },
			null
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if(aData[0]!=xRow2) {
					if ($(this).hasClass('row_selected')) {
						$(this).removeClass('row_selected');
					} else {
						oTable2.$('tr.row_selected').removeClass('row_selected');
						$(this).addClass('row_selected');
					}
				}
				xRow2 = aData[0];

				var data = oTable2.fnGetData( this );
				dID = data[0];
			})
		},

		"bSort": false,
		"bInfo": false,
		"bProcessing": false,
		"sAjaxSource": "<?=active_module_url();?>status/grid_pmb/"
	});
	

	$("thead input").keypress(function(event) {
      if ( event.which == 13 ) {
          oTable.fnFilter( this.value, $("thead input").index(this) );
      }});
      		
	/*
	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
	 * the footer
	 */
	$("thead input").each( function (i) {
		asInitVals[i] = this.value;
	} );

	
	
	$("thead input").focus( function () {
		if ( this.className == "search_init" )
		{
			this.className = "";
			this.value = "";
		}
	} );
	

	$("thead input").blur( function (i) {
		if ( this.value == "" )
		{
			this.className = "search_init";
			this.value = asInitVals[$("thead input").index(this)];
		}
	} );
});

</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs" id="myTab">
		  <li class="active"><a data-toggle="tab" href="#transaksi"><strong>Status Pembayaran</strong></a></li>
		</ul>
		
		<table class="table" id="table1">
			<thead>
				<tr>
					<th>NOP</th>
					<th>Tahun</th>
					<th>Nama Wajib Pajak</th>	
					<th>Jml. PBB</th>
          <th>Status Bayar</th>
          
				</tr>
			</thead>
			<thead>
			  <tr>
			    <th>
			      <input class="search_init" type="text" value="NOP" name="nop">
			    </th>
			    <th>
			      <input class="search_init" type="text" value="Tahun" name="tahun">
			    </th>
			    <th>
			      <input class="search_init" type="text" value="Nama" name="nama_wp_sppt">
			    </th>
			    <th>
			      &nbsp;
			    </th>
          <th>&nbsp;
			    </th>

			  </tr>
			</thead>  			
			<tbody>
			</tbody>

		</table>
	</div>
    <!--div class="container-fluid">
		<div class="page-header" style="margin-bottom:8px;">
			<strong>:: Pembayaran</strong>
		</div>
		<table class="table" id="table2" width="300px" align="left">
			<thead>
				<tr>
					<th>Tahun</th>
					<th>PBB Harus Dibayar</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div-->
</div>
<? $this->load->view('_foot'); ?>