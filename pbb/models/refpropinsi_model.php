<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class refpropinsi_model extends CI_Model {
	private $tbl = 'ref_propinsi';
	
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

	
	function get_all_distinct($filter='')
	{
		$sql = "select distinct s.kd_propinsi||'.'||s.kd_dati2||'.'||s.kd_kecamatan||'.'||s.kd_kelurahan
		               ||'.'||s.kd_blok||'.'||s.no_urut||'.'||s.kd_jns_op nop, nm_wp_sppt, jln_wp_sppt
		    from sppt s
				where (1=1)".$filter."
				order by nop 
				limit 100";
				
		$query = $this->db->query($sql);
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}
			
	function data_grid($str_where='', $str_limit='', $str_order_by='propinsi_kd', $filter='')
	{
    $sql     = " SELECT COUNT(*) c FROM ".$this->tbl;
    $qry  = $this->db->query($sql)->row(0);
    $tot_rows = $qry->c;
    
    $sql          = " SELECT COUNT(*) c FROM ".$this->tbl.
                    " WHERE (1=1) $str_where $filter  ";
    $qry       = $this->db->query($sql)->row(0);
    $fil_rows = $qry->c;
    
    $sql = " SELECT *
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