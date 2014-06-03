<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class posuser_model extends CI_Model
{
    private $tbl = 'user_pos';
    
    function __construct()
    {
        parent::__construct();
    }
    
    function set_userbank()
    {
        $id = $this->session->userdata('userid');
        
        $this->db->where('user_id', $id);
        $query = $this->db->get($this->tbluser);
        
        if ($row = $query->row())
            $userbank = KD_PROPINSI . KD_DATI2 . $row->kd_kecamatan . $row->kd_kelurahan;
        else
            $userarea = KD_PROPINSI . KD_DATI2 . '000000';
        
        $this->session->set_userdata('user_bank', $userarea);
        
        return $userarea;
    }
    
    function save($data)
    {
        $this->db->insert($this->tbl, $data);
    }
    
    function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->tbl, $data);
    }
    
    function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->tbl);
    }
}

/* End of file _model.php */
