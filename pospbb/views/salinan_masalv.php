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

var dID;
var oTable;
var xRow;
var xRow2;
var asInitVals = new Array();

$(document).ready(function() {
	oTable = $('#table1').dataTable({
		"iDisplayLength": 10,
		"bJQueryUI" : true,
		"sScrollY": "325px",
		"bScrollCollapse": false,
		"bPaginate": false,
		"sPaginationType": "full_numbers",
		"bFilter": false,
		"bLengthChange": false,
		// "sDom": '<"top">rt<"bottom"ilp><"clear">',
		"sDom":'<"toolbar">fT<"clear">lrtip<"clear">',
		"aoColumnDefs": [
			{ "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 0 ] },
			{ "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 1 ] },
			{ "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 2 ] },
			{ "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 3 ] },
			{ "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 4 ] },
			{ "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 5 ] },
			{ "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 6 ] },
			{ "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 7 ] },

			{ "bSortable": false, "bSearchable": false, "bVisible": false, "aTargets": [ 8,9,10,11,12,13,14,15,16,17,18,19,20 ] },
		],
		"aoColumns": [
			{ "sWidth": "10%" },
			{ "sWidth": "6%" , "sClass": "center"},
			{ "sWidth": "10%", "sClass": "right"},
			{ "sWidth": "6%" , "sClass": "right"},
			{ "sWidth": "8%" , "sClass": "center"},
			{ "sWidth": "20%"},
			{ "sWidth": "25%" },
			{ "sWidth": "6%" , "sClass": "center"},
		],
        "oTableTools": {
            "sSwfPath": "<?=base_url()?>assets/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
        },
        "oLanguage": {
            "sProcessing":   "<img border='0' src='<?=base_url('assets/img/ajax-loader-big-circle-ball.gif')?>' />",
        },
		"bSort": true,
		"bInfo": true,
        "bServerSide": false,
		"bProcessing": true,
		"sAjaxSource": "<?=active_module_url();?>salinan_masal/cari/"
		/*"fnFooterCallback": function( nFoot, aData, iStart, iEnd, aiDisplay ) {
			nFoot.getElementsByTagName('th')[2].innerHTML = aData[10];
			nFoot.getElementsByTagName('th')[3].innerHTML = 10;
			nFoot.getElementsByTagName('th')[4].innerHTML = 15;
		}*/
	});

	$("div.toolbar").append($('.asd'));

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
	});
});

$(document).ready(function()
{
    var saved = null;

	$( "#tgl_awal, #tgl_akhir" ).datepicker({ dateFormat: "dd-mm-yy" });

	$('#table1 a.delete').live('click', function (e) {
		e.preventDefault();
		var nRow = $(this).parents('tr')[0];
		oTable.fnDeleteRow( nRow );
	});

	$("#btn_cari1").click(function() {
		var blok  = $("#prefix").val() +$("#blok").val();
        var blok2 = $("#blok2").val();
		var thn   = $("#tahun").val();

		if (blok && blok2 && thn) {
            $('#sw').val('nop');
		    oTable.fnReloadAjax("<?=active_module_url();?>salinan_masal/cari/nop/"+blok+'/'+blok2+'/'+thn);
		} else {
			alert ('Harap mengisi Range NOP dan Tahun dengan benar!');
		}
	});

	$("#btn_cari2").click(function() {
        var tgl1 = $("#tgl_awal").val();
        var tgl2 = $("#tgl_akhir").val();

		if (tgl1 && tgl2) {
            $('#sw').val('tgl');
		    oTable.fnReloadAjax("<?=active_module_url();?>salinan_masal/cari/tgl/"+tgl1+'/'+tgl2);
		} else {
			alert ('Harap mengisi Range Tanggal Bayar dengan benar!');
		}
	});

	$('#btn_cetak').click(function() {
        $.ajax({
            url: "<?=$faction;?>",
            type: "POST",
            data: "dtCetak=" + JSON.stringify({ "dtCetak" : oTable.fnGetData() }),
            success: function (msg) {
                if(msg!='No Data') {                    
                    var rpt = window.open("", "Cetak");
                    if (!rpt) 
                        alert('You have a popup blocker enabled. Please allow popups for this site.');
                    else 
                        $(rpt.document.body).html(msg);
                } else alert(msg);
            }
        });
	});
    
	$('#btn_cetak5').click(function() {
        $('#data').val(JSON.stringify(oTable.fnGetData()));  
        $("#rptform").attr("action", "<?echo active_module_url('salinan_masal/cetak_bank');?>"); 
        $('#rptform').submit();
	});
    
	$('#btn_cetak2').click(function() {
        $('#data').val(JSON.stringify(oTable.fnGetData()));  
        $("#rptform").attr("action", "<?echo active_module_url('salinan_masal/cetak_pdf');?>");
        $('<input type="hidden" name="sttsno"/>').val("").appendTo('#rptform');
        $('#rptform').submit();
	});
    
	$('#btn_cetak3').click(function() {
        $('#data').val(JSON.stringify(oTable.fnGetData()));  
        $("#rptform").attr("action", "<?echo active_module_url('salinan_masal/cetak_pdf');?>");
        $('<input type="hidden" name="sttsno"/>').val("2").appendTo('#rptform');
        $('#rptform').submit();
	});
    
	$('#btn_cetak4').click(function() {
        $('#data').val(JSON.stringify(oTable.fnGetData()));  
        $("#rptform").attr("action", "<?echo active_module_url('salinan_masal/cetak_pdf');?>");
        $('<input type="hidden" name="sttsno"/>').val("3").appendTo('#rptform');
        $('#rptform').submit();
	});
    
    /*
    // OLD Version - button cetak typenya submit
	$("#myform").submit(function(eventObj){
		var data = JSON.stringify({ "dtCetak" : oTable.fnGetData() });
		$('<input type="hidden" name="dtCetak"/>').val(data).appendTo('#myform');
		return true;
	});
    */
});
</script>

<!-- form untuk report pdf //  , 'target'=>'_blank')  -->
<?php echo form_open("", array('id'=>'rptform', 'target'=>'_blank', 'style'=>'display:none')); ?> 
<input type="hidden" id="data" name="data">
<?echo form_close();?> 	

<div class="content">    
	<div class="container-fluid">
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a data-toggle="tab" href="#transaksi"><strong>Salinan STTS Masal</strong></a></li>
		</ul>

		<?php
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		}
		?>

		<?=msg_block();?>

		<div class="asd pull-left">
            <button type="button" class="btn btn-success" id="btn_cetak"  name="btn_cetak"  >Cetak (Draft)</button>
            <button type="button" class="btn btn-success" id="btn_cetak2" name="btn_cetak2" >Cetak 1 (PDF)</button>	
            <button type="button" class="btn btn-success" id="btn_cetak3" name="btn_cetak3" >Cetak 2 (PDF)</button>	
            <button type="button" class="btn btn-success" id="btn_cetak4" name="btn_cetak4" >Cetak 3 (PDF)</button>	
            <button type="button" class="btn btn-success" id="btn_cetak5" name="btn_cetak5" >Cetak (Bank)</button>	
		</div>
        
		<div class="asdx form-horizontal">
			<?php 
            echo form_open($faction, array('id'=>'myform', 'style'=>'margin: 0px;'));
            //echo form_open($faction, array('id'=>'myform', 'target'=>'_new'));
            ?>
                <input type="hidden" id="sw" name="sw" value="tgl">
                <div class="row">
                    <div class="span1" style="width:70px;">
                        <span class="staticfont">Tgl. Bayar</span>
                    </div>
                    <div class="span10">
                        <input style="width:80px;" type="text" id="tgl_awal" name="tgl_awal" value="<?=$tgl_awal;?>"> s.d.
                        <input style="width:80px;" type="text" id="tgl_akhir" name="tgl_akhir" value="<?=$tgl_akhir;?>">
                        <span class="staticfont">&nbsp;</span>
                        <button type="button" class="btn btn-info" id="btn_cari2" name="btn_cari2">Cari Berd. Range Tanggal</button>
                    </div>
                </div>
                <div class="row">
                    <div class="span1" style="width:70px;">
                        <span class="staticfont">NOP Awal</span>
                    </div>
                    <div class="span10">
                        <input class="span1" type="text" id="prefix" name="prefix" value="<?=$prefix;?>" readonly>
                        <input class="span2" type="text" id="blok" name="blok"> s.d.
                        <input class="span1" type="text" id="blok2" name="blok2">
                        <span class="staticfont">Tahun</span>
                        <input class="span1" type="text" id="tahun" name="tahun" />

                        <span class="staticfont">&nbsp;</span>
                        <button type="button" class="btn btn-info" id="btn_cari1" name="btn_cari1">Cari Berd. Range NOP</button>
                    </div>
                </div>
			</form>
		</div>
        <hr>

		<table class="table" id="table1">
			<thead>
				<tr>
                    <th>NOP</th>
                    <th>Tahun</th>
                    <th>Sudah dibayar</th>
                    <th>Pmb. Ke</th>
                    <th>Tgl. Bayar</th>
                    <th>Nama WP</th>
                    <th>Alamat WP</th>
                    <th>Batal</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<?  $this->load->view('_foot'); ?>