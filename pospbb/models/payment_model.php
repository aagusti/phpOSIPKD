<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class payment_model extends CI_Model {
	private $tbl = 'sppt';
	
	function __construct() {
		parent::__construct();
	}
	
	function get_pembayaran_ke($nop, $thn) {
	  $nop=str_replace('.','',$nop);
	  $nop=str_replace(' ','',$nop);
	  $nop=str_replace('-','',$nop);
	  $kd_propinsi=substr($nop,0,2);
	  $kd_dati2=substr($nop,2,2);
	  $kd_kecamatan=substr($nop,4,3);
	  $kd_kelurahan=substr($nop,7,3);
	  $kd_blok=substr($nop,10,3);
	  $no_urut=substr($nop,13,4);
	  $kd_jns_op=substr($nop,17,1);

		$sql = "select coalesce(max(pembayaran_sppt_ke),0)+1 jml
			from pembayaran_sppt ps
			where ps.kd_propinsi='$kd_propinsi' and ps.kd_dati2='$kd_dati2' and ps.kd_kecamatan='$kd_kecamatan' and 
			      ps.kd_kelurahan='$kd_kelurahan' and ps.kd_blok='$kd_blok' and ps.no_urut='$no_urut' and ps.kd_jns_op = '$kd_jns_op'
			      and ps.thn_pajak_sppt = '$thn'";
    
		$query = $this->db->query($sql);
    $nval=$query->row();
    $nva=$nval->jml;
    return $nva;
	}
	
	function update_pmb($data) {
		$this->db->insert('pembayaran_sppt',$data);
	}
	
	function get_by_nop_thn_ke($nop, $thn, $ke) {

	  $nop=urldecode($nop);
	  $nop=str_replace('.','',$nop);
	  $nop=str_replace(' ','',$nop);
	  $nop=str_replace('-','',$nop);
	  $kd_propinsi=substr($nop,0,2);
	  $kd_dati2=substr($nop,2,2);
	  $kd_kecamatan=substr($nop,4,3);
	  $kd_kelurahan=substr($nop,7,3);
	  $kd_blok=substr($nop,10,3);
	  $no_urut=substr($nop,13,4);
	  $kd_jns_op=substr($nop,17,1);
  
	  $fields=explode(',',POS_FIELD);
	  $field="";
	  $join ="";

	  foreach ($fields as $f)
	  {
	     $f=trim($f);
	     $join .= " AND ps.$f=tp.$f "; 
	     $field.= "ps.$f ,";
	  };	  
    $join = str_replace('tp.kd_kppbb_bank','tp.kd_kppbb',$join);
    $join = str_replace('tp.kd_kanwil_bank','tp.kd_kanwil',$join);
	  
		$sql = "select s.kd_propinsi, s.kd_dati2, s.kd_kecamatan, s.kd_kelurahan, s.kd_blok, s.no_urut, s.kd_jns_op , 
		               s.thn_pajak_sppt, ps.pembayaran_sppt_ke, s.nm_wp_sppt, s.jln_wp_sppt, s.rt_wp_sppt, s.rw_wp_sppt, 
		               s.kelurahan_wp_sppt, s.kota_wp_sppt, s.npwp_sppt, ps.tgl_pembayaran_sppt, ps.denda_sppt, 
			     s.pbb_terhutang_sppt, s.faktor_pengurang_sppt, s.pbb_yg_harus_dibayar_sppt, s.tgl_jatuh_tempo_sppt,
			     ps.denda_sppt  denda_sppt, ps.jml_sppt_yg_dibayar  jml_sppt_yg_dibayar,  kec.nm_kecamatan, kel.nm_kelurahan,
			     s.tgl_jatuh_tempo_sppt, s.luas_bumi_sppt, s.luas_bng_sppt, $field tp.nm_tp
			from sppt s
			     inner join pembayaran_sppt ps on
			        s.kd_propinsi=ps.kd_propinsi and s.kd_dati2=ps.kd_dati2 and s.kd_kecamatan=ps.kd_kecamatan and 
			        s.kd_kelurahan=ps.kd_kelurahan and s.kd_blok=ps.kd_blok and s.no_urut=ps.no_urut and s.kd_jns_op = ps.kd_jns_op
			        and s.thn_pajak_sppt = ps.thn_pajak_sppt
			     inner join ref_kecamatan kec on
			        s.kd_propinsi=kec.kd_propinsi and s.kd_dati2=kec.kd_dati2 and s.kd_kecamatan=kec.kd_kecamatan 
		       inner join ref_kelurahan kel on
			        s.kd_propinsi=kel.kd_propinsi and s.kd_dati2=kel.kd_dati2 and s.kd_kecamatan=kel.kd_kecamatan 
			        and s.kd_kelurahan=kel.kd_kelurahan 
			     left join tempat_pembayaran tp on 1=1 $join
		  where s.kd_propinsi='$kd_propinsi' and s.kd_dati2='$kd_dati2' and s.kd_kecamatan='$kd_kecamatan' and 
			      s.kd_kelurahan='$kd_kelurahan' and s.kd_blok='$kd_blok' and s.no_urut='$no_urut' and s.kd_jns_op = '$kd_jns_op'
			      and s.thn_pajak_sppt = '$thn' and ps.pembayaran_sppt_ke='$ke' ";
    $query = $this->db->query($sql);
   
		if($query->num_rows()!=0)
		{
			return $query->row();
		}
		else
      return FALSE;
	}

	function cancel_nop_thn_ke($nop, $thn, $ke) {
	  $nop=urldecode($nop);
	  $nop=str_replace('.','',$nop);
	  $nop=str_replace(' ','',$nop);
	  $nop=str_replace('-','',$nop);

	  $kd_propinsi=substr($nop,0,2);
	  $kd_dati2=substr($nop,2,2);
	  $kd_kecamatan=substr($nop,4,3);
	  $kd_kelurahan=substr($nop,7,3);
	  $kd_blok=substr($nop,10,3);
	  $no_urut=substr($nop,13,4);
	  $kd_jns_op=substr($nop,17,1);
	  
	  /*$fields=explode(',',POS_FIELD);
	  $field="";
	  $join ="";
	  foreach ($fields as $f)
	  {
	     $f=trim($f);
	     $join .= " AND ps.$f=tp.$f "; 
	     $field.= "ps.$f ,";
	  };*/	  
	  
		$sql = "update pembayaran_sppt set denda_sppt=0, jml_sppt_yg_dibayar=0
		   where kd_propinsi='$kd_propinsi' and kd_dati2='$kd_dati2' and kd_kecamatan='$kd_kecamatan' and 
			      kd_kelurahan='$kd_kelurahan' and kd_blok='$kd_blok' and no_urut='$no_urut' and kd_jns_op = '$kd_jns_op'
			      and thn_pajak_sppt = '$thn' and pembayaran_sppt_ke='$ke' ";

		$query = $this->db->query($sql);

		$sql = "update sppt set status_pembayaran_sppt='0'
		   where kd_propinsi='$kd_propinsi' and kd_dati2='$kd_dati2' and kd_kecamatan='$kd_kecamatan' and 
			      kd_kelurahan='$kd_kelurahan' and kd_blok='$kd_blok' and no_urut='$no_urut' and kd_jns_op = '$kd_jns_op'
			      and thn_pajak_sppt = '$thn'  ";
   
		$query = $this->db->query($sql);

	}
		
	    function get_salinan($nop, $thn)
    {
        
        $nop          = str_replace('.', '', $nop);
        $nop          = str_replace(' ', '', $nop);
        $nop          = str_replace('-', '', $nop);
        $kd_propinsi  = substr($nop, 0, 2);
        $kd_dati2     = substr($nop, 2, 2);
        $kd_kecamatan = substr($nop, 4, 3);
        $kd_kelurahan = substr($nop, 7, 3);
        $kd_blok      = substr($nop, 10, 3);
        $no_urut      = substr($nop, 13, 4);
        $kd_jns_op    = substr($nop, 17, 1);
        
        $sql   = "select ps.pembayaran_sppt_ke, s.nm_wp_sppt, s.jln_wp_sppt, s.rt_wp_sppt, s.rw_wp_sppt, s.kelurahan_wp_sppt, s.kota_wp_sppt, s.npwp_sppt,
			s.pbb_terhutang_sppt, s.faktor_pengurang_sppt, s.pbb_yg_harus_dibayar_sppt
			from pembayaran_sppt ps 
			inner join sppt s on ps.kd_propinsi||ps.kd_dati2||ps.kd_kecamatan||ps.kd_kelurahan||ps.kd_blok||ps.no_urut||ps.kd_jns_op||ps.thn_pajak_sppt = s.kd_propinsi||s.kd_dati2||s.kd_kecamatan||s.kd_kelurahan||s.kd_blok||s.no_urut||s.kd_jns_op||s.thn_pajak_sppt
			where ps.kd_propinsi||ps.kd_dati2||ps.kd_kecamatan||ps.kd_kelurahan||ps.kd_blok||ps.no_urut||ps.kd_jns_op = '$nop'
			and ps.thn_pajak_sppt = '$thn'
			order by ps.pembayaran_sppt_ke desc limit 1 ";
        $query = $this->db->query($sql);
        if ($query->num_rows() !== 0) {
            return $query->row();
        } else
            return FALSE;
    }

    // ========================
    
    function get_salinan_masal_by_nop($blok, $blok2, $thn) {
        $fields=explode(',',POS_FIELD);
        $field="";
        $join ="";

        foreach ($fields as $f) {
            $f = trim($f);
            $join .= " AND ps.$f=tp.$f "; 
            $field.= "ps.$f ,";
        };	  
        $join = str_replace('tp.kd_kppbb_bank','tp.kd_kppbb', $join);
        $join = str_replace('tp.kd_kanwil_bank','tp.kd_kanwil', $join);
        $join = substr($join, 5);

        $blok          = urldecode($blok);
        $blok          = str_replace('.', '', $blok);
        $blok          = str_replace(' ', '', $blok);
        $blok          = str_replace('-', '', $blok);
        
        $kd_propinsi   = substr($blok, 0, 2);
        $kd_dati2      = substr($blok, 2, 2);
        $kd_kecamatan  = substr($blok, 4, 3);
        $kd_kelurahan  = substr($blok, 7, 3);
        $kd_blok       = substr($blok, 10, 3);
        $no_urut       = substr($blok, 13, 4);
        $kd_jenis      = substr($blok, 17, 1);

        $blok2          = urldecode($blok2);
        $blok2          = str_replace('.', '', $blok2);
        $blok2          = str_replace(' ', '', $blok2);
        $blok2          = str_replace('-', '', $blok2);
        $no_urut_2      = substr($blok2, 0, 4);
        $kd_jenis_2     = substr($blok2, 4, 1);

        $sql   = "select s.kd_propinsi||'.'|| s.kd_dati2||'.'||s.kd_kecamatan||'.'|| s.kd_kelurahan||'-'||s.kd_blok||'.'|| s.no_urut||'.'||s.kd_jns_op as kode, s.thn_pajak_sppt, 
            s.nm_wp_sppt, s.jln_wp_sppt, s.rt_wp_sppt, s.rw_wp_sppt, s.kelurahan_wp_sppt, s.kota_wp_sppt, s.npwp_sppt,
            s.pbb_terhutang_sppt, s.faktor_pengurang_sppt, s.pbb_yg_harus_dibayar_sppt, jml_sppt_yg_dibayar, s.tgl_jatuh_tempo_sppt,
            ps.pembayaran_sppt_ke, ps.tgl_pembayaran_sppt,
            kec.nm_kecamatan, kel.nm_kelurahan, ps.denda_sppt, s.luas_bumi_sppt, s.luas_bng_sppt,  {$field} nm_tp
                        
            from pembayaran_sppt ps 
            inner join sppt s on ps.kd_propinsi=s.kd_propinsi and ps.kd_dati2=s.kd_dati2 and ps.kd_kecamatan=s.kd_kecamatan and ps.kd_kelurahan=s.kd_kelurahan and ps.kd_blok=s.kd_blok and ps.no_urut=s.no_urut and ps.kd_jns_op=s.kd_jns_op and ps.thn_pajak_sppt=s.thn_pajak_sppt
            inner join ref_kecamatan kec on s.kd_propinsi=kec.kd_propinsi and s.kd_dati2=kec.kd_dati2 and s.kd_kecamatan=kec.kd_kecamatan 
            inner join ref_kelurahan kel on s.kd_propinsi=kel.kd_propinsi and s.kd_dati2=kel.kd_dati2 and s.kd_kecamatan=kel.kd_kecamatan and s.kd_kelurahan=kel.kd_kelurahan 
			left join tempat_pembayaran tp on {$join}
            
            where ps.kd_propinsi='{$kd_propinsi}' and ps.kd_dati2='{$kd_dati2}' and ps.kd_kecamatan='{$kd_kecamatan}' and 
            ps.kd_kelurahan='{$kd_kelurahan}' and ps.kd_blok='{$kd_blok}' and ps.no_urut BETWEEN '{$no_urut}' AND '{$no_urut_2}' 
            and ps.thn_pajak_sppt = '{$thn}' 
            
			order by ps.pembayaran_sppt_ke desc";
            
        $query = $this->db->query($sql);;

        $result['sql']      = $sql;
        $result['query']    = $query->result_array();
        $result['num_rows'] = $query->num_rows();
        $result['tot_rows'] = $query->num_rows();
        
        return $result;
    }    
    
    function get_salinan_masal_by_tgl($tgl1, $tgl2) {
        $fields=explode(',',POS_FIELD);
        $field="";
        $join ="";

        foreach ($fields as $f) {
            $f = trim($f);
            $join .= " AND ps.$f=tp.$f "; 
            $field.= "ps.$f ,";
        };	  
        $join = str_replace('tp.kd_kppbb_bank','tp.kd_kppbb', $join);
        $join = str_replace('tp.kd_kanwil_bank','tp.kd_kanwil', $join);
        $join = substr($join, 5);

        $sql   = "select s.kd_propinsi||'.'|| s.kd_dati2||'.'||s.kd_kecamatan||'.'|| s.kd_kelurahan||'-'||s.kd_blok||'.'|| s.no_urut||'.'||s.kd_jns_op as kode, s.thn_pajak_sppt, 
            s.nm_wp_sppt, s.jln_wp_sppt, s.rt_wp_sppt, s.rw_wp_sppt, s.kelurahan_wp_sppt, s.kota_wp_sppt, s.npwp_sppt,
            s.pbb_terhutang_sppt, s.faktor_pengurang_sppt, s.pbb_yg_harus_dibayar_sppt, jml_sppt_yg_dibayar, s.tgl_jatuh_tempo_sppt,
            ps.pembayaran_sppt_ke, ps.tgl_pembayaran_sppt,
            kec.nm_kecamatan, kel.nm_kelurahan, ps.denda_sppt, s.luas_bumi_sppt, s.luas_bng_sppt,  {$field} nm_tp
                        
            from pembayaran_sppt ps 
            inner join sppt s on ps.kd_propinsi=s.kd_propinsi and ps.kd_dati2=s.kd_dati2 and ps.kd_kecamatan=s.kd_kecamatan and ps.kd_kelurahan=s.kd_kelurahan and ps.kd_blok=s.kd_blok and ps.no_urut=s.no_urut and ps.kd_jns_op=s.kd_jns_op and ps.thn_pajak_sppt=s.thn_pajak_sppt
            inner join ref_kecamatan kec on s.kd_propinsi=kec.kd_propinsi and s.kd_dati2=kec.kd_dati2 and s.kd_kecamatan=kec.kd_kecamatan 
            inner join ref_kelurahan kel on s.kd_propinsi=kel.kd_propinsi and s.kd_dati2=kel.kd_dati2 and s.kd_kecamatan=kel.kd_kecamatan and s.kd_kelurahan=kel.kd_kelurahan 
			left join tempat_pembayaran tp on {$join}
                 
            where ps.tgl_pembayaran_sppt between '{$tgl1}' and '{$tgl2}'
            
			order by tgl_pembayaran_sppt, ps.pembayaran_sppt_ke desc";
            
        $query = $this->db->query($sql);;

        $result['sql']      = $sql;
        $result['query']    = $query->result_array();
        $result['num_rows'] = $query->num_rows();
        $result['tot_rows'] = $query->num_rows();
        
        return $result;
    }
}

/* End of file _model.php */