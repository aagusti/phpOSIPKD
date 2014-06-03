<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class posuser_model extends CI_Model
{
    private $tbl = 'user_pos';
    
    function __construct()
    {
        parent::__construct();
    }
    
    function set_user()
    {
      $id  
        $this->db->insert($this->tbl, $data);
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
