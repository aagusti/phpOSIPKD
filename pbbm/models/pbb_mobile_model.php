<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pbb_mobile_model extends CI_Model {
    
    
    function informasi_objek_pajak($n, $t){
        $lanjut = true;
        $kdprop=''; $kddati=''; $kdkec=''; $kdkel=''; $kdblok=''; $nourut=''; $jns='';
        
        if (intval($t) <= 0) { $lanjut = false; }
        
        $nop_cnt = strlen($n);
        if ($n == '' || ($nop_cnt != 24 && $nop_cnt != 18) ) {
            $lanjut = false;
        } else {
            //cleansing
            // $s = str_replace(array("-", "'"), array(".", "''"), $n);
            // $s = str_replace(".", "", $s);
            $s = preg_replace("/[^0-9]/","",$n);
            
            $nop_dot = preg_replace("/([0-9]{2})([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{1})/", "$1.$2.$3.$4.$5.$6.$7", $s);
        
            $kode = explode(".", $nop_dot);
            if (count($kode) >= 7 ) {
                list($kdprop, $kddati, $kdkec, $kdkel, $kdblok, $nourut, $jns) = $kode;
                $lanjut = ($kdprop!='') && ($kddati!='') && ($kdkec!='') && ($kdkel!='') && ($kdblok!='') && ($nourut!='') && ($jns!='');
            } else {
                $lanjut = false;
            }
        }
        if ($lanjut) {
            $sql = "SELECT 
                  dop.kd_propinsi||'.'||dop.kd_dati2||'-'||dop.kd_kecamatan||'.'||dop.kd_kelurahan ||'-'|| dop.kd_blok ||'.'||dop.no_urut||'.'|| dop.kd_jns_op as nop,
                  dop.jalan_op || ', ' || dop.blok_kav_no_op as alamat_op, 
                  dop.rt_op || ' / ' || dop.rw_op as rt_rw_op, 
                  kec.nm_kecamatan, kel.nm_kelurahan,
                  dop.total_luas_bumi, dop.total_luas_bng,
                  s.thn_pajak_sppt, coalesce(s.pbb_yg_harus_dibayar_sppt, 0) as tagihan,
                  coalesce(ps.jml_sppt_yg_dibayar, 0) as bayar, ps.tgl_pembayaran_sppt as tgl_bayar
                  
                  
                FROM dat_objek_pajak dop
                LEFT JOIN sppt s
                  ON dop.kd_propinsi = s.kd_propinsi AND dop.kd_dati2 = s.kd_dati2
                    AND dop.kd_kecamatan = s.kd_kecamatan
                    AND dop.kd_kelurahan = s.kd_kelurahan
                    AND dop.kd_blok = s.kd_blok
                    AND dop.no_urut = s.no_urut
                    AND dop.kd_jns_op = s.kd_jns_op
                    AND trim(s.thn_pajak_sppt) = '" . trim($t) . "' 
                LEFT JOIN pembayaran_sppt ps
                  ON ps.kd_propinsi = s.kd_propinsi AND ps.kd_dati2 = s.kd_dati2
                  AND ps.kd_kecamatan = s.kd_kecamatan AND ps.kd_kelurahan = s.kd_kelurahan
                  AND ps.kd_blok = s.kd_blok AND ps.no_urut = s.no_urut
                  AND ps.kd_jns_op = s.kd_jns_op AND ps.thn_pajak_sppt = s.thn_pajak_sppt
                LEFT JOIN ref_kecamatan kec 
                  ON kec.kd_propinsi = dop.kd_propinsi
                  AND kec.kd_dati2 = dop.kd_dati2
                  AND kec.kd_kecamatan = dop.kd_kecamatan
                LEFT JOIN ref_kelurahan kel 
                  ON kel.kd_propinsi = dop.kd_propinsi
                  AND kel.kd_dati2 = dop.kd_dati2
                  AND kel.kd_kecamatan = dop.kd_kecamatan
                  AND kel.kd_kelurahan = dop.kd_kelurahan
                
            WHERE dop.kd_propinsi = '" . $kdprop. "' 
                  AND dop.kd_dati2 = '" . $kddati . "' 
                  AND dop.kd_kecamatan = '" . $kdkec . "' 
                  AND dop.kd_kelurahan = '" . $kdkel . "' 
                  AND dop.kd_blok = '" . $kdblok . "' 
                  AND dop.no_urut = '" . $nourut . "' 
                  AND dop.kd_jns_op = '" . $jns . "' 
            
            ORDER BY ps.tgl_pembayaran_sppt";
            
            /*
            WHERE dop.kd_propinsi = '" . KD_PROPINSI."' 
                  AND dop.kd_dati2 = '" . KD_DATI2."' 
                  */
                
            $query = $this->db->query($sql);
            if($query->num_rows() > 0) {
              return $query->result_array();
            } else {
              return NULL;
            }
        } else {
            return NULL;
        }
    }  
}
/* End of file _model.php */
?>
