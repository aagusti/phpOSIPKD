<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class privileges_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}
		
	function get_by_app($app_id, $grp_id)
	{	$sql = "select a.id app_id, a.nama app_nm, m.id as module_id, m.nama as module_nm, 
				gm.group_id, gm.reads, gm.inserts, gm.writes, gm.deletes
				from modules m 
				inner join apps a on a.id=m.app_id
				inner join group_modules gm on gm.module_id=m.id 
				where a.id=".$app_id." and gm.group_id=".$grp_id."

				union
				select a.id appid, a.nama appnm, m.id as moduleid, m.nama as modulenm, 
				".$grp_id.", null, null, null, null
				from modules m 
				inner join apps a on a.id=m.app_id
				where a.id=".$app_id." 
				and m.id not in (
					select module_id 
					from group_modules 
					where group_id=".$grp_id.")";
				
		$query = $this->db->query($sql);
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}
		
	function upd_auth($a,$b,$c,$d){
		$group = $this->db->query("select count(group_id) as jml 
			from group_modules 
			where group_id=$a and module_id=$b")->row();
		if($group->jml > 0) {
			$this->db->where('group_id', $a);
			$this->db->where('module_id', $b);
			$this->db->update('group_modules',array($c=>$d));
		} else {
			$this->db->insert('group_modules',array('group_id'=>$a, 'module_id'=>$b, $c=>$d));
		}
	}
}

/* End of file _model.php */