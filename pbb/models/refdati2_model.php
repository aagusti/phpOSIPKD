<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class refdati2_model extends CI_Model {
	private $tbl = 'ref_dati2';
	
	function __construct() {
		parent::__construct();
	}
	
	function tambah($data) {
		$this->db->insert($this->tbl,$data);
	}
	
	function koreksi($kd_propinsi, $data) {
		$this->db->where('kd_propinsi', $kd_propinsi);
		$this->db->update($this->tbl,$data);
	}
	
	function hapus($kd_propinsi) {
		$this->db->where('kd_propinsi', $kd_propinsi);
		$this->db->delete($this->tbl);
	}

	
	
	function data_grid($str_where='', $str_limit='', $str_order_by='', $filter='')
	{
    $sql     = " SELECT COUNT(*) c FROM ".$this->tbl;
    $qry  = $this->db->query($sql)->row(0);
    $tot_rows = $qry->c;
    if ($str_order_by=='')
       $str_order_by='order by kode';
       
    $sql          = " SELECT COUNT(*) c FROM ".$this->tbl.
                    " WHERE (1=1) $str_where $filter  ";
    $qry       = $this->db->query($sql)->row(0);
    $fil_rows = $qry->c;
    
    $sql = " SELECT kd_propinsi||'.'||kd_dati2 as kode, nm_dati2 as uraian
			       FROM ".$this->tbl.
			     " WHERE (1=1) $str_where $filter  
			       $str_order_by 
			       $str_limit";

		$qry = $this->db->query($sql);

		$result['sql']      = $sql;
		$result['query']      = $qry->result_array();
		$result['num_rows'] = $fil_rows;
		$result['tot_rows'] = $tot_rows['c'];
		$result['fil_rows'] = $fil_rows;
		
		return $result;
	}
}

/* End of file _model.php */