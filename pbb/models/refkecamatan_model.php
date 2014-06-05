<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class refkecamatan_model extends CI_Model {
	private $tbl = 'ref_kecamatan';
	
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
    
    $sql = " SELECT kd_propinsi||'.'||kd_dati2||'.'||kd_kecamatan as kode, nm_kecamatan as uraian
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

	function kode($kode='')
	{
		$sql     = " SELECT *  FROM ".$this->tbl.
				   " WHERE kd_kecamatan='$kode'";
		$qry  = $this->db->query($sql);
		if ($qry->num_rows()>0)
		{
			$result['query']      = $qry->row_array(0);
			$result['found']      = '1';
		}else
		{
			$result['found']      = '0';
		}
		return $result;
	}

}

/* End of file _model.php */