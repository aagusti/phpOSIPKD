<?php
class pbbEisModel extends CI_Model {

	var $today_amount=999999999999;
	var $today_trans=10;
	var $today_cap='Hari Ini';
	
	var $week_amount=999999999999;
	var $week_trans=10;
	var $week_cap='Minggu Ini';
	
	var $month_amount=999999999999;
	var $month_trans=10;
	var $month_cap='Bulan Ini';
	
	var $year_amount=999999999999;
	var $year_trans=10;
	var $year_cap='Tahun Ini';
  
  var $curdate='now()'; #//new_time(sysdate,'GMT','PST')
	
	function __construct()
	{
		parent::__construct();
	}

	function today_pbb()
	{
    $curdate=$this->curdate;
		$this->db->select('count(*) jumlah_transaksi, sum(jml_sppt_yg_dibayar) amount_transaksi');
		$this->db->from('pembayaran_sppt');
		$this->db->where("tgl_pembayaran_sppt) = TO_DATE($curdate, 'YYYYMMDD')");
		
		/* Cek session */
		if($this->session->userdata('user_id')) {
			$this->db->where('kd_propinsi', get_user_pro_kd());
			$this->db->where('kd_dati2', get_user_kab_kd());
			
			/* Kode Kecamatan */
			if(get_user_kec_kd() != '000') {
				$this->db->where('kd_kecamatan', get_user_kec_kd());
			}
			
			/* Kode Kelurahan */
			if(get_user_kel_kd() != '000') {
				$this->db->where('kd_kelurahan', get_user_kel_kd());
			}
		}
		
		$result = $this->db->get();
			
		if($result->num_rows() > 0) {
			return $result->row_array();
		} else {
			return NULL;
		}
	}
	
	function week_pbb()
	{
    $curdate=$this->curdate;
		$this->db->select('count(*) jumlah_transaksi, sum(jml_sppt_yg_dibayar) amount_transaksi');
		$this->db->from('pembayaran_sppt');
		$this->db->where("tgl_pembayaran_sppt BETWEEN TO_DATE(date($curdate)-7, 'YYYYMMDD') AND TO_DATE($curdate, 'YYYYMMDD')");
		
		/* Cek session */
		if($this->session->userdata('user_id')) {
			$this->db->where('kd_propinsi', get_user_pro_kd());
			$this->db->where('kd_dati2', get_user_kab_kd());
			
			/* Kode Kecamatan */
			if(get_user_kec_kd() != '000') {
				$this->db->where('kd_kecamatan', get_user_kec_kd());
			}
			
			/* Kode Kelurahan */
			if(get_user_kel_kd() != '000') {
				$this->db->where('kd_kelurahan', get_user_kel_kd());
			}
		}
		
		$result = $this->db->get();
		
		if($result->num_rows() > 0) {
			return $result->row_array();
		} else {
			return NULL;
		}
	}
	
	function month_pbb()
	{
   $curdate=$this->curdate;
		$this->db->select('count(*) jumlah_transaksi, sum(jml_sppt_yg_dibayar) amount_transaksi');
		$this->db->from('pembayaran_sppt');
		$this->db->where("tgl_pembayaran_sppt = TO_DATE($curdate, 'YYYYMM')");
		
		/* Cek session */
		if($this->session->userdata('user_id')) {
			$this->db->where('kd_propinsi', get_user_pro_kd());
			$this->db->where('kd_dati2', get_user_kab_kd());
			
			/* Kode Kecamatan */
			if(get_user_kec_kd() != '000') {
				$this->db->where('kd_kecamatan', get_user_kec_kd());
			}
			
			/* Kode Kelurahan */
			if(get_user_kel_kd() != '000') {
				$this->db->where('kd_kelurahan', get_user_kel_kd());
			}
		}
		
		$result = $this->db->get();
		
		if($result->num_rows() > 0) {
			return $result->row_array();
		} else {
			return NULL;
		}
	}
	
	function year_pbb()
	{
    $curdate=$this->curdate;
		$this->db->select('count(*) jumlah_transaksi, sum(jml_sppt_yg_dibayar) amount_transaksi');
		$this->db->from('pembayaran_sppt');
		$this->db->where("TO_CHAR(tgl_pembayaran_sppt,'YYYY') = TO_CHAR($curdate, 'YYYY')");
		
		/* Cek session */
		if($this->session->userdata('user_id')) {
			$this->db->where('kd_propinsi', get_user_pro_kd());
			$this->db->where('kd_dati2', get_user_kab_kd());
			
			/* Kode Kecamatan */
			if(get_user_kec_kd() != '000') {
				$this->db->where('kd_kecamatan', get_user_kec_kd());
			}
			
			/* Kode Kelurahan */
			if(get_user_kel_kd() != '000') {
				$this->db->where('kd_kelurahan', get_user_kel_kd());
			}
		}
		
		$result = $this->db->get();
		
		if($result->num_rows() > 0) {
			return $result->row_array();
		} else {
			return NULL;
		}
	}
	
}
 