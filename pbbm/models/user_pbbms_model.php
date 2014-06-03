<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class user_pbbms_model extends CI_Model
{
    private $tbl = 'user_pbbms';
    
    function __construct()
    {
        parent::__construct();
    }
    
    function get_all()
    {
        $sql = "select u.nama, u.disabled, u.created, up.user_id, up.kd_propinsi, up.kd_dati2, up.kd_kecamatan, up.kd_kelurahan, kel.nm_kelurahan, kec.nm_kecamatan
            from users u 
            inner join user_pbbms up on u.id=up.user_id
            left join ref_kecamatan kec on up.kd_propinsi=kec.kd_propinsi and up.kd_dati2=kec.kd_dati2 and up.kd_kecamatan=kec.kd_kecamatan
            left join ref_kelurahan kel on up.kd_propinsi=kel.kd_propinsi and up.kd_dati2=kel.kd_dati2 and up.kd_kecamatan=kel.kd_kecamatan and up.kd_kelurahan=kel.kd_kelurahan 
            order by u.disabled desc, u.nama";
        
        $query = $this->db->query($sql);
        if ($query->num_rows() !== 0) {
            return $query->result();
        } else
            return FALSE;
    }
    
    function get($id)
    {
        $this->db->where('user_id', $id);
        $query = $this->db->get($this->tbl);
        if ($query->num_rows() !== 0) {
            return $query->row();
        } else
            return FALSE;
    }
    
    function get_by_kec_kel($kec_kd, $kel_kd)
    {
        $sql   = "SELECT u.* 
			FROM users u 
			INNER JOIN user_pbbms up ON u.id=up.user_id
			WHERE up.kd_kecamatan='{$kec_kd}' AND up.kd_kelurahan='{$kel_kd}'";
        $query = $this->db->query($sql);
        if ($query->num_rows() !== 0) {
            return $query->row();
        } else
            return FALSE;
    }
    
    //-- admin
    function save($data)
    {
        $this->db->insert($this->tbl, $data);
        // return $this->db->insert_id();
    }
    
    function update($id, $data)
    {
        $this->db->where('user_id', $id);
        $this->db->update($this->tbl, $data);
    }
    
    function delete($id)
    {
        $this->db->where('user_id', $id);
        $this->db->delete($this->tbl);
    }
}

/* End of file _model.php */