<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class tp_model extends CI_Model {
	private $tbl = 'tempat_pembayaran';
	
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
    
    $sql = " SELECT kd_propinsi||'.'||kd_dati2||'.'||kd_kecamatan||'.'||kd_kelurahan as kode, nm_kelurahan as uraian
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
	
	function get_array($kec_kd='')
	{
		
		if ($kec_kd!='')
		{
			$where = "AND (kd_kecamatan='$kec_kd')";
		} else $where='';

		$sql     = " SELECT * FROM ".$this->tbl.
				   " WHERE (1=1) $where
					 ORDER BY kd_propinsi, kd_dati2, kd_kecamatan, kd_kelurahan";
		$qry  = $this->db->query($sql);
		if ($qry->num_rows() > 0)
		{
			return $qry->result_array();
		} else
		{
			return false;
		}
	}
	
	private function fvalidation() {
		$this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('nop', 'NOP', 'required');
		$this->form_validation->set_rules('tahun', 'Tahun', 'required|numeric');
	}
	
	function get_nama($kd_kanwil, $kd_kantor, $kd_tp='')
	{
		if ($kd_kanwil && $kd_kantor && $kd_tp)
		{
			$sql = " SELECT * FROM ".$this->tbl.
				   " WHERE kd_kanwil='$kd_kanwil' 
				           and kd_kantor='$kd_kantor'
						   and kd_tp='$kd_tp'";
			$qry  = $this->db->query($sql);
			if ($qry->num_rows() > 0)
			{
				$result = $qry->row(0);
				return $result->nm_tp;
			} else
			{
				return false;
			}
		}
	}
}

/* End of file _model.php */