<?php
class tp_model extends CI_Model {

var $tables ='tempat_pembayaran';

    function __construct() {
        parent::__construct();
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
}
