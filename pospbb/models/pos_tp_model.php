<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pos_tp_model extends CI_Model {
	private $tbl = 'tempat_pembayaran';
	
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
        $sql = "select *
				from tempat_pembayaran
        order by nm_tp ";
		
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
    $sql = "select *
    from tempat_pembayaran 
    where
       id = $id";
    
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
            return true;
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
	
	function delete($data) {
        $this->db->trans_start();
        $this->db->where('id', $data);
        $this->db->delete($this->tbl);
        $this->db->trans_complete();
            
        if($this->db->trans_status())
            return true;
        else
            return false;
	}
}

/* End of file _model.php */