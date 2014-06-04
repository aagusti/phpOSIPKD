<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pos_user_model extends CI_Model {
	private $tbl = 'users';
	
	function __construct() {
		parent::__construct();
	}
	
    function pos_field() {
        $fields     = explode(',', POS_FIELD);
        $pos_join   = ''; $fs='';
        foreach ($fields as $f) {
            $fs = $f;
            if ($f == 'kd_kanwil_bank')
                $fs = 'kd_kanwil';
            else if ($f == 'kd_kppbb_bank')
                $fs = 'kd_kppbb';
                
            $pos_join .= "up.{$fs}=tp.{$fs} and ";
        }
        $pos_join = substr($pos_join, 0, -4);
        return $pos_join;
    }
    
	function get_all() {
        $sql = "select u.*,tp.nm_tp
				from users as u
                inner join user_pbb up on up.user_id=u.id
                inner join tempat_pembayaran tp on {$this->pos_field()}
				order by u.nama";
		
        $this->db->trans_start();
		$query = $this->db->query($sql);
        $this->db->trans_complete();
        
        if($this->db->trans_status() && $query->num_rows()>0)
            return $query->result();
        else
            return false;
	}
		
	function get($id)
	{
        $sql = "select u.*,tp.*
				from users u
                inner join user_pbb up on up.user_id=u.id
                inner join tempat_pembayaran tp on {$this->pos_field()}
                where u.id={$id}
				order by u.nama";
				
        $this->db->trans_start();
		$query = $this->db->query($sql);
        $this->db->trans_complete();
        
        if($this->db->trans_status() && $query->num_rows()>0)
            return $query->row();
        else
            return false;
	}
	
	//-- admin
	function save($data) {
        $this->db->trans_start();
		$this->db->insert($this->tbl,$data);
        $this->db->trans_complete();
            
        if($this->db->trans_status())
            return $this->db->insert_id();
        else
            return false;
	}
	
	function update($id, $data) {
        $this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update($this->tbl,$data);
        $this->db->trans_complete();
            
        if($this->db->trans_status())
            return true;
        else
            return false;
	}
	
	function delete($id) {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->delete($this->tbl);
        $this->db->trans_complete();
            
        if($this->db->trans_status())
            return true;
        else
            return false;
	}
}

/* End of file _model.php */
