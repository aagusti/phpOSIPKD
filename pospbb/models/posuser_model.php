<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class posuser_model extends CI_Model
{
    private $tbl = 'user_pbb';
    
    function __construct()
    {
        parent::__construct();
    }
    
    function set_user()
    {
        $id = $this->session->userdata('userid');
        
        $this->db->where('user_id', $id);
        $query    = $this->db->get($this->tbl);
        $fields   = explode(',', POS_FIELD);
        $where    = '';
        $userbank = '';
        if ($row = $query->result_array()) {
            foreach ($fields as $f) {
                if ($f == 'kd_kanwil_bank')
                    $fs = 'kd_kanwil';
                else if ($f == 'kd_kppbb_bank')
                    $fs = 'kd_kppbb';
                else
                    $fs = $f;
                $this->session->set_userdata($f, $row[0][$fs]);
                $where .= "AND $fs='" . $row[0][$fs] . "'";
            }
            
            if ($row = $this->db->query("SELECT * FROM tempat_pembayaran WHERE (1=1) $where")->row()) {
                $this->session->set_userdata('tpnm', $row->nm_tp);
                $this->session->set_userdata('tpkd', $userbank);
            } else
                return false;
            
            return true;
        } else
            return false;
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
