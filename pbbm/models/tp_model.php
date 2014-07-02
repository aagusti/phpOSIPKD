<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class tp_model extends CI_Model {
	private $tbl = 'tempat_pembayaran';
	
	function __construct() {
		parent::__construct();
	}
		
	function get_all() {
        $this->db->trans_start();
		$query = $this->db->get($this->tbl);
        $this->db->trans_complete();
        
        if($this->db->trans_status() && $query->num_rows()>0)
            return $query->result();
        else
            return false;
	}
	
	function get($id)
	{
        $this->db->trans_start();
		$this->db->where('id',$id);
		$query = $this->db->get($this->tbl);
        $this->db->trans_complete();
        
        if($this->db->trans_status() && $query->num_rows()>0)
            return $query->row();
        else
            return false;
	}
	
    function get_select() {
        $fields     = explode(',', POS_FIELD);
        $pos_kode = '';
        $fs = '';
        foreach ($fields as $f) {
            $fs = $f;
            if ($f == 'kd_kanwil_bank')
                $fs = 'kd_kanwil';
            else if ($f == 'kd_kppbb_bank')
                $fs = 'kd_kppbb';

            $pos_kode .= "tp.{$fs}||";
        }
        $pos_kode = substr($pos_kode, 0, -2);
    
    
        $sql   = "select {$pos_kode} kode, tp.nm_tp from tempat_pembayaran tp";
        $query = $this->db->query($sql);
        
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
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