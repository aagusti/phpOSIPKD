<? $this->load->view('_head'); ?>
<? $this->load->view('_navbar'); ?>
<style type="text/css">@import "<?=base_url()?>assets/css/pbbm.css";</style>
<script>
$(document).ready(function() {
    var oTable = $('#datatable').dataTable({
        "iDisplayLength": 100,
        "sScrollY": "220px",
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
            { "sWidth": '110pt' },
            null,
            { "sWidth": '20pt', "sClass": "right" },
            { "sWidth": '25pt', "sClass": "right" },
            { "sWidth": '20pt', "sClass": "right" },
            { "sWidth": '25pt', "sClass": "right" },
            { "sWidth": '20pt', "sClass": "right" },
            { "sWidth": '25pt', "sClass": "right" },
            { "sWidth": '20pt', "sClass": "right" },
            { "sWidth": '25pt', "sClass": "right" },
            { "sWidth": '10pt', "sClass": "center"},
            { "sWidth": '20pt', "sClass": "right" },
            { "sWidth": '25pt', "sClass": "right" },
            { "sWidth": '10pt', "sClass": "center"},
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
        /*
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.getJSON( sSource, aoData, function (json) {
                //Here you can do whatever you want with the additional data
                // console.dir(json);
                
                $('#nsppt1').html(json['nsppt1']);
                $('#amount1').html(json['amount1']);
                $('#nsppt2').html(json['nsppt2']);
                $('#amount2').html(json['amount2']);
                $('#nsppt3').html(json['nsppt3']);
                $('#amount3').html(json['amount3']);
                $('#nsppt4').html(json['nsppt4']);
                $('#amount4').html(json['amount4']);
                $('#persen1').html(json['persen1']);
                $('#nsppt5').html(json['nsppt5']);
                $('#amount5').html(json['amount5']);
                $('#persen2').html(json['persen2']);
                
                //Call the standard callback to redraw the table
                fnCallback(json);
            });
        },
        */
        "fnInitComplete": function (oSettings, json) {
            $('#nsppt1').html(json['nsppt1']);
            $('#amount1').html(json['amount1']);
            $('#nsppt2').html(json['nsppt2']);
            $('#amount2').html(json['amount2']);
            $('#nsppt3').html(json['nsppt3']);
            $('#amount3').html(json['amount3']);
            $('#nsppt4').html(json['nsppt4']);
            $('#amount4').html(json['amount4']);
            $('#persen1').html(json['persen1']);
            $('#nsppt5').html(json['nsppt5']);
            $('#amount5').html(json['amount5']);
            $('#persen2').html(json['persen2']);
            oTable.fnAdjustColumnSizing();
        },
    });
    
    $( "#tglawal, #tglakhir" ).datepicker({
        dateFormat:'dd-mm-yy',
        changeMonth:true,
        changeYear:true
    });

    $("#btngo").click(function(){
        var tahun = $("#tahun").val();
        var buku = $("#buku").val();
        var tglawal = $("#tglawal").val();
        var tglakhir = $("#tglakhir").val();
        var kec_kd = $("#kec_kd").val();
        var kel_kd = $("#kel_kd").val();
        window.location = "<?=active_module_url().'realisasi'?>/?tahun="+tahun+"&buku="+buku+"&tglawal="+ tglawal + "&tglakhir=" + tglakhir+ "&kec_kd=" + kec_kd +"&kel_kd=" + kel_kd;
    });

    $("#kec_kd, #kel_kd, #tahun, #buku").change(function(){
        var tahun = $("#tahun").val();
        var buku = $("#buku").val();
        var tglawal = $("#tglawal").val();
        var tglakhir = $("#tglakhir").val();
        
        if($(this).attr('name')=='kec_kd') $("#kel_kd").val('000');
        var kec_kd = $("#kec_kd").val();
        var kel_kd = $("#kel_kd").val();
        window.location = "<?=active_module_url().'realisasi'?>/?tahun="+tahun+"&buku="+buku+"&tglawal="+ tglawal + "&tglakhir=" + tglakhir+ "&kec_kd=" + kec_kd +"&kel_kd=" + kel_kd;
    });

    $('#btnprint').click(function() {
        var tahun = $("#tahun").val();
        var buku = $("#buku").val();
        var tglawal = $("#tglawal").val();
        var tglakhir = $("#tglakhir").val();
        var kec_kd = $("#kec_kd").val();
        var kel_kd = $("#kel_kd").val();
        // window.open("<?=active_module_url()."real_rpt/nb"?>?tahun="+tahun+"&tglawal="+ tglawal + "&tglakhir=" + tglakhir+ "&kec_kd=" + kec_kd +"&kel_kd=" + kel_kd + "&buku=" + buku,target="laporan");
        
		 var winparams = 'location=1,status=1,scrollbars=1,resizable=no,width='+screen.width+',height='+screen.height+',menubar=no,toolbar=no,fullscreen=no';
         window.open("<?=active_module_url().'real_rpt/cetak/pdf/1'?>/"+ kec_kd +"/"+ kel_kd +"/"+ tahun +"/"+ buku +"/" + tglawal +"/"+ tglakhir, 'Laporan', winparams);
    });
});
</script>

