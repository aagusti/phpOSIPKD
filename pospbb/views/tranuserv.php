<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>
<style type="text/css">@import "<?=base_url()?>assets/css/pbbm.css";</style>
<script>
$(document).ready(function() {
    var oTable = $('#datatable').dataTable( {
        "iDisplayLength": 100,
        "sScrollY": "260px",
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

        "aoColumns" : [
            { sWidth: '14%', sClass: "center" },   
            <? if($this->uri->segment(3)=='1') : ?>
            { sWidth: '6%', sClass: "center" },
            <? endif; ?>
            null,  
            { sWidth: '10%', sClass: "right" },   
            { sWidth: '8%', sClass: "right" },
            { sWidth: '10%', sClass: "right" },
            <? if($this->uri->segment(3)=='1') : ?>
            { sWidth: '6%', sClass: "center" },
            null,
            <? endif; ?>
            { sWidth: '10%', sClass: "right" },
            
        ],
        
        "aoColumnDefs": [ 
            { "bSearchable": false, "aTargets": [ 0 ], "bSortable": true, "aTargets": [ 0 ] },
            { "bSearchable": false, "aTargets": [ 1 ], "bSortable": true, "aTargets": [ 1 ] }
        ],
        
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
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.getJSON( sSource, aoData, function (json) {
                //Here you can do whatever you want with the additional data
                // console.dir(json);
                $('#pokok').html(json['pokok']);
                $('#denda').html(json['denda']);
                $('#total').html(json['total']);
                
                //Call the standard callback to redraw the table
                fnCallback(json);
            });
        },
    });

	var tb_array = [
		'<div class="btn-group pull-left">',
		'	<button class="btn btn-success" id="btnprint">Print Format</button>',
		'</div>',
	];
	var tb = ''; //tb_array.join(' ');
	$("div.toolbar").html(tb);
    
    $( "#tglawal, #tglakhir" ).datepicker({
        dateFormat:'dd-mm-yy', 
        changeMonth:true, 
        changeYear:true
    });

    $("#btngo").click(function(){
        var tglawal  = $("#tglawal").val();
        var tglakhir = $("#tglakhir").val();
        var kec_kd   = $("#kec_kd").val();
        var kel_kd   = $("#kel_kd").val();
        var buku = $("#buku").val();
        var user_kd = $("#user_kd").val();
        window.location = "<?=active_module_url().'tranuser/'.$trantypes?>/?tglawal="+ tglawal + "&tglakhir=" + tglakhir+ "&kec_kd=" + kec_kd +"&kel_kd=" + kel_kd + "&buku=" + buku + "&user_kd=" + user_kd;
    });

    $("#kec_kd, #kel_kd, #buku, #user_kd").change(function(){
        var tglawal = $("#tglawal").val();
        var tglakhir = $("#tglakhir").val();
        
        if($(this).attr('name')=='kec_kd') $("#kel_kd").val('000');
        var kec_kd = $("#kec_kd").val();
        var kel_kd = $("#kel_kd").val();
        var buku = $("#buku").val();
        var user_kd = $("#user_kd").val();
        window.location = "<?=active_module_url().'tranuser/'.$trantypes?>/?tglawal="+ tglawal + "&tglakhir=" + tglakhir+"&kec_kd=" + kec_kd +"&kel_kd=" + kel_kd + "&buku=" + buku + "&user_kd=" + user_kd;
    });

    $('#btnprint').click(function() {
        var tglawal = $("#tglawal").val();
        var tglakhir = $("#tglakhir").val();
        var kec_kd = $("#kec_kd").val();
        var kel_kd = $("#kel_kd").val();
        var buku = $("#buku").val();
        var user_kd = $("#user_kd").val();
		var winparams = 'location=1,status=1,scrollbars=1,resizable=no,width='+screen.width+',height='+screen.height+',menubar=no,toolbar=no,fullscreen=no';
        window.open("<?=active_module_url().'trans_rpt/cetak/pdf/'.$trantypes?>/"+ kec_kd +"/"+ kel_kd +"/"+ "/" + buku +"/" + tglawal +"/"+ tglakhir+"/"+user_kd, 'Laporan', winparams);
    });
    
	$('#btn_csv').click(function() {
        var rpt_type = <?=$trantypes;?>;
        var url = '<?=active_module_url('trans_rpt/csv_rekap_user');?>';
        if(rpt_type==1) url = '<?=active_module_url('trans_rpt/csv_rincian_user');?>';
                
        $('#myform').attr('action', url);
        $('#myform').submit();
        return false;
	});
});
</script>
<div class="content">
    <div class="container-fluid">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#tranuser"><strong><? echo $this->uri->segment(3)==2 ? 'Transaksi User - Rekap Harian' : 'Transaksi User – Rinci Harian';?></strong></a></li>
        </ul>
        <!--div class="form-horizontal"-->
        <?php echo form_open('#',array('id'=>'myform', 'class'=>'form-horizontal'));?>
			<div class="control-group">
				<label class="control-label">Tanggal</label> 
				<div class="controls">
					<input style="width:80px;" id="tglawal" name="tglawal" width="5" type="text" value="<?if(isset($tglawal)) echo $tglawal?>"/>
					s.d. <input style="width:80px;" id="tglakhir" name="tglakhir" type="text" value="<?if(isset($tglakhir)) echo $tglakhir?>"/>
					<button type="button" class="btn" id="btngo" name="btngo">Go</button>
				</div>
			</div>
        
			<div class="control-group">
				<label class="control-label">Buku</label> 
				<div class="controls">
          
      	<select id="buku" name="buku" style="width:125px;">
				<?for ($i=1; $i<=5; $i++){
					for ($j=$i;$j<=5;$j++){
						$r="";
						for ($k=$i;$k<=$j;$k++) $r.="$k,";
						$r=substr($r,0,strlen($r)-1);
						if ($buku=="$i$j") $selected="selected";
						else $selected="";
						echo "<option value=\"$i$j\" $selected>Buku $r</option>\n";
					}
					}
					?>
				</select>
                User
                <select id="user_kd" name="user_kd">
                <?php
                
                    echo "<option value=\"\">Semua User</option>\n";

                    if ($user_kd == "0") $selected="selected";
                    else $selected="";
                    echo "<option value=\"0\" $selected>User H2H</option>\n";

                    if ($user_kd == "-1") $selected="selected";
                    else $selected="";
                    echo "<option value=\"-1\" $selected>User POSPBB</option>\n";

                    foreach ($usertbl as $row)  {
                        $selected='';
                        if ($row->id==$user_kd) $selected=" selected";
                        echo "<option value=\"".$row->id ."\" $selected>".$row->nama."</option>\n";
                    }
                    ?>
                </select> 
           </div>
			</div>
			<div class="control-group">
				<label class="control-label">Kecamatan</label> 
				<div class="controls">
					<select id="kec_kd" name="kec_kd" <?=($user_kec_kd!='000'?" disabled" :"")?>>
					<?php
						if ($user_kec_kd=='000')
						   echo "<option value=\"000\">Semua</option>\n";
						
						foreach ($kecamatan as $kec) 
						{
						 $selected='';
						 if ($kec->kd_kecamatan==$kec_kd) $selected=" selected";
						 echo "<option value=\"".$kec->kd_kecamatan ."\" $selected>".$kec->nm_kecamatan."</option>\n";
						}
						?>
					</select> 
					Kelurahan 
					<select id="kel_kd" name="kel_kd">
					<?php
						if ($user_kel_kd=='000')
							echo "<option value=\"000\">Semua</option>\n";
						foreach ($kelurahan as $kel) 
						{
						  $selected='';
						  if ($kel->kd_kelurahan==$kel_kd) $selected=" selected";
						  echo "<option value=\"".$kel->kd_kelurahan."\" $selected>".$kel->nm_kelurahan."</option>\n";
						}
						?>
					</select>
                    <button type="button" class="btn btn-success" id="btnprint">Print Format</button>
                    <button type="button" class="btn btn-success" id="btn_csv" name="btn_csv">Download (CSV)</button>
				</div>
			</div>
        </form>
		
		<hr>
		
        <table class="display" id="datatable">
            <thead>
                <tr>
                    <? if($this->uri->segment(3)=='1') : ?>
                      <th>NOP</th>
                      <th>Thn.SPPT</th>
                    <? else: ?>
                      <th>Tanggal</th>
                    <? endif; ?>
                    <th>Uraian</th>
                    <th>Pokok</th>
                    <th>Denda</th>
                    <th>Bayar</th>
                    <? if($this->uri->segment(3)=='1') : ?>
                      <th>Tanggal</th>
                      <th>Tempat Pembayaran</th>
                    <? endif; ?>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <? if($this->uri->segment(3)=='1') : ?>
                    <td></td>
                    <? endif; ?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <? if($this->uri->segment(3)=='1') : ?>
                    <td></td>
                    <td></td>
                    <? endif; ?>
                    <td></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">TOTAL</td>
                    <td><span id="pokok">&nbsp;</span></td>
                    <td><span id="denda">&nbsp;</span></td>
                    <td><span id="total">&nbsp;</span></td>
                    <? if($this->uri->segment(3)=='1') : ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <? endif; ?>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<? $this->load->view('_foot'); ?>