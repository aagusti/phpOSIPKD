<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class users_model extends CI_Model {
	private $tbl = 'users';
	
	function __construct() {
		parent::__construct();
	}
		
	function get_all()
	{	$sql = "select u.*
				from users u
				order by u.disabled desc, u.nama";
				
		$query = $this->db->query($sql);
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}
	
	function get_by_group($group_id, $in_group=FALSE)
	{	$sql = "select * from (
					select 1 in_group, u.*, ".$group_id." group_id
					from users u
					inner join user_groups ug on ug.user_id=u.id
					where group_id=".$group_id."
					union
					select 0 as in_group, u.*,".$group_id." group_id
					from users u
					where u.id not in (select user_id from user_groups where group_id=".$group_id.")
				) as gu
				".($in_group? " where in_group=1 ": "")."
				order by in_group desc, disabled desc, nama

";
				
		$query = $this->db->query($sql);
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}
	
	function get($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->row();
		}
		else
			return FALSE;
	}
	
	//-- admin
	function save($data) {
		$this->db->insert($this->tbl,$data);
		return $this->db->insert_id();
	}
	
	function update($id, $data) {
		$this->db->where('id', $id);
		$this->db->update($this->tbl,$data);
	}
	
	function delete($id) {
		$this->db->where('id', $id);
		$this->db->delete($this->tbl);
	}
}

/* End of file _model.php */