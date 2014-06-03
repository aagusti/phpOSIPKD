<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class sppt_model extends CI_Model
{
    private $tbl = 'sppt';
    
    function __construct()
    {
        parent::__construct();
    }
    
    function get_all_distinct($filter = '')
    {
        $sql = "select distinct s.kd_propinsi||'.'||s.kd_dati2||'.'||s.kd_kecamatan||'.'||s.kd_kelurahan||'.'||s.kd_blok||'.'||s.no_urut||'.'||s.kd_jns_op nop, nm_wp_sppt, jln_wp_sppt
		    from sppt s
				where (1=1)" . $filter . "
				order by nop 
				limit 100";
        
        $query = $this->db->query($sql);
        if ($query->num_rows() !== 0) {
            return $query->result();
        } else
            return FALSE;
    }
    
    function data_grid($str_where = '', $str_limit = '', $str_order_by = '', $filter = '')
    {
      $sql = "select count(*) c
              from sppt s ";
      $rows= $this->db->query($sql)->row(1);
      $tot_rows = $rows->c;  
      $sql = "select count(*) c
              from sppt s 
			        where (1=1) 
              $str_where ";
      
      $rows= $this->db->query($sql)->row(1);
      $num_rows = $rows->c;  
      
      $sql = "select s.kd_propinsi||'.'||s.kd_dati2||'.'||s.kd_kecamatan||'.'||s.kd_kelurahan||'.'||s.kd_blok||'.'||s.no_urut||'.'||s.kd_jns_op nop, 
                     thn_pajak_sppt, nm_wp_sppt, pbb_yg_harus_dibayar_sppt, status_pembayaran_sppt
			        from sppt s
			        where (1=1) 
			        $str_where 
			        $filter  
			        $str_order_by 
			        $str_limit "; 
        
        $query              = $this->db->query($sql);
        $result['sql']      = $sql;
        $result['query']    = $query->result_array();
        $result['num_rows'] = $str_where != '' ? $num_rows : $tot_rows;
        $result['tot_rows'] = $tot_rows;
        
        return $result;
    }
    
    function data_grid_pmb($nop)
    {
        $nop          = urldecode($nop);
        $nop          = str_replace('.', '', $nop);
        $nop          = str_replace(' ', '', $nop);
        $nop          = str_replace('-', '', $nop);
        $nop          = preg_replace( '/[^0-9]/', '', $nop);
        $kd_propinsi  = substr($nop, 0, 2);
        $kd_dati2     = substr($nop, 2, 2);
        $kd_kecamatan = substr($nop, 4, 3);
        $kd_kelurahan = substr($nop, 7, 3);
        $kd_blok      = substr($nop, 10, 3);
        $no_urut      = substr($nop, 13, 4);
        $kd_jns_op    = substr($nop, 17, 1);
        
        $sql = "select thn_pajak_sppt, pbb_yg_harus_dibayar_sppt, case when status_pembayaran_sppt = '1' then 'Sudah' else 'Belum' end status_pembayaran_sppt
			from sppt s
			where s.kd_propinsi='$kd_propinsi' and s.kd_dati2='$kd_dati2' and s.kd_kecamatan='$kd_kecamatan' and 
			      s.kd_kelurahan='$kd_kelurahan' and s.kd_blok='$kd_blok' and s.no_urut='$no_urut' and s.kd_jns_op = '$kd_jns_op'
			group by thn_pajak_sppt, pbb_yg_harus_dibayar_sppt, status_pembayaran_sppt
			order by 1 ";
        
        $query = $this->db->query($sql);
        if ($query->num_rows() !== 0) {
            return $query->result();
        } else
            return FALSE;
    }
    
    function get_by_nop_thn($nop, $thn)
    {
        $nop          = urldecode($nop);
        $nop          = str_replace('.', '', $nop);
        $nop          = str_replace(' ', '', $nop);
        $nop          = str_replace('-', '', $nop);
        $nop          = preg_replace( '/[^0-9]/', '', $nop);

        $kd_propinsi  = substr($nop, 0, 2);
        $kd_dati2     = substr($nop, 2, 2);
        $kd_kecamatan = substr($nop, 4, 3);
        $kd_kelurahan = substr($nop, 7, 3);
        $kd_blok      = substr($nop, 10, 3);
        $no_urut      = substr($nop, 13, 4);
        $kd_jns_op    = substr($nop, 17, 1);
        
        $sql = "select s.nm_wp_sppt, s.jln_wp_sppt, s.rt_wp_sppt, s.rw_wp_sppt, s.kelurahan_wp_sppt, s.kota_wp_sppt, s.npwp_sppt,
			     s.pbb_terhutang_sppt, s.faktor_pengurang_sppt, s.pbb_yg_harus_dibayar_sppt, s.tgl_jatuh_tempo_sppt,
			     coalesce(sum(ps.denda_sppt),0) denda_sppt,
			     coalesce(sum(jml_sppt_yg_dibayar),0) jml_sppt_yg_dibayar
			from sppt s
			     left join pembayaran_sppt ps on
			        s.kd_propinsi=ps.kd_propinsi and s.kd_dati2=ps.kd_dati2 and s.kd_kecamatan=ps.kd_kecamatan and 
			        s.kd_kelurahan=ps.kd_kelurahan and s.kd_blok=ps.kd_blok and s.no_urut=ps.no_urut and s.kd_jns_op = ps.kd_jns_op
			        and s.thn_pajak_sppt = ps.thn_pajak_sppt  
			where s.kd_propinsi='$kd_propinsi' and s.kd_dati2='$kd_dati2' and s.kd_kecamatan='$kd_kecamatan' and 
			      s.kd_kelurahan='$kd_kelurahan' and s.kd_blok='$kd_blok' and s.no_urut='$no_urut' and s.kd_jns_op = '$kd_jns_op'
			      and s.thn_pajak_sppt = '$thn' and s.status_pembayaran_sppt='0'
			group  by s.nm_wp_sppt, s.jln_wp_sppt, s.rt_wp_sppt, s.rw_wp_sppt, s.kelurahan_wp_sppt, s.kota_wp_sppt, s.npwp_sppt,
			     s.pbb_terhutang_sppt, s.faktor_pengurang_sppt, s.pbb_yg_harus_dibayar_sppt, s.tgl_jatuh_tempo_sppt ";
        
        $query = $this->db->query($sql);
        
        
        if ($query->num_rows() !== 0) {
            return $query->row();
            
        } else
            return FALSE;
    }
    
    function get_by_blok_thn($blok, $thn)
    {
      $blok          = urldecode($blok);
      $blok          = str_replace('.', '', $blok);
      $blok          = str_replace(' ', '', $blok);
      $blok          = str_replace('-', '', $blok);
      $blok          = preg_replace( '/[^0-9]/', '', $blok);

      $kd_propinsi  = substr($blok, 0, 2);
      $kd_dati2     = substr($blok, 2, 2);
      $kd_kecamatan = substr($blok, 4, 3);
      $kd_kelurahan = substr($blok, 7, 3);
      $kd_blok      = substr($blok, 10, 3);
      
      $sql = "select s.kd_propinsi||'.'|| s.kd_dati2||'.'||s.kd_kecamatan||'.'|| s.kd_kelurahan||'-'
         ||s.kd_blok||'.'|| s.no_urut||'.'||s.kd_jns_op as kode, s.thn_pajak_sppt, 
         s.nm_wp_sppt, s.jln_wp_sppt, s.rt_wp_sppt, s.rw_wp_sppt, 
         s.kelurahan_wp_sppt, s.kota_wp_sppt, s.npwp_sppt,  s.pbb_terhutang_sppt, s.faktor_pengurang_sppt, 
         s.pbb_yg_harus_dibayar_sppt, s.tgl_jatuh_tempo_sppt,  coalesce(sum(ps.denda_sppt),0) denda_sppt,
           coalesce(sum(jml_sppt_yg_dibayar),0) jml_sppt_yg_dibayar, s.no_urut, s.kd_jns_op 
        from sppt s
           left join pembayaran_sppt ps on
              s.kd_propinsi=ps.kd_propinsi and s.kd_dati2=ps.kd_dati2 and s.kd_kecamatan=ps.kd_kecamatan and 
              s.kd_kelurahan=ps.kd_kelurahan and s.kd_blok=ps.kd_blok and s.no_urut=ps.no_urut and 
              s.kd_jns_op = ps.kd_jns_op  and s.thn_pajak_sppt = ps.thn_pajak_sppt  
        where s.kd_propinsi='$kd_propinsi' and s.kd_dati2='$kd_dati2' and s.kd_kecamatan='$kd_kecamatan' and 
              s.kd_kelurahan='$kd_kelurahan' and s.kd_blok='$kd_blok' 
              and s.thn_pajak_sppt = '$thn' and s.status_pembayaran_sppt='0'
          
        group  by  s.kd_propinsi, s.kd_dati2, s.kd_kecamatan, s.kd_kelurahan, s.kd_blok, s.no_urut, 
              s.kd_jns_op, s.thn_pajak_sppt, s.nm_wp_sppt, s.jln_wp_sppt, s.rt_wp_sppt, s.rw_wp_sppt, 
              s.kelurahan_wp_sppt, s.kota_wp_sppt, s.npwp_sppt, s.pbb_terhutang_sppt, s.faktor_pengurang_sppt, 
              s.pbb_yg_harus_dibayar_sppt, s.tgl_jatuh_tempo_sppt 
        having 
              s.pbb_yg_harus_dibayar_sppt-(coalesce(sum(jml_sppt_yg_dibayar),0)-coalesce(sum(ps.denda_sppt),0))>0
      ";
  //die($sql);
      $query = $this->db->query($sql);

      $result['sql']      = $sql;
      $result['query']    = $query->result_array();
      $result['num_rows'] = $query->num_rows();
      $result['tot_rows'] = $query->num_rows();
		
      return $result;
    }

    function get_by_range_thn($blok, $blok2, $thn)
    {
      $blok          = urldecode($blok);
      $blok          = str_replace('.', '', $blok);
      $blok          = str_replace(' ', '', $blok);
      $blok          = str_replace('-', '', $blok);
      $blok          = preg_replace( '/[^0-9]/', '', $blok);

      $kd_propinsi  = substr($blok, 0, 2);
      $kd_dati2     = substr($blok, 2, 2);
      $kd_kecamatan = substr($blok, 4, 3);
      $kd_kelurahan = substr($blok, 7, 3);
      $kd_blok      = substr($blok, 10, 3);
      $no_urut      = substr($blok, 13, 4);
      $kd_jenis      = substr($blok, 17, 1);
      
      $blok2          = urldecode($blok2);
      $blok2          = str_replace('.', '', $blok2);
      $blok2          = str_replace(' ', '', $blok2);
      $blok2          = str_replace('-', '', $blok2);
      $blok2          = preg_replace( '/[^0-9]/', '', $blok2);

      $no_urut_2      = substr($blok2, 0, 4);
      $kd_jenis_2     = substr($blok2, 4, 1);

      
      $sql = "select s.kd_propinsi||'.'|| s.kd_dati2||'.'||s.kd_kecamatan||'.'|| s.kd_kelurahan||'-'
         ||s.kd_blok||'.'|| s.no_urut||'.'||s.kd_jns_op as kode, s.thn_pajak_sppt, 
         s.nm_wp_sppt, s.jln_wp_sppt, s.rt_wp_sppt, s.rw_wp_sppt, 
         s.kelurahan_wp_sppt, s.kota_wp_sppt, s.npwp_sppt,  s.pbb_terhutang_sppt, s.faktor_pengurang_sppt, 
         s.pbb_yg_harus_dibayar_sppt, s.tgl_jatuh_tempo_sppt,  coalesce(sum(ps.denda_sppt),0) denda_sppt,
           coalesce(sum(jml_sppt_yg_dibayar),0) jml_sppt_yg_dibayar, s.no_urut, s.kd_jns_op 
        from sppt s
           left join pembayaran_sppt ps on
              s.kd_propinsi=ps.kd_propinsi and s.kd_dati2=ps.kd_dati2 and s.kd_kecamatan=ps.kd_kecamatan and 
              s.kd_kelurahan=ps.kd_kelurahan and s.kd_blok=ps.kd_blok and s.no_urut=ps.no_urut and 
              s.kd_jns_op = ps.kd_jns_op  and s.thn_pajak_sppt = ps.thn_pajak_sppt  
        where s.kd_propinsi='$kd_propinsi' and s.kd_dati2='$kd_dati2' and s.kd_kecamatan='$kd_kecamatan' and 
              s.kd_kelurahan='$kd_kelurahan' and s.kd_blok='$kd_blok' and s.no_urut BETWEEN '$no_urut' AND '$no_urut_2' 
              and s.thn_pajak_sppt = '$thn' and s.status_pembayaran_sppt='0'
        group  by  s.kd_propinsi, s.kd_dati2, s.kd_kecamatan, s.kd_kelurahan, s.kd_blok, s.no_urut, 
              s.kd_jns_op, s.thn_pajak_sppt, s.nm_wp_sppt, s.jln_wp_sppt, s.rt_wp_sppt, s.rw_wp_sppt, 
              s.kelurahan_wp_sppt, s.kota_wp_sppt, s.npwp_sppt, s.pbb_terhutang_sppt, s.faktor_pengurang_sppt, 
              s.pbb_yg_harus_dibayar_sppt, s.tgl_jatuh_tempo_sppt 
        having 
              s.pbb_yg_harus_dibayar_sppt-(coalesce(sum(jml_sppt_yg_dibayar),0)-coalesce(sum(ps.denda_sppt),0))>0
      ";
      $query = $this->db->query($sql);

      $result['sql']      = $sql;
      $result['query']    = $query->result_array();
      $result['num_rows'] = $query->num_rows();
      $result['tot_rows'] = $query->num_rows();
		
      return $result;
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
