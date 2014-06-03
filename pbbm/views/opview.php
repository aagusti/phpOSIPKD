<? $this->load->view('_head'); ?>
<? $this->load->view('_navbar'); ?>
<style>
.page-header {
    padding: 4px;
    background-color: #D5D5D5;
    margin: 8px 0px 0px 0px;
    border-bottom: 1px solid #000000;
}
</style>
<style type="text/css">@import "<?=base_url()?>assets/css/pbbm.css";</style>
<script>
$(document).ready(function() {
        var oTable = $('#datatable').dataTable( {
        "iDisplayLength": 0,
        "sScrollY": "140px",
        "bJQueryUI" : true,
        "bAutoWidth": false,
        "bScrollCollapse": true,
        "bLengthChange": false,
        "bPaginate": false,
        "sPaginationType" : "full_numbers",
        "bFilter": false,
        "bLengthChange": false,
        "sDom": '<"H"lfr>t<"F"ip>',
        // "sDom": '<"H"lfr>t<"F"ip>T',
        
        "aoColumns" : [   
            { "sWidth": "4%" },   
            null,  
            { "sWidth": "8%" },  
            { "sWidth": "10%", "sClass": "right" },   
            { "sWidth": "8%", "sClass": "right" },  
            { "sWidth": "10%", "sClass": "right" }, 
            { "sWidth": "8%", "sClass": "right" }, 
            { "sWidth": "8%", "sClass": "right" }, 
            { "sWidth": "8%", "sClass": "right" }, 
            { "sWidth": "10%", "sClass": "right" } 
        ] ,
        "oTableTools": {
            "sSwfPath": "<?=base_url()?>assets/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
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
    });


	$("tfoot").removeClass();
	
      $('#btnprint').click(function() {
         var nop_kd = $("#nop_kd").val();
         /*
         window.open("<?=active_module_url('op_rpt');?>?nop_kd="+nop_kd, "_blank");
		 return false;
         */
         
		 var winparams = 'location=1,status=1,scrollbars=1,resizable=no,width='+screen.width+',height='+screen.height+',menubar=no,toolbar=no,fullscreen=no';
		 window.open('<?=active_module_url('op_rpt');?>cetak/pdf/'+nop_kd, 'Laporan', winparams);
		 return false;
       });

} );

</script>
<div class="content">
    <div class="container-fluid">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#op"><strong>Objek Pajak</strong></a></li>
        </ul>
        <?php echo form_open(active_module_url().'op',array('id'=>'myform', 'class'=>'form-horizontal','method'=>'get'));?>
			<div class="control-group">
				<label class="control-label"><strong>N O P</strong></label> 
				<div class="controls">
					<input type="text" id="nop_kd" class="small autocompleteIconTextfield" value="<?=($nop_kd != 0 ? $nop_kd : '');?>" name="nop_kd" autocomplete="off" placeholder="NOP" size="30"/>
					<!--Nama
					<input type="text" class="small autocompleteIconTextfield" name="nama_wp" id="nama_wp" autocomplete="off" placeholder="Nama" size="30" value="<?=($nama_wp != '' ? $nama_wp : '');?>" />
					&nbsp;&nbsp;&nbsp;&nbsp;-->
					<button class="btn btn-info" type="submit">Cari</button>
					<button class="btn btn-success" id="btnprint">Print Format</button>
				</div>
			</div>
        </form>   
		
		<?php if(!isset($data_source) && !empty($nop_kd)) { ?>
		  <div><div id="msg_helper" class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Data tidak ditemukan !</div></div>
		<?php } ?>
		
        <div class="row">
            <div class="span6">
                <div class="page-header">
                    <strong><i class="icon-th-list"></i> Objek Pajak<?echo !empty($data_source[0]['nop']) ? " - NOP : ".$data_source[0]['nop'] : "";?></strong>
                </div>
                <div class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label">Letak OP</label>
                        <div class="controls">
                            <label class="input">: <?=$data_source[0]['alamat_op'];?></label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">RT/RW</label>
                        <div class="controls">
                            <label class="input">: <?=$data_source[0]['rt_rw_op'];?></label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Kelurahan</label>
                        <div class="controls">
                            <label class="input">: <?=$data_source[0]['kelurahan_op'];?></label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Kecamatan</label>
                        <div class="controls">
                            <label class="input">: <?=$data_source[0]['kecamatan_op'];?></label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Kota</label>
                        <div class="controls">
                            <label class="input">: <?=LICENSE_TO;?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="page-header">
                    <strong><i class="icon-th-list"></i> Subjek Pajak</strong>
                </div>
                <div class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label">Nama WP</label>
                        <div class="controls">
                            <label class="input">: <?=$data_source[0]['nm_wp_sppt'];?></label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Alamat</label>
                        <div class="controls">
                            <label class="input">: <?=$data_source[0]['alamat_wp'];?></label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">RT/RW</label>
                        <div class="controls">
                            <label class="input">: <?=$data_source[0]['rt_rw_wp'];?></label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Kelurahan</label>
                        <div class="controls">
                            <label class="input">: <?=$data_source[0]['kelurahan_wp'];?></label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Kabupaten/Kota</label>
                        <div class="controls">
                            <label class="input">: <?=$data_source[0]['kota_wp'];?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
        <div class="page-header">
            <strong><i class="icon-list"></i> SPPT</strong>  
        </div>
		
        <table class="display dataTables" id="datatable">
            <thead>
                <tr>
                    <th>Tahun</th>
                    <th>Nama WP</th>
                    <th>Luas Tanah</th>
                    <th>NJOP Tanah</th>
                    <th>Luas Bng</th>
                    <th>NJOP Bng</th>
                    <th>Ketetapan</th>
                    <th>Bayar</th>
                    <th>Sisa</th>
                    <th>Tgl. Byr (Akh)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(isset($data_source)) {
                    foreach($data_source as $val) {
                    ?>
                <tr>
                    <td><?=$val['thn_pajak_sppt'];?></td>
                    <td><?=$val['nm_wp_sppt'];?></td>
                    <td align="right"><?=number_format ($val['luas_tanah'], 0 ,  ',' , '.' );?></td>
                    <td align="right"><?=number_format ($val['njop_tanah'], 0 ,  ',' , '.' );?></td>
                    <td align="right"><?=number_format ($val['luas_bng'], 0 ,  ',' , '.' );?></td>
                    <td align="right"><?=number_format ($val['njop_bng'], 0 ,  ',' , '.' );?></td>
                    <td align="right"><?=number_format ($val['ketetapan'], 0 ,  ',' , '.' );?></td>
                    <td align="right"><?=number_format ($val['jml_bayar'], 0 ,  ',' , '.' );?></td>
                    <td align="right"><?=number_format ($val['ketetapan']-$val['jml_bayar'], 0 ,  ',' , '.' );?></td>
                    <td align="right"><?=$val['tgl_bayar'];?></td>
                </tr>
                <?php
                    }
                    }
                    ?>
            </tbody>
        </table>
    </div>
</div>

<? $this->load->view('_foot'); ?>



