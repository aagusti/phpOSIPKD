<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ipbb extends CI_Controller {
    private $module = 'ipbb';

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $nopthn = $_GET['nopthn'];
        if(!$nopthn) return NULL;
        
        $param1 = explode("-", $nopthn);
        $nop = $param1[0];
        $thn = $param1[1];
        
        $nop_cnt = strlen($nop);
        $thn_cnt = strlen($thn);

        if(!$nop || !$thn || $thn_cnt!=4 || ($nop_cnt!=24 && $nop_cnt!=18)) return NULL;
        
        $kdprop=''; $kddati=''; $kdkec=''; $kdkel=''; $kdblok=''; $nourut=''; $jns='';
        $nop_num = preg_replace("/[^0-9]/","",$nop);
        $nop_dot = preg_replace("/([0-9]{2})([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{1})/", "$1.$2.$3.$4.$5.$6.$7", $nop_num);
    
        $kode = explode(".", $nop_dot);
        list($kdprop, $kddati, $kdkec, $kdkel, $kdblok, $nourut, $jns) = $kode;
            
        $sql = "SELECT 
            dop.kd_propinsi||'.'||dop.kd_dati2||'-'||dop.kd_kecamatan||'.'||dop.kd_kelurahan ||'-'|| dop.kd_blok ||'.'||dop.no_urut||'.'|| dop.kd_jns_op as nop,
            coalesce(dop.jalan_op || ', ' || dop.blok_kav_no_op,'') as alamat_op, 
            dop.rt_op || ' / ' || dop.rw_op as rt_rw_op, 
            coalesce(kec.nm_kecamatan,'') nm_kecamatan, coalesce(kel.nm_kelurahan,'') nm_kelurahan,
            dop.total_luas_bumi, dop.total_luas_bng,
            s.thn_pajak_sppt, coalesce(s.pbb_yg_harus_dibayar_sppt, 0) as tagihan,
            coalesce(ps.jml_sppt_yg_dibayar, 0) as bayar, coalesce(to_char(ps.tgl_pembayaran_sppt,'DD-MM-YYYY'),'') as tgl_bayar


            FROM dat_objek_pajak dop
            LEFT JOIN sppt s
                ON dop.kd_propinsi = s.kd_propinsi AND dop.kd_dati2 = s.kd_dati2
                AND dop.kd_kecamatan = s.kd_kecamatan
                AND dop.kd_kelurahan = s.kd_kelurahan
                AND dop.kd_blok = s.kd_blok
                AND dop.no_urut = s.no_urut
                AND dop.kd_jns_op = s.kd_jns_op
                AND trim(s.thn_pajak_sppt) = '" . trim($thn) . "' 
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
                
        $query = $this->db->query($sql);
        if($query->num_rows() > 0) {
            echo json_encode($query->row_array());
        } else {
            return NULL;
        }
    }
}
