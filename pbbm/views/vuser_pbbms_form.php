<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>
<script>
    $(document).ready(function() {
    	$('#btn_cancel').click(function() {
    		window.location = "<?echo $this->uri->segment(2)=='users2' ? active_module_url() : active_module_url('user_pbbms');?>";
    	});
    
      $("#kd_kecamatan").change(function(){
         var kec_kd = $("#kd_kecamatan").val();
         $.ajax({
            url: "<?=active_module_url().'user_pbbms/get_lurah/'?>"+kec_kd,
            dataType: "json",
            success: function(data) {
               var kelurahans = data.kelurahans,
                   sKelurahan= '<option value="000">Semua</option>';;
               for(var idx = 0;idx < kelurahans.length;++idx){          
                   sKelurahan+= '<option value="'+ kelurahans[idx].kd_kelurahan +'">' + kelurahans[idx].nm_kelurahan +'</option>';
    
               }
               $('#kd_kelurahan').empty().append(sKelurahan);
            }
        });
      });  
    });
</script>
<div class="content">
    <div class="container-fluid">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#"><strong>USERS</strong></a>
            </li>
        </ul>
        <?php
            if(validation_errors()){
            	echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
            	echo validation_errors('<small>','</small>');
            	echo '</blockquote>';
            } ?>
        <?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal','enctype'=>'multipart/form-data'));?>
        <div class="control-group">
            <label class="control-label">User</label>
            <div class="controls">
                <!--input class="input-small" type="text" name="user_id" value="-->
                <select id="user_id" name="user_id" class="input-medium" <?echo $this->uri->segment(3)=='edit' ? 'readonly' : '';?>>
                <?php
                    foreach ($users as $r) 
                    {
                    	$selected='';
                    	if ($r->id==$dt['user_id']) $selected=" selected";
                    	echo "<option value=\"".$r->id ."\" $selected>".$r->nama."</option>\n";
                    }
                    ?>
                </select> 
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Kecamatan</label> 
            <div class="controls">
                <select id="kd_kecamatan" name="kd_kecamatan" class="input-medium" <?echo get_user_kec_kd() != '000' ? "disabled" : "";?>>
                <?php
                    echo "<option value=\"000\">Semua</option>\n";
                    
                    foreach ($kecamatan as $kec) 
                    {
                    	$selected='';
                    	if ($kec->kd_kecamatan==$dt['kd_kecamatan']) $selected=" selected";
                    	echo "<option value=\"".$kec->kd_kecamatan ."\" $selected>".$kec->nm_kecamatan."</option>\n";
                    }
                    ?>
                </select> 
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Kelurahan </label> 
            <div class="controls">
                <select id="kd_kelurahan" name="kd_kelurahan" class="input-medium">
                <?php
                    echo "<option value=\"000\">Semua</option>\n";
                    foreach ($kelurahan as $kel) 
                    {
                    	$selected='';
                    	if ($kel->kd_kelurahan==$dt['kd_kelurahan']) $selected=" selected";
                    	echo "<option value=\"".$kel->kd_kelurahan."\" $selected>".$kel->nm_kelurahan."</option>\n";
                    }
                    ?>
                </select> 
            </div>
        </div>
        <!--div class="control-group">
            <label class="control-label">Disabled</label>
            <div class="controls">
                <label class="checkbox">
                <input type="checkbox" name="disabled" <?=$dt['disabled']?>>
                </label>
            </div>
        </div-->
        <div class="control-group">
            <div class="controls">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn" id="btn_cancel">Batal</button>
            </div>
        </div>
        </form>
    </div>
</div>
<? $this->load->view('_foot'); ?>