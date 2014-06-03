<? $this->load->view('_head'); ?>
<? //$this->load->view(active_module().'/_navbar'); ?>
<style type="text/css">
    .content {
        padding: 0px;
        margin: 0px;
    }
    form {
        margin: 1px;
    }
    form .cnop, form .ctahun, form .cbtn {
        float: left;
    }
    form .cnop {
        min-width: 40px;
        width: 150px;
        margin-left: 5px;
    }
    form .ctahun {
        min-width: 30px;
        width: 40px;
        margin-left: 2px;
    }
    form .cbtn {
        margin-left: 2px;
    }
    .table-info {
        display:table;
        width:100%;
    }
    table tbody {
        vertical-align: top;
    }
</style>
<div class="content">
    <? if ($found==false) :?>
    <div id="msg_helper" class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>Data tidak ditemukan.<?echo $result; ?></div>
    <? endif ?>
    <div style="margin:0px;padding:0px;">
        <div style="margin:0px;display:table;width:100%;background-color:#efefef;border-bottom:1px solid #cdcdcd;">
            <form action="<?=active_module_url('pbbm_mobile')?>" id="myform" method="get" style="margin:2px">
                <label style="cursor:default;float:left;padding-left:1px;padding-top:5px;font-weight:bold;">Cari :</label> 
                <input type="text" id="nop" class="small autocompleteIconTextfield cnop" value="<?=($carinop != '' ? $carinop : '');?>" name="nop" autocomplete="off" required="required" placeholder="NOP" maxlength="24"/>
                <input type="text" id="tahun" class="small autocompleteIconTextfield ctahun" value="<?=($caritahun != '' ? $caritahun : '');?>" name="tahun" autocomplete="off" required="required" placeholder="Tahun" maxlength="4"/>
                <button style="height: 25px;" class="midle button-gray ui-corner-all btn_submit btn btn-primary cbtn" type="submit">GO</button>
            </form>
        </div>
        <div style="display:table;padding:4px 2px;width:100%;">
            <? if ($countresult >0 ) : ?>
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td></td><td width="10"></td><td></td>
                    </tr>
                    <tr>
                        <td>NOP</td><td align="center">:</td><td><?=$result[0]['nop']?></td>
                    </tr>
                    <tr>
                        <td>Alamat</td><td align="center">:</td><td><?=$result[0]['alamat_op']?></td>
                    </tr>
                    <tr>
                        <td>RT/RW</td><td align="center">:</td><td><?=$result[0]['rt_rw_op']?></td>
                    </tr>
                    <tr>
                        <td>Kec.</td><td align="center">:</td><td><?=$result[0]['nm_kelurahan']?></td>
                    </tr>
                    <tr>
                        <td>Desa/Kel.</td><td align="center">:</td><td><?=$result[0]['nm_kecamatan']?></td>
                    </tr>
                    <tr>
                        <td>Luas Bumi</td><td align="center">:</td><td><?=number_format($result[0]['total_luas_bumi'], 0 ,  ',' , '.' )?>&nbsp;m<sup>2</sup></td>
                    </tr>
                    <tr>
                        <td>Luas Bangunan</td><td align="center">:</td><td><?=number_format($result[0]['total_luas_bng'], 0 ,  ',' , '.' )?>&nbsp;m<sup>2</sup></td>
                    </tr>
                    <tr>
                        <td>Jml.Tagihan</td><td align="center">:</td><td><?=($result[0]['tagihan']>0?'Rp. ' . number_format($result[0]['tagihan'], 0 ,  ',' , '.' ):'-')?></td>
                    </tr>
                    <tr>
                        <td>Tgl.Terakhir Bayar</td><td align="center">:</td><td><?=($result[$countresult-1]['tgl_bayar']!=null?date('d/m/Y', strtotime($result[$countresult-1]['tgl_bayar'])):'-')?></td>
                    </tr>
                    <?
                        $sisa = $result[0]['tagihan'];
                        for($xx=0;$xx<$countresult;$xx++){
                            $sisa -= $result[$xx]['bayar'];
                        }
                    ?>
                    <tr>
                        <td>Sisa Tagihan</td><td align="center">:</td><td><?=($sisa>0?'Rp. ' . number_format($sisa, 0 ,  ',' , '.' ):'-')?></td>
                    </tr>
                </table>
            <? endif ?>
        </div>
    </div>
</div>
<? $this->load->view('_foot'); ?>