<div class="content">
    <div class="container-fluid">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#realisasi"><strong>Ketetapan dan Realisasi PBB-P2</strong></a></li>
        </ul>

        <div class="form-horizontal">
            <div class="control-group">
                <label class="control-label">Tgl. Realisasi</label>
                <div class="controls">
                    <input style="width:80px;" id="tglawal" name="tglawal" width="5" type="text" value="<?if(isset($tglawal)) echo $tglawal?>"/>
                    s.d. <input style="width:80px;" id="tglakhir" name="tglakhir" type="text" value="<?if(isset($tglakhir)) echo $tglakhir?>"/>
                    <button class="btn" id="btngo" name="btngo">Go</button>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Tahun SPPT</label>
                <div class="controls">
                    <select id="tahun" name="tahun" style="width:80px;">
                    <?php
                        echo "<option value=\"0000\">Semua</option>\n";

                        $maxtahun=date('Y');
                        for ($i=$maxtahun; $i>$maxtahun-10; $i--)
                        {
                          $selected='';
                          if ($i==$tahun) $selected=" selected";
                          echo "<option value=\"$i\" $selected>$i</option>\n";
                        }
                        ?>
                    </select> Buku
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
                    <button class="btn btn-success" id="btnprint">Print Format</button>
                </div>
            </div>
        </div>

        <hr>
        <!--class="ui-state-default"  style="border:solid #EDEDED; "-->
        <table class="display dataTables" id="datatable">
            <thead>
                <tr>
                    <th rowspan="3" class="ui-state-default">Kode</th>
                    <th rowspan="3" class="ui-state-default">Uraian</th>
                    <th colspan="2" class="ui-state-default">Pokok</th>
                    <th colspan="7" class="ui-state-default">Realisasi</th>
                    <th colspan="3" class="ui-state-default">Sisa</th>
                </tr>
                <tr>
                    <th rowspan="2" class="ui-state-default">SPPT</th>
                    <th rowspan="2" class="ui-state-default">Jumlah</th>
                    <th colspan="2" class="ui-state-default">Lalu</th>
                    <th colspan="2" class="ui-state-default">Kini</th>
                    <th colspan="2" class="ui-state-default">Jumlah</th>
                    <th rowspan="2" class="ui-state-default">%</th>
                    <th rowspan="2" class="ui-state-default">SPPT</th>
                    <th rowspan="2" class="ui-state-default">Jumlah</th>
                    <th rowspan="2" class="ui-state-default">%</th>
                </tr>
                <tr>
                    <th class="ui-state-default">SPPT</th>
                    <th class="ui-state-default">Jumlah</th>
                    <th class="ui-state-default">SPPT</th>
                    <th class="ui-state-default">Jumlah</th>
                    <th class="ui-state-default">SPPT</th>
                    <th class="ui-state-default">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">TOTAL</td>
                    <td><span id="nsppt1"></span></td>
                    <td><span id="amount1"></span></td>
                    <td><span id="nsppt2"></span></td>
                    <td><span id="amount2"></span></td>
                    <td><span id="nsppt3"></span></td>
                    <td><span id="amount3"></span></td>
                    <td><span id="nsppt4"></span></td>
                    <td><span id="amount4"></span></td>
                    <td><span id="persen1"></span></td>
                    <td><span id="nsppt5"></span></td>
                    <td><span id="amount5"></span></td>
                    <td><span id="persen2"></span></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<? $this->load->view('_foot'); ?>