<? $this->load->view('_head'); ?>

  
<? $this->load->view(active_module().'/_navbar'); ?>

<div id="modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>
<div class="content">
<script>
$(document).ready(function(){
    $('.isotope-container').isotope({ filter: $('input[name=dashboardview]:checked').val() });
    $('input[name=dashboardview]').change(function(){
        var base = this;
        setTimeout(function(){
            $('.isotope-container').isotope({filter: $(base).val()});},500);
    });
});
</script>

<div class="grid_5 leading" style="height:270px;">
	<fieldset class="fieldset-buttons ui-corner-all" style="margin-left:0px;">
		<legend class="buttonset-legend">
			<span id="dashboardview-filter" class="buttonset">
				<input type="radio" name="dashboardview" id="dashboardview-jmltrans" value=".jml-trans" checked />
				  <label for="dashboardview-jmltrans"><?=$subtitle;?></label>
				<input type="radio" name="dashboardview" id="dashboardview-nomtrans" value=".nom-trans" />
				  <label for="dashboardview-nomtrans"><?=$subtitle2;?></label>
			</span>
		</legend>
		
		<ul class="isotope-widgets isotope-container">
			<li class="jml-trans">
				<a class="button-gray ui-corner-all" href="#">
					<strong><?=$today_trans;?></strong>
					<span><?=$today_cap;?></span>
				</a>
			</li>
			<li class="jml-trans">
				<a class="button-blue ui-corner-all" href="#">
					<strong><?=$week_trans;?></strong>
					<span><?=$week_cap;?></span>
				</a>
			</li>
			<li class="jml-trans">
				<a class="button-orange ui-corner-all" href="#">
					<strong><?=$month_trans;?></strong>
					<span><?=$month_cap;?></span>
				</a>
			</li>
			<li class="jml-trans">
				<a class="button-green ui-corner-all" href="#">
					<strong><?=$year_trans;?></strong>
					<span><?=$year_cap;?></span>
				</a>
			</li>
			
			<li class="nom-trans">
				<a class="button-gray ui-corner-all" href="#">
					<strong><?=$today_amount;?></strong>
					<span><?=$today_cap;?></span>
				</a>
			</li>
			<li class="nom-trans">
				<a class="button-blue ui-corner-all" href="#">
					<strong><?=$week_amount;?></strong>
					<span><?=$week_cap;?></span>
				</a>
			</li>
			<li class="nom-trans">
				<a class="button-orange ui-corner-all" href="#">
					<strong><?=$month_amount;?></strong>
					<span><?=$month_cap;?></span>
				</a>
			</li>
			<li class="nom-trans">
				<a class="button-green ui-corner-all" href="#">
					<strong><?=$year_amount;?></strong>
					<span><?=$year_cap;?></span>
				</a>
			</li>
		</ul>
	</fieldset>
 </div>
</div>
<? $this->load->view('_foot'); ?>