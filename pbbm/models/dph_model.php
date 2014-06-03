<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class dph_model extends CI_Model
{
    private $tbl = 'dph';
    
    function __construct()
    {
        parent::__construct();
    }
	
	function last_kode() {
		$this->db->select_max('kode', 'nomor');
		// $this->db->where('tahun',$tahun);
		$kode = $this->db->get($this->tbl)->row()->nomor;
		$kode++;
		
		return str_pad($kode, 3, "0", STR_PAD_LEFT);;
	}
	
	function get_user_pbbms() {
		$sql = "SELECT u.* 
			FROM users u 
			INNER JOIN user_pbbms up ON u.id=up.user_id";
		return $this->db->query($sql)->result();
	}
	
	function get_nop_blok($thn,$is_nop=false,$r1=false,$r2=false) {
		$dt1      = explode('.', $r1);
		$kec      = $dt1[2];
		$kel      = $dt1[3];
		$blok1    = $dt1[4];
		$no_urut1 = @$dt1[5];
		
		if ($r2) {
			$dt2      = explode('.', $r2);
			$blok2    = $dt2[0];
			$no_urut2 = @$dt2[1];
		}
		
		$this->db->where('status_pembayaran_sppt','0');
		$this->db->where('thn_pajak_sppt',$thn);
		$this->db->where('kd_kecamatan',$kec);
		$this->db->where('kd_kelurahan',$kel);
		
		if ($r2) 
			$this->db->where("kd_blok between '{$blok1}' and '{$blok2}' ");
		else
			$this->db->where("kd_blok", $blok1);
			
		if ($is_nop) 
			if ($r2 && $no_urut2)
				$this->db->where("no_urut between '{$no_urut1}' and '{$no_urut2}' ");
			else
				$this->db->where("no_urut", $no_urut1);
		
		$query = $this->db->get('sppt');
		if($query->num_rows()!==0)
		{
			return $query->result_array();
		}
		else
			return FALSE;
	}
	
	function cek_nop_thn($nop_thn) {
		// 32.03.030.004.019.0264.0-2012
		$data1 = explode('.', $nop_thn);
		$data2 = explode('-', $data1[6]);
		
		$this->db->where('kd_propinsi', $data1[0]);
		$this->db->where('kd_dati2', $data1[1]);
		$this->db->where('kd_kecamatan', $data1[2]);
		$this->db->where('kd_kelurahan', $data1[3]);
		$this->db->where('kd_blok', $data1[4]);
		$this->db->where('no_urut', $data1[5]);
		$this->db->where('kd_jns_op', $data2[0]);
		$this->db->where('thn_pajak_sppt', $data2[1]);
		
		$query = $this->db->get('dph_payment');
		if($query->num_rows()!==0)
			return 'ada';
		else
			return 'tidak ada';
	}
	
	function get_detail($dph_id) {
		$this->db->select('kd_propinsi, kd_dati2, kd_kecamatan, kd_kelurahan, 
			kd_blok, no_urut, kd_jns_op, thn_pajak_sppt, pembayaran_ke, denda, 
			jml_yg_dibayar');
		$this->db->where('dph_id', $dph_id);
		$query = $this->db->get('dph_payment');
		if($query->num_rows()!==0)
			return $query->result_array();
		else
			return false;
	}
	
	function get_detail2($dph_id) { //untuk laporan
		$ret = array();
		$sql = "SELECT d.id, d.kd_kecamatan||'-'||d.kd_kelurahan||'-'||d.tahun||'-'||d.kode as kode_pmb , kec.nm_kecamatan as kec, kel.nm_kelurahan as kel,
      u1.nama pejabat1, u2.nama pejabat2 , u1.nip nip1, u2.nip nip2, u1.jabatan as jabatan1, u2.jabatan as jabatan2
			FROM dph d
			INNER JOIN ref_kecamatan kec ON kec.kd_propinsi=d.kd_propinsi AND kec.kd_dati2=d.kd_dati2 AND kec.kd_kecamatan=d.kd_kecamatan 
			INNER JOIN ref_kelurahan kel ON kel.kd_propinsi=d.kd_propinsi AND kel.kd_dati2=d.kd_dati2 AND kel.kd_kecamatan=d.kd_kecamatan AND kel.kd_kelurahan=d.kd_kelurahan 
			LEFT JOIN users u1 on u1.id=d.pejabat1_id
			LEFT JOIN users u2 on u2.id=d.pejabat2_id
      WHERE d.id={$dph_id}";
			
		$query = $this->db->query($sql);
		if($query->num_rows()!==0)
			$ret['header'] = $query->row();
		else
			$ret['header'] = false;

        $sql = "SELECT dp.kd_propinsi||'.'||dp.kd_dati2||'.'||dp.kd_kecamatan||'.'||dp.kd_kelurahan||'.'||dp.kd_blok||'.'||dp.no_urut||'.'||dp.kd_jns_op as nop, 
			s.nm_wp_sppt as pemilik, s.tgl_terbit_sppt as tanggal, s.pbb_yg_harus_dibayar_sppt as pokok, dp.denda as denda, s.pbb_yg_harus_dibayar_sppt + dp.denda as bayar,

			dp.kd_propinsi, dp.kd_dati2, dp.kd_kecamatan, dp.kd_kelurahan, 
			dp.kd_blok, dp.no_urut, dp.kd_jns_op, dp.thn_pajak_sppt, dp.pembayaran_ke, dp.denda, 
			dp.jml_yg_dibayar, dp.tgl_rekam_byr, dp.nip_rekam_byr
			FROM dph_payment dp
			INNER JOIN sppt s 
				ON dp.kd_propinsi=s.kd_propinsi 
				AND dp.kd_dati2=s.kd_dati2 
				AND dp.kd_kecamatan=s.kd_kecamatan 
				AND dp.kd_kelurahan=s.kd_kelurahan
				AND dp.kd_blok=s.kd_blok 
				AND dp.no_urut=s.no_urut 
				AND dp.kd_jns_op=s.kd_jns_op 
				AND dp.thn_pajak_sppt=s.thn_pajak_sppt
			WHERE dp.dph_id={$dph_id} ";
			
		$query = $this->db->query($sql);
		if($query->num_rows()!==0)
			$ret['detail'] = $query->result();
		else
			$ret['detail'] = false;
			
		return $ret;
	}
	
	function update_stat($id) {
        $this->db->where('id', $id);
        $this->db->update($this->tbl, array('tgl_posting'=>date('Y-m-d'), 'status_posting'=>1));
	}
	
	function cek_stat($id) {
        $this->db->where('id', $id);
        $this->db->where('status_posting', 1);
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
			return TRUE; //sudah posting
		else
			return FALSE; //belum posting
	}
	
	function get($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->row();
		}
		else
			return FALSE;
	}
    
    function save($data)
    {
        $this->db->insert($this->tbl, $data);
		return $this->db->insert_id();
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
