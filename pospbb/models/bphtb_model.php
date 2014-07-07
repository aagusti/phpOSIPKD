<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class bphtb_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}
	
    function get_daftar_sspd()
	{   
    
        $sql = "select sspd.*,
            -- po.nama as nm_perolehan,  
            ppat.kode as kd_ppat, ppat.nama as nm_ppat, ppat.alamat as alamat_ppat, ppat.kelurahan, ppat.kecamatan, 
            ppat.kota, ppat.wilayah_kerja, ppat.kd_wilayah, ppat.npwp,

            cast(sspd.kd_propinsi || '.' || sspd.kd_dati2 || '.' || sspd.kd_kecamatan || '.' || 
            sspd.kd_kelurahan || '.' || sspd.kd_blok || '-' || sspd.no_urut || '.' || sspd.kd_jns_op as varchar) as nomor_op,
            --prop.nm_propinsi as op_propinsi, dati2.nm_dati2 as op_dati2, kec.nm_kecamatan as op_kecamatan, 
            --kel.nm_kelurahan as op_kelurahan, dasar.nama as nm_dasar, null as berkas_masuk,
            --validasi.id as bpn, validasi.bpn_tgl_terima, validasi.bpn_tgl_selesai,
            --sppt.nm_wp_sppt, 
            --      sppt.jln_wp_sppt, sppt.blok_kav_no_wp_sppt, 
            --      sppt.rw_wp_sppt, sppt.rt_wp_sppt, 
            --      sppt.kelurahan_wp_sppt, sppt.kota_wp_sppt, 
            --      sppt.kd_pos_wp_sppt, sppt.npwp_sppt,
            --sspd_a.id as sspd_approval_id, sspd_a.tgl_approval as sspd_tgl_approval, 
            --sspd_a.tahun as approval_tahun, sspd_a.kode as approval_kode, sspd_a.no_urut as approval_no_urut,
            0 as x
            from 
            bphtb_sspd sspd inner join bphtb_perolehan po on sspd.perolehan_id = po.id
            inner join bphtb_ppat ppat on sspd.ppat_id = ppat.id 
            --left join sppt on sspd.kd_propinsi=sppt.kd_propinsi and 
            --      sspd.kd_dati2=sppt.kd_dati2 and sspd.kd_kecamatan=sppt.kd_kecamatan and 
            --      sspd.kd_kelurahan=sppt.kd_kelurahan and sspd.kd_blok=sppt.kd_blok and
            --      sspd.no_urut=sppt.no_urut and sspd.kd_jns_op=sppt.kd_jns_op and
            --      sspd.thn_pajak_sppt = sppt.thn_pajak_sppt
            --left join ref_propinsi prop on sspd.kd_propinsi = prop.kd_propinsi
            --left join ref_dati2 dati2 on sspd.kd_propinsi = dati2.kd_propinsi and sspd.kd_dati2 = dati2.kd_dati2
            --left join ref_kecamatan kec on sspd.kd_propinsi = kec.kd_propinsi and sspd.kd_dati2 = kec.kd_dati2 and sspd.kd_kecamatan = kec.kd_kecamatan
            --left join ref_kelurahan kel on sspd.kd_propinsi = kel.kd_propinsi and sspd.kd_dati2 = kel.kd_dati2 and sspd.kd_kecamatan = kel.kd_kecamatan and sspd.kd_kelurahan = kel.kd_kelurahan
            --left join bphtb_dasar dasar on sspd.dasar_id = dasar.id

            --left join bphtb_validasi validasi on validasi.sspd_id=sspd.id 
            --left join bphtb_sspd_approval sspd_a on sspd_a.sspd_id = sspd.id 
            
            where sspd.kode='1'

            order by sspd.tahun desc, sspd.kode desc, sspd.no_sspd desc";
                
		$query = $this->db->query($sql);
		if($query->num_rows()!==0)
			return $query->result();
		else
			return FALSE;
	}
    
        
        
        
        
        
        
    
	//-- admin
	function save($data) {
        $this->db->trans_start();
		$this->db->insert($this->tbl,$data);
        $this->db->trans_complete();
            
        if($this->db->trans_status())
            return $this->db->insert_id();
        else
            return false;
	}
	
	function update($id, $data) {
        $this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update($this->tbl,$data);
        $this->db->trans_complete();
            
        if($this->db->trans_status())
            return true;
        else
            return false;
	}
	
	function delete($id) {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->delete($this->tbl);
        $this->db->trans_complete();
            
        if($this->db->trans_status())
            return true;
        else
            return false;
	}
}

/* End of file _model.php */