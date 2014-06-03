<? $this->load->view('_head'); ?>
<? $this->load->view('_navbar'); ?>
<style type="text/css">@import "<?=base_url()?>assets/css/pbbm.css";</style>
<script>
$(document).ready(function() {
    var oTable = $('#datatable').dataTable( {
        "iDisplayLength": 100,
        "sScrollY": "270px",
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
            { sWidth: '10%' },   
            null,  
            { sWidth: '6%', sClass: "center" },   
            { sWidth: '15%', sClass: "right" },   
            { sWidth: '10%', sClass: "right" },
            { sWidth: '15%', sClass: "right" }
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
        "fnInitComplete": function (oSettings, json) {
            $('#pokok').html(json['pokok']);
            $('#denda').html(json['denda']);
            $('#total').html(json['total']);
            oTable.fnAdjustColumnSizing();
        },
    });


    $("#tahun, #kec_kd, #kel_kd, #buku, #tahun_sppt1, #tahun_sppt2, #tp_kd").change(function(){
        var tahun = $("#tahun").val();
        var tahun_sppt1 = $("#tahun_sppt1").val();
        var tahun_sppt2 = $("#tahun_sppt2").val();

        if($(this).attr('name')=='kec_kd') $("#kel_kd").val('000');
        var kec_kd = $("#kec_kd").val();
        var kel_kd = $("#kel_kd").val();
        var buku = $("#buku").val();
        var tp = $("#tp_kd").val();
        window.location = "<?=active_module_url().'tranmonths/'?>?tahun="+ tahun +"&tahun_sppt1="+ tahun_sppt1 +"&tahun_sppt2="+ tahun_sppt2+ "&kec_kd=" + kec_kd +"&kel_kd=" + kel_kd + "&buku=" + buku + "&tp_kd=" + tp;
    });

    $('#btnprint').click(function() {
        var tahun = $("#tahun").val();
        var tahun_sppt1 = $("#tahun_sppt1").val();
        var tahun_sppt2 = $("#tahun_sppt2").val();
        var kec_kd = $("#kec_kd").val();
        var kel_kd = $("#kel_kd").val();
        var buku = $("#buku").val();
        var tp = $("#tp_kd").val();
        // window.open("<?=active_module_url().'trans_rpt/bulan'?>?tahun="+ tahun +"&tahun_sppt1="+ tahun_sppt1 +"&tahun_sppt2="+ tahun_sppt2+ "&kec_kd=" + kec_kd +"&kel_kd=" + kel_kd + "&buku=" + buku,target="laporan");
        
        var winparams = 'location=1,status=1,scrollbars=1,resizable=no,width='+screen.width+',height='+screen.height+',menubar=no,toolbar=no,fullscreen=no';
        window.open("<?=active_module_url().'trans_rpt/cetak/pdf/'?>3/"+ kec_kd +"/"+ kel_kd +"/"+ tahun_sppt1 +"/"+ tahun_sppt2+ "/" + buku +"/" + tahun + "/" + tp, 'Laporan', winparams);
        return false;
    });
  
});

function closeDialog () {
    $('#printdialog').modal('hide');
}    
</script>

<div class="content">
    <div class="container-fluid">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#transaksi"><strong>Transaksi Pembayaran - Rekap Bulanan</strong></a></li>
        </ul>
        <?php echo form_open('#',array('id'=>'myform', 'class'=>'form-horizontal'));?>
        <div class="control-group">
            <label class="control-label">Thn Bayar</label> 
            <div class="controls">
                <select id="tahun" name="tahun" style="width:80px;">
                <?php
                    $maxtahun=date('Y');
                    $mintahun=1999;
                    $thncnt = $maxtahun - $mintahun;
                    for ($i=$maxtahun; $i>=$maxtahun-$thncnt; $i--)
                    {
                      $selected='';
                      if ($i==$tahun) $selected=" selected";
                      echo "<option value=\"$i\" $selected>$i</option>\n"; 
                    }
                    ?>
                </select> 
                Thn. SPPT 
                <select id="tahun_sppt1" name="tahun_sppt1" style="width:80px;">
                <?php
                    $maxtahun=date('Y');
                    $mintahun=1999;
                    $thncnt = $maxtahun - $mintahun;
                    for ($i=$maxtahun; $i>=$maxtahun-$thncnt; $i--)
                    {
                      $selected='';
                      if ($i==$tahun_sppt1) $selected=" selected";
                      echo "<option value=\"$i\" $selected>$i</option>\n"; 
                    }
                    ?>
                </select> 
                s.d
                <select id="tahun_sppt2" name="tahun_sppt2" style="width:80px;">
                <?php
                    $maxtahun=date('Y');
                    $mintahun=1999;
                    $thncnt = $maxtahun - $mintahun;
                    for ($i=$maxtahun; $i>=$maxtahun-$thncnt; $i--)
                    {
                      $selected='';
                      if ($i==$tahun_sppt2) $selected=" selected";
                      echo "<option value=\"$i\" $selected>$i</option>\n"; 
                    }
                    ?>
                </select> 
                
                
                Buku
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
                            

                TP
                <select id="tp_kd" name="tp_kd">
                <?php
                    echo "<option value=\"\">Semua TP</option>\n";
                    foreach ($tp as $row)  {
                        $selected='';
                        if ($row->kode==$tp_kd) $selected=" selected";
                        echo "<option value=\"".$row->kode ."\" $selected>".$row->nm_tp."</option>\n";
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
        </form>
        <hr>
        <table class="display" id="datatable">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Uraian</th>
                    <th>Thn.SPPT</th>
                    <th>Pokok</th>
                    <th>Denda</th>
                    <th>Bayar</th>
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
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">TOTAL</td>
                    <td><span id="pokok">&nbsp;</span></td>
                    <td><span id="denda">&nbsp;</span></td>
                    <td><span id="total">&nbsp;</span></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
</div>
<? $this->load->view('_foot'); ?>

