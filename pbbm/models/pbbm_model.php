<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class pbbm_model extends CI_Model
{
    private $tbluser = 'user_pbbms';
    public $rangebuku;
    function __construct()
    {
        parent::__construct();
        $this->rangebuku = array(
            1 => array(
                0 => 0,
                1 => 100000
            ),
            2 => array(
                0 => 100001,
                1 => 500000
            ),
            3 => array(
                0 => 500001,
                1 => 2000000
            ),
            4 => array(
                0 => 2000001,
                1 => 5000000
            ),
            5 => array(
                0 => 5000001,
                1 => 9999999999
            )
        );
    }
    function getKodeBlok()
    {
        return $this->kode_blok;
    }
    function setKodeBlok($kode_blok)
    {
        $this->kode_blok = $kode_blok;
    }
    function getKodePropinsi()
    {
        return $this->kode_propinsi;
    }
    function setKodePropinsi($kode_propinsi)
    {
        $this->kode_propinsi = $kode_propinsi;
    }
    function getKodeDati2()
    {
        return $this->kode_dati2;
    }
    function setKodeDati2($kode_dati2)
    {
        $this->kode_dati2 = $kode_dati2;
    }
    function getKodeJenisOP()
    {
        return $this->kode_jenis_op;
    }
    function setKodeJenisOP($kode_jenis_op)
    {
        $this->kode_jenis_op = $kode_jenis_op;
    }
    function getKodeKecamatan()
    {
        return $this->kode_kecamatan;
    }
    function setKodeKecamatan($kode_kecamatan)
    {
        $this->kode_kecamatan = $kode_kecamatan;
    }
    function getKodeKelurahan()
    {
        return $this->kode_kelurahan;
    }
    function setKodeKelurahan($kode_kelurahan)
    {
        $this->kode_kelurahan = $kode_kelurahan;
    }
    function getNOP()
    {
        return $this->nop;
    }
    function setNOP($nop)
    {
        $this->nop = $nop;
    }
    function getNoUrut()
    {
        return $this->no_urut;
    }
    function setNoUrut($no_urut)
    {
        $this->no_urut = $no_urut;
    }
    function getNamaWP()
    {
        return $this->nama_wp;
    }
    function setNamaWP($nama_wp)
    {
        $this->nama_wp = $nama_wp;
    }
    function getTahun()
    {
        return $this->tahun;
    }
    function setTahun($tahun)
    {
        $this->tahun = $tahun;
    }
    
    
    function set_userarea()
    {
        $id = $this->session->userdata('userid');
        
        $this->db->where('user_id', $id);
        $query = $this->db->get($this->tbluser);
        
        if ($row = $query->row())
            $userarea = KD_PROPINSI . KD_DATI2 . $row->kd_kecamatan . $row->kd_kelurahan;
        else
            $userarea = KD_PROPINSI . KD_DATI2 . '000000';
        
        $this->session->set_userdata('user_area', $userarea);
        
        return $userarea;
    }
    
    function informasi_objek_pajak($n)
    {
        $sql = "SELECT 
          s.kd_propinsi||'.'||s.kd_dati2||'-'||s.kd_kecamatan||'.'||s.kd_kelurahan ||'-'|| s.kd_blok ||'.'||s.no_urut||'.'|| s.kd_jns_op nop,
          dop.jalan_op || ', ' || dop.blok_kav_no_op alamat_op,
          dop.rt_op || ' / ' || dop.rw_op rt_rw_op, kel_op.nm_kelurahan kelurahan_op, kec_op.nm_kecamatan kecamatan_op,
          s.luas_bumi_sppt,
          s.luas_bng_sppt,
          
          s.nm_wp_sppt,
          
          --dsp.jalan_wp || ', ' || dsp.blok_kav_no_wp alamat_wp,
          --dsp.rt_wp || ' / ' || dsp.rw_wp rt_rw_wp,
          --dsp.kelurahan_wp, dsp.kota_wp,
          s.jln_wp_sppt|| ', ' || s.blok_kav_no_wp_sppt alamat_wp,
          s.rt_wp_sppt || ' / ' || s.rw_wp_sppt rt_rw_wp,
          s.kelurahan_wp_sppt kelurahan_wp, s.kota_wp_sppt kota_wp,
          
          s.thn_pajak_sppt,
          s.luas_bumi_sppt luas_tanah, 
          s.njop_bumi_sppt njop_tanah, 
          s.luas_bng_sppt luas_bng, 
          s.njop_bng_sppt njop_bng,
          s.pbb_yg_harus_dibayar_sppt ketetapan,
          s.status_pembayaran_sppt status_bayar,
          sum(ps.jml_sppt_yg_dibayar-ps.denda_sppt) jml_bayar,
          to_char(max(ps.tgl_pembayaran_sppt),'dd-mm-yyyy') tgl_bayar
          
        FROM sppt s
		LEFT JOIN dat_objek_pajak dop
		  ON dop.kd_propinsi = s.kd_propinsi 
			AND dop.kd_dati2 = s.kd_dati2
			AND dop.kd_kecamatan = s.kd_kecamatan
			AND dop.kd_kelurahan = s.kd_kelurahan
			AND dop.kd_blok = s.kd_blok
			AND dop.no_urut = s.no_urut
			AND dop.kd_jns_op = s.kd_jns_op
        --LEFT JOIN dat_subjek_pajak dsp ON dsp.subjek_pajak_id = dop.subjek_pajak_id  --ngambil dari sppt aja kata pa agus 20140120
        LEFT JOIN pembayaran_sppt ps
          ON ps.kd_propinsi = s.kd_propinsi 
          AND ps.kd_dati2 = s.kd_dati2
          AND ps.kd_kecamatan = s.kd_kecamatan
          AND ps.kd_kelurahan = s.kd_kelurahan
          AND ps.kd_blok = s.kd_blok
          AND ps.no_urut = s.no_urut
          AND ps.kd_jns_op = s.kd_jns_op
          AND ps.thn_pajak_sppt = s.thn_pajak_sppt
		LEFT JOIN ref_kelurahan kel_op
			ON kel_op.kd_kecamatan = s.kd_kecamatan
			AND kel_op.kd_kelurahan = s.kd_kelurahan
		LEFT JOIN ref_kecamatan kec_op
			ON kec_op.kd_kecamatan = s.kd_kecamatan
          
        WHERE cast(s.thn_pajak_sppt as int) BETWEEN (" . $this->getTahun() . "-9) AND " . $this->getTahun();
        
        if ($n and $n != '') {
            $sql .= " AND dsp.nm_wp ilike '$n%'";
        } else {
            $sql .= " AND s.kd_propinsi = '" . KD_PROPINSI . "'
              AND s.kd_dati2 = '" . KD_DATI2 . "'
              AND s.kd_kecamatan = '" . $this->getKodeKecamatan() . "'
              AND s.kd_kelurahan = '" . $this->getKodeKelurahan() . "'
              AND s.kd_blok = '" . $this->getKodeBlok() . "'
              AND s.no_urut = '" . $this->getNoUrut() . "'
              AND s.kd_jns_op = '" . $this->getKodeJenisOP() . "'
              ";
        }
        $sql .= " GROUP BY s.kd_propinsi, s.kd_dati2, s.kd_kecamatan, s.kd_kelurahan, 
                        s.kd_blok, s.no_urut, s.kd_jns_op, dop.jalan_op, dop.blok_kav_no_op,
                        dop.rt_op, dop.rw_op,	dop.total_luas_bumi, dop.total_luas_bng, s.nm_wp_sppt,
                        --dsp.jalan_wp, dsp.blok_kav_no_wp,	dsp.rt_wp, dsp.rw_wp,	dsp.kelurahan_wp, dsp.kota_wp, 
                        s.jln_wp_sppt, s.blok_kav_no_wp_sppt,	s.rt_wp_sppt, s.rw_wp_sppt, s.kelurahan_wp_sppt, s.kota_wp_sppt, 
                        s.thn_pajak_sppt, s.luas_bumi_sppt, s.njop_bumi_sppt, 
                        s.luas_bng_sppt, s.njop_bng_sppt, s.pbb_yg_harus_dibayar_sppt,
                        s.status_pembayaran_sppt ,
						kel_op.nm_kelurahan, kec_op.nm_kecamatan 
                  ORDER BY s.kd_propinsi, s.kd_dati2, s.kd_kecamatan, s.kd_kelurahan, 
                        s.kd_blok, s.no_urut, s.kd_jns_op, s.thn_pajak_sppt DESC LIMIT 10";
        
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return NULL;
        }
    }
    
    function qry_realisasi_kec($tahun, $tglm, $tgls, $buku)
    {
        $bukumin = $this->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->rangebuku[substr($buku, 1, 1)][1];
        
        $r = "
        SELECT  kode, uraian, sum(sppt1) sppt1, sum(amount1) amount1, sum(sppt2) sppt2, sum(amount2) amount2, 
                sum(sppt3) sppt3, sum(amount3) amount3, sum(sppt4) sppt4, sum(amount4) amount4,
                sum(sppt5) sppt5, sum(amount5) amount5
        FROM (
            SELECT 
              k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan kode, k.nm_kecamatan uraian, 
              count(*) sppt1, sum(s.pbb_yg_harus_dibayar_sppt) amount1, 0 sppt2, 0 amount2,
              0 sppt3, 0 amount3, 0 sppt4, 0 amount4,0 sppt5, 0 amount5
            FROM ref_kecamatan k
            LEFT JOIN sppt s
              ON k.kd_propinsi = s.kd_propinsi
              AND k.kd_dati2 = s.kd_dati2 
              AND k.kd_kecamatan = s.kd_kecamatan
            WHERE k.kd_propinsi='" . KD_PROPINSI . "' 
                  AND k.kd_dati2='" . KD_DATI2 . "' 
                  AND s.thn_pajak_sppt='$tahun'
                  AND s.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax
                  AND s.status_pembayaran_sppt<'2'
            GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.nm_kecamatan

            UNION
            
            SELECT k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan kode, k.nm_kecamatan uraian, 0 sppt1, 0 amount1, 
                count(distinct s.kd_kecamatan||s.kd_kelurahan||s.kd_blok||s.no_urut||s.kd_jns_op) sppt2, sum(p.jml_sppt_yg_dibayar-p.denda_sppt) amount2, 
                0 sppt3, 0 amount3, 
                0 sppt4, 0 amount4,
                0 sppt5, 0 amount5 
            FROM ref_kecamatan k, sppt s, pembayaran_sppt p
                
            WHERE k.kd_propinsi='" . KD_PROPINSI . "' AND k.kd_dati2='" . KD_DATI2 . "'
                  and p.thn_pajak_sppt='$tahun' 
                  AND s.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax
                  AND tgl_pembayaran_sppt < '$tglm' 
                  AND p.kd_propinsi = s.kd_propinsi AND p.kd_dati2 = s.kd_dati2 AND p.kd_kecamatan = s.kd_kecamatan AND p.kd_kelurahan = s.kd_kelurahan 
                  AND p.kd_blok = s.kd_blok 
                  AND p.no_urut = s.no_urut AND p.kd_jns_op = s.kd_jns_op AND p.thn_pajak_sppt = s.thn_pajak_sppt 
                  AND k.kd_propinsi = s.kd_propinsi AND k.kd_dati2 = s.kd_dati2 AND k.kd_kecamatan = s.kd_kecamatan 
            GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.nm_kecamatan 

            UNION
            
            SELECT k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan kode, k.nm_kecamatan uraian, 0 sppt1, 0 amount1, 
                0 sppt3, 0 amount3, 
                count(distinct s.kd_kecamatan||s.kd_kelurahan||s.kd_blok||s.no_urut||s.kd_jns_op) sppt2, sum(p.jml_sppt_yg_dibayar-p.denda_sppt) amount2, 
                0 sppt4, 0 amount4,
                0 sppt5, 0 amount5 
            FROM ref_kecamatan k, sppt s, pembayaran_sppt p
                
            WHERE k.kd_propinsi='" . KD_PROPINSI . "' AND k.kd_dati2='" . KD_DATI2 . "'
                  and p.thn_pajak_sppt='$tahun' 
                  AND s.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax
                  AND tgl_pembayaran_sppt between '$tglm' AND '$tgls' 
                  AND p.kd_propinsi = s.kd_propinsi AND p.kd_dati2 = s.kd_dati2 AND p.kd_kecamatan = s.kd_kecamatan AND p.kd_kelurahan = s.kd_kelurahan 
                  AND p.kd_blok = s.kd_blok 
                  AND p.no_urut = s.no_urut AND p.kd_jns_op = s.kd_jns_op AND p.thn_pajak_sppt = s.thn_pajak_sppt 
                  AND k.kd_propinsi = s.kd_propinsi AND k.kd_dati2 = s.kd_dati2 AND k.kd_kecamatan = s.kd_kecamatan 
            GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.nm_kecamatan 
        ) a	
        GROUP BY kode, uraian
        ORDER BY kode";
        //die($r);
        return $r;
    }
    
    function qry_realisasi_kel($tahun, $tglm, $tgls, $kec_kd, $buku)
    {
        $bukumin = $this->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->rangebuku[substr($buku, 1, 1)][1];
        return "
        SELECT  kode, uraian, sum(sppt1) sppt1, sum(amount1) amount1, sum(sppt2) sppt2, sum(amount2) amount2, 
                sum(sppt3) sppt3, sum(amount3) amount3, sum(sppt4) sppt4, sum(amount4) amount4,
                sum(sppt5) sppt5, sum(amount5) amount5
        FROM (
            SELECT 
              k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan||'.'||k.kd_kelurahan  kode, k.nm_kelurahan uraian, 
              count(*) sppt1, sum(s.pbb_yg_harus_dibayar_sppt) amount1, 0 sppt2, 0 amount2,
              0 sppt3, 0 amount3, 0 sppt4, 0 amount4,0 sppt5, 0 amount5
            FROM ref_kelurahan k
            LEFT JOIN sppt s
              ON k.kd_propinsi = s.kd_propinsi
              AND k.kd_dati2 = s.kd_dati2 
              AND k.kd_kecamatan = s.kd_kecamatan
              AND k.kd_kelurahan = s.kd_kelurahan
            WHERE k.kd_propinsi='" . KD_PROPINSI . "' 
                  AND k.kd_dati2='" . KD_DATI2 . "' 
                  AND k.kd_kecamatan='$kec_kd' 
                  AND s.thn_pajak_sppt='$tahun'
                  AND s.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax
                  AND s.status_pembayaran_sppt<'2'
            GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.kd_kelurahan, k.nm_kelurahan

            UNION
            
            SELECT k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan||'.'||k.kd_kelurahan kode, k.nm_kelurahan uraian, 0 sppt1, 0 amount1, 
                count(distinct s.kd_kecamatan||s.kd_kelurahan||s.kd_blok||s.no_urut||s.kd_jns_op) sppt2, sum(p.jml_sppt_yg_dibayar-p.denda_sppt) amount2, 
                0 sppt3, 0 amount3, 
                0 sppt4, 0 amount4,
                0 sppt5, 0 amount5 
            FROM ref_kelurahan k, sppt s, pembayaran_sppt p
            WHERE k.kd_propinsi='" . KD_PROPINSI . "' AND k.kd_dati2='" . KD_DATI2 . "'AND k.kd_kecamatan='$kec_kd'  
                  and p.thn_pajak_sppt='$tahun' 
                  AND s.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax
                  AND tgl_pembayaran_sppt < '$tglm' 
                  AND p.kd_propinsi = s.kd_propinsi AND p.kd_dati2 = s.kd_dati2 AND p.kd_kecamatan = s.kd_kecamatan AND p.kd_kelurahan = s.kd_kelurahan 
                  AND p.kd_blok = s.kd_blok 
                  AND p.no_urut = s.no_urut AND p.kd_jns_op = s.kd_jns_op AND p.thn_pajak_sppt = s.thn_pajak_sppt 
                  AND k.kd_propinsi = s.kd_propinsi AND k.kd_dati2 = s.kd_dati2 
                      AND k.kd_kecamatan = s.kd_kecamatan AND k.kd_kelurahan = s.kd_kelurahan 
            GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.kd_kelurahan, k.nm_kelurahan 

            UNION
            
            
            SELECT k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan||'.'||k.kd_kelurahan kode, k.nm_kelurahan uraian, 0 sppt1, 0 amount1, 
                0 sppt2, 0 amount2, 
                count(distinct s.kd_kecamatan||s.kd_kelurahan||s.kd_blok||s.no_urut||s.kd_jns_op) sppt3, sum(p.jml_sppt_yg_dibayar-p.denda_sppt) amount3, 
                0 sppt4, 0 amount4,
                0 sppt5, 0 amount5 
            FROM ref_kelurahan k, sppt s, pembayaran_sppt p
            WHERE k.kd_propinsi='" . KD_PROPINSI . "' AND k.kd_dati2='" . KD_DATI2 . "'AND k.kd_kecamatan='$kec_kd'  
                  and p.thn_pajak_sppt='$tahun' 
                  AND s.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax
                  AND p.tgl_pembayaran_sppt between '$tglm' AND '$tgls' 
                  AND p.kd_propinsi = s.kd_propinsi AND p.kd_dati2 = s.kd_dati2 AND p.kd_kecamatan = s.kd_kecamatan AND p.kd_kelurahan = s.kd_kelurahan 
                  AND p.kd_blok = s.kd_blok 
                  AND p.no_urut = s.no_urut AND p.kd_jns_op = s.kd_jns_op AND p.thn_pajak_sppt = s.thn_pajak_sppt 
                  AND k.kd_propinsi = s.kd_propinsi AND k.kd_dati2 = s.kd_dati2 
                      AND k.kd_kecamatan = s.kd_kecamatan AND k.kd_kelurahan = s.kd_kelurahan 
            GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.kd_kelurahan, k.nm_kelurahan 
       
         ) a	
      GROUP BY kode, uraian
      ORDER BY kode";
    }
    
    
    function qry_realisasi_op($tahun, $tglm, $tgls, $kec_kd, $kel_kd, $buku)
    {
        $bukumin = $this->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->rangebuku[substr($buku, 1, 1)][1];
        $sql     = "
	 SELECT  kode, uraian, sum(sppt1) sppt1, sum(amount1) amount1, sum(sppt2) sppt2, sum(amount2) amount2, 
                sum(sppt3) sppt3, sum(amount3) amount3, sum(sppt4) sppt4, sum(amount4) amount4,
                sum(sppt5) sppt5, sum(amount5) amount5
        FROM (
            SELECT 
		a.kd_propinsi||'.'||a.kd_dati2||'-'||a.kd_kecamatan||'.'||a.kd_kelurahan ||'-'|| a.kd_blok ||'.'||a.no_urut||'.'|| a.kd_jns_op kode, 
		a.nm_wp_sppt uraian, 
              1 sppt1, a.pbb_yg_harus_dibayar_sppt amount1, 0 sppt2, 0 amount2,
              0 sppt3, 0 amount3, 0 sppt4, 0 amount4,0 sppt5, 0 amount5
            FROM sppt a
            WHERE a.thn_pajak_sppt='$tahun'
                  AND a.kd_propinsi='" . KD_PROPINSI . "' 
                  AND a.kd_dati2='" . KD_DATI2 . "' 
                  AND a.kd_kecamatan='$kec_kd' 
                  AND a.kd_kelurahan='$kel_kd'
                  AND a.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax
                  AND a.status_pembayaran_sppt<'2'
                  AND (1=1) 
            UNION 
            SELECT 
		a.kd_propinsi||'.'||a.kd_dati2||'-'||a.kd_kecamatan||'.'||a.kd_kelurahan ||'-'|| a.kd_blok ||'.'||a.no_urut||'.'|| a.kd_jns_op kode, 
		a.nm_wp_sppt uraian, 
              0 sppt1, 0 amount1, 1 sppt2, sum(b.jml_sppt_yg_dibayar-b.denda_sppt) amount2,
              0 sppt3, 0 amount3, 0 sppt4, 0 amount4,0 sppt5, 0 amount5
            FROM sppt a
                 INNER JOIN pembayaran_sppt b
                    ON  a.kd_propinsi = b.kd_propinsi 
                    AND a.kd_dati2 = b.kd_dati2 
                    AND a.kd_kecamatan = b.kd_kecamatan 
                    AND a.kd_kelurahan = b.kd_kelurahan
                    AND a.kd_blok = b.kd_blok 
                    AND a.no_urut = b.no_urut 
                    AND a.kd_jns_op = b.kd_jns_op 
                    AND a.thn_pajak_sppt = b.thn_pajak_sppt 
            WHERE a.thn_pajak_sppt='$tahun'
                  AND a.kd_propinsi='" . KD_PROPINSI . "' 
                  AND a.kd_dati2='" . KD_DATI2 . "' 
                  AND a.kd_kecamatan='$kec_kd' 
                  AND a.kd_kelurahan='$kel_kd'
                  AND a.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax
                  AND a.status_pembayaran_sppt<'2'
                  AND b.tgl_pembayaran_sppt >='$tahun-01-01' AND b.tgl_pembayaran_sppt < '$tglm' 
                  AND (1=1)
            GROUP BY a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok, 
                     a.no_urut, a.kd_jns_op, a.nm_wp_sppt
            
            UNION 
            SELECT 
		a.kd_propinsi||'.'||a.kd_dati2||'-'||a.kd_kecamatan||'.'||a.kd_kelurahan ||'-'|| a.kd_blok ||'.'||a.no_urut||'.'|| a.kd_jns_op kode, 
		a.nm_wp_sppt uraian, 
              0 sppt1, 0 amount1, 0 sppt2, 0 amount2, 1 sppt3, 
              sum(b.jml_sppt_yg_dibayar-b.denda_sppt) amount3, 0 sppt4, 0 amount4,0 sppt5, 0 amount5
            FROM  sppt a
                 INNER JOIN pembayaran_sppt b
                    ON a.kd_propinsi = b.kd_propinsi 
                    AND a.kd_dati2 = b.kd_dati2 
                    AND a.kd_kecamatan = b.kd_kecamatan 
                    AND a.kd_kelurahan = b.kd_kelurahan
                    AND a.kd_blok = b.kd_blok 
                    AND a.no_urut = b.no_urut 
                    AND a.kd_jns_op = b.kd_jns_op 
                    AND a.status_pembayaran_sppt<'2'
                  AND a.thn_pajak_sppt = b.thn_pajak_sppt 
            WHERE a.kd_propinsi='" . KD_PROPINSI . "' 
                  AND a.kd_dati2='" . KD_DATI2 . "' 
                  AND a.kd_kecamatan='$kec_kd' 
                  AND a.kd_kelurahan='$kel_kd'
                  AND a.thn_pajak_sppt='$tahun'
                  AND a.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax
                  AND b.tgl_pembayaran_sppt BETWEEN '$tglm' AND '$tgls' 
                  AND (1=1)                  
            GROUP BY a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok, 
                     a.no_urut, a.kd_jns_op, a.nm_wp_sppt";
        $sql .= ") a	
        GROUP BY kode, uraian
        ORDER BY kode";
        //die($sql);
        return $sql;
    }
    
    function qry_piutang_kec($tahun, $tahun2, $buku)
    {
        $bukumin = $this->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->rangebuku[substr($buku, 1, 1)][1];
        
        $sql = "SELECT  kode, uraian, count(*) transaksi, sum(amount) amount
            FROM (
              SELECT 
                s.kd_propinsi||'.'||s.kd_dati2||'-'||s.kd_kecamatan kode, k.nm_kecamatan uraian, 
                s.pbb_yg_harus_dibayar_sppt-sum(coalesce(p.jml_sppt_yg_dibayar,0)-coalesce(p.denda_sppt,0)) amount
              FROM ref_kecamatan k
              INNER JOIN sppt s
                ON k.kd_propinsi = s.kd_propinsi
                AND k.kd_dati2 = s.kd_dati2 
                AND k.kd_kecamatan = s.kd_kecamatan
              LEFT JOIN pembayaran_sppt p
                ON  s.kd_propinsi = p.kd_propinsi
                AND s.kd_dati2 = p.kd_dati2 
                AND s.kd_kecamatan = p.kd_kecamatan
                AND s.kd_kelurahan = p.kd_kelurahan 
                AND s.kd_blok = p.kd_blok
                AND s.no_urut=p.no_urut 
                AND s.kd_jns_op = p.kd_jns_op 
                AND s.thn_pajak_sppt = p.thn_pajak_sppt
              WHERE s.kd_propinsi='" . KD_PROPINSI . "' 
                  AND s.kd_dati2='" . KD_DATI2 . "' 
                  AND s.thn_pajak_sppt BETWEEN '$tahun' AND '$tahun2' 
                  AND s.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax
                  AND s.status_pembayaran_sppt<>'2'
              GROUP BY s.kd_propinsi, s.kd_dati2, s.kd_kecamatan, s.kd_kelurahan, 
                       s.kd_blok, s.no_urut, s.kd_jns_op, s.thn_pajak_sppt, k.nm_kecamatan, s.pbb_yg_harus_dibayar_sppt
              HAVING s.pbb_yg_harus_dibayar_sppt > sum(coalesce(p.jml_sppt_yg_dibayar,0)-coalesce(p.denda_sppt,0))   
            ) a	
            GROUP BY kode, uraian 
            ORDER BY kode";
        return $sql;
    }
    
    function qry_piutang_kel($tahun, $tahun2, $buku, $kec_kd)
    {
        $bukumin = $this->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->rangebuku[substr($buku, 1, 1)][1];
        
        $sql = "SELECT  kode, uraian, count(*) transaksi, sum(amount) amount
            FROM (
              SELECT 
                k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan||'.'||k.kd_kelurahan kode, k.nm_kelurahan uraian, 
                1 transaksi, s.pbb_yg_harus_dibayar_sppt-sum(coalesce(p.jml_sppt_yg_dibayar,0)-coalesce(p.denda_sppt,0)) amount
              FROM ref_kelurahan k
              INNER JOIN sppt s
                ON k.kd_propinsi = s.kd_propinsi
                AND k.kd_dati2 = s.kd_dati2 
                AND k.kd_kecamatan = s.kd_kecamatan
                AND k.kd_kelurahan = s.kd_kelurahan
              LEFT JOIN pembayaran_sppt p
                ON  s.kd_propinsi = p.kd_propinsi
                AND s.kd_dati2 = p.kd_dati2 
                AND s.kd_kecamatan = p.kd_kecamatan
                AND s.kd_kelurahan = p.kd_kelurahan 
                AND s.kd_blok = p.kd_blok
                AND s.no_urut=p.no_urut 
                AND s.kd_jns_op = p.kd_jns_op 
                AND s.thn_pajak_sppt = p.thn_pajak_sppt
              WHERE k.kd_propinsi='" . KD_PROPINSI . "' 
                  AND k.kd_dati2='" . KD_DATI2 . "' 
                  AND k.kd_kecamatan='$kec_kd' 
                  AND s.thn_pajak_sppt  BETWEEN '$tahun' AND '$tahun2' 
                  AND s.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax
                  AND s.status_pembayaran_sppt<>'2'
              GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.kd_kelurahan, k.nm_kelurahan,
                       s.kd_blok, s.no_urut, s.kd_jns_op, s.thn_pajak_sppt, s.pbb_yg_harus_dibayar_sppt
              HAVING s.pbb_yg_harus_dibayar_sppt > sum(coalesce(p.jml_sppt_yg_dibayar,0)-coalesce(p.denda_sppt,0))  
            ) a	
            GROUP BY kode, uraian 
            ORDER BY kode";
        return $sql;
    }
    
    function qry_piutang_op($tahun, $tahun2, $buku, $kec_kd, $kel_kd)
    {
        $bukumin = $this->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->rangebuku[substr($buku, 1, 1)][1];
        $sql     = "
				SELECT  kode, uraian, count(*) transaksi, sum(amount) amount
        FROM (
            SELECT 
							k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan||'.'||k.kd_kelurahan 
              ||'-'|| k.kd_blok ||'.'||k.no_urut||'.'|| k.kd_jns_op||' '||k.thn_pajak_sppt kode, 
							k.nm_wp_sppt uraian, 1 transaksi,
              k.pbb_yg_harus_dibayar_sppt-sum(coalesce(p.jml_sppt_yg_dibayar,0)-coalesce(p.denda_sppt,0)) amount
            FROM sppt k
                 LEFT JOIN pembayaran_sppt p
                    ON k.kd_propinsi = p.kd_propinsi 
                    AND k.kd_dati2 = p.kd_dati2 
                    AND k.kd_kecamatan = p.kd_kecamatan 
                    AND k.kd_kelurahan = p.kd_kelurahan
                    AND k.kd_blok = p.kd_blok 
                    AND k.no_urut = p.no_urut 
                    AND k.kd_jns_op = p.kd_jns_op 
                    AND k.thn_pajak_sppt = p.thn_pajak_sppt 
            WHERE k.thn_pajak_sppt   BETWEEN '$tahun' AND '$tahun2' 
                  AND k.kd_propinsi='" . KD_PROPINSI . "' 
                  AND k.kd_dati2='" . KD_DATI2 . "' 
                  AND k.kd_kecamatan='$kec_kd' 
                  AND k.kd_kelurahan='$kel_kd'
                  AND k.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax
                  AND k.status_pembayaran_sppt<>'2'
            GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.kd_kelurahan, k.kd_blok, k.no_urut, k.kd_jns_op, 
                  k.thn_pajak_sppt, k.nm_wp_sppt, k.pbb_yg_harus_dibayar_sppt
            HAVING k.pbb_yg_harus_dibayar_sppt > sum(coalesce(p.jml_sppt_yg_dibayar,0)-coalesce(p.denda_sppt,0))  
        ) a	
         
        GROUP BY kode, uraian 
		ORDER BY kode";
        return $sql;
    }
    
    function qry_realisasi_lb_kec($tahun)
    {
        return "
        SELECT  k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan kode, k.nm_kecamatan uraian,
                count(s.*) sppt1, sum(amount1) amount1,  sum(amount2) amount2, sum(amount1) - sum(amount2) amount3 
        FROM ref_kecamatan k
        LEFT JOIN (
                SELECT a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok , a.no_urut, a.kd_jns_op, 
		               a.nm_wp_sppt uraian, 1 sppt1, a.pbb_yg_harus_dibayar_sppt amount1, sum(jml_sppt_yg_dibayar-denda_sppt)  amount2
                FROM sppt a
                INNER JOIN pembayaran_sppt b
                  ON  a.kd_propinsi = b.kd_propinsi 
                    AND a.kd_dati2 = b.kd_dati2 
                    AND a.kd_kecamatan = b.kd_kecamatan 
                    AND a.kd_kelurahan = b.kd_kelurahan
                    AND a.kd_blok = b.kd_blok 
                    AND a.no_urut = b.no_urut 
                    AND a.kd_jns_op = b.kd_jns_op 
                    AND a.thn_pajak_sppt = b.thn_pajak_sppt 
                WHERE a.thn_pajak_sppt='$tahun'
                  AND a.kd_propinsi='" . KD_PROPINSI . "' 
                  AND a.kd_dati2='" . KD_DATI2 . "' 

                GROUP BY a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok, a.no_urut, a.kd_jns_op, a.thn_pajak_sppt
                HAVING a.pbb_yg_harus_dibayar_sppt<sum(jml_sppt_yg_dibayar-denda_sppt)
            ) s
            ON k.kd_propinsi = s.kd_propinsi
              AND k.kd_dati2 = s.kd_dati2 
              AND k.kd_kecamatan = s.kd_kecamatan
            WHERE (1=1) AND (1=1)
            GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.nm_kecamatan
            ORDER BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan ";
    }
    
    function qry_realisasi_lb_kel($tahun, $kec_kd)
    {
        return "
        SELECT  k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan ||'.'|| k.kd_kelurahan kode, k.nm_kelurahan uraian,
                count(s.*) sppt1, sum(amount1) amount1,  sum(amount2) amount2, sum(amount1) - sum(amount2) amount3 
        FROM ref_kelurahan k
        LEFT JOIN (
                SELECT a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok , a.no_urut, a.kd_jns_op, 
		               a.nm_wp_sppt uraian, 1 sppt1, a.pbb_yg_harus_dibayar_sppt amount1, sum(jml_sppt_yg_dibayar-denda_sppt)  amount2
                FROM sppt a
                INNER JOIN pembayaran_sppt b
                  ON  a.kd_propinsi = b.kd_propinsi 
                    AND a.kd_dati2 = b.kd_dati2 
                    AND a.kd_kecamatan = b.kd_kecamatan 
                    AND a.kd_kelurahan = b.kd_kelurahan
                    AND a.kd_blok = b.kd_blok 
                    AND a.no_urut = b.no_urut 
                    AND a.kd_jns_op = b.kd_jns_op 
                    AND a.thn_pajak_sppt = b.thn_pajak_sppt 
                WHERE a.thn_pajak_sppt='$tahun'
                  AND a.kd_propinsi='" . KD_PROPINSI . "' 
                  AND a.kd_dati2='" . KD_DATI2 . "' 
                  AND a.kd_kecamatan='$kec_kd' 

                GROUP BY a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok, a.no_urut, a.kd_jns_op, a.thn_pajak_sppt
                HAVING a.pbb_yg_harus_dibayar_sppt<sum(jml_sppt_yg_dibayar-denda_sppt)
            ) s
            ON k.kd_propinsi = s.kd_propinsi
              AND k.kd_dati2 = s.kd_dati2 
              AND k.kd_kecamatan = s.kd_kecamatan
              AND k.kd_kelurahan = s.kd_kelurahan
              
            WHERE k.kd_propinsi='" . KD_PROPINSI . "' 
                  AND k.kd_dati2='" . KD_DATI2 . "' 
                  AND k.kd_kecamatan='$kec_kd' AND (1=1)
            GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.kd_kelurahan, k.nm_kelurahan 
            ORDER BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.kd_kelurahan";
    }
    
    
    function qry_realisasi_lb_op($tahun, $kec_kd, $kel_kd)
    {
        $sql = "
        SELECT  kode, uraian, sum(sppt1) sppt1, sum(amount1) amount1,  sum(amount2) amount2, sum(amount1) -sum(amount2) amount3
        FROM (
            SELECT a.kd_propinsi||'.'||a.kd_dati2||'-'||a.kd_kecamatan||'.'||a.kd_kelurahan ||'-'|| a.kd_blok ||'.'||a.no_urut||'.'|| a.kd_jns_op kode, 
		               a.nm_wp_sppt uraian, 1 sppt1, a.pbb_yg_harus_dibayar_sppt amount1, sum(jml_sppt_yg_dibayar-denda_sppt)  amount2
            FROM sppt a
            INNER JOIN pembayaran_sppt b
               ON  a.kd_propinsi = b.kd_propinsi 
                    AND a.kd_dati2 = b.kd_dati2 
                    AND a.kd_kecamatan = b.kd_kecamatan 
                    AND a.kd_kelurahan = b.kd_kelurahan
                    AND a.kd_blok = b.kd_blok 
                    AND a.no_urut = b.no_urut 
                    AND a.kd_jns_op = b.kd_jns_op 
                    AND a.thn_pajak_sppt = b.thn_pajak_sppt 
            WHERE a.thn_pajak_sppt='$tahun'
                  AND a.kd_propinsi='" . KD_PROPINSI . "' 
                  AND a.kd_dati2='" . KD_DATI2 . "' 
                  AND a.kd_kecamatan='$kec_kd' 
                  AND a.kd_kelurahan='$kel_kd'
                  AND (1=1)
            GROUP BY a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok, a.no_urut, a.kd_jns_op, a.thn_pajak_sppt
            HAVING a.pbb_yg_harus_dibayar_sppt<sum(jml_sppt_yg_dibayar-denda_sppt)
            ";
        $sql .= ") as c	
        GROUP BY kode, uraian
        ORDER BY kode ";
        return $sql;
    }
    
    function qry_realisasi_kb_kec($tahun)
    {
        return "
        SELECT  k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan kode, k.nm_kecamatan uraian,
                count(s.*) sppt1, sum(amount1) amount1,  sum(amount2) amount2, sum(amount1) - sum(amount2) amount3 
        FROM ref_kecamatan k
        LEFT JOIN (
                SELECT a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok , a.no_urut, a.kd_jns_op, 
		               a.nm_wp_sppt uraian, 1 sppt1, a.pbb_yg_harus_dibayar_sppt amount1, sum(jml_sppt_yg_dibayar-denda_sppt)  amount2
                FROM sppt a
                INNER JOIN pembayaran_sppt b
                  ON  a.kd_propinsi = b.kd_propinsi 
                    AND a.kd_dati2 = b.kd_dati2 
                    AND a.kd_kecamatan = b.kd_kecamatan 
                    AND a.kd_kelurahan = b.kd_kelurahan
                    AND a.kd_blok = b.kd_blok 
                    AND a.no_urut = b.no_urut 
                    AND a.kd_jns_op = b.kd_jns_op 
                    AND a.thn_pajak_sppt = b.thn_pajak_sppt 
                WHERE a.thn_pajak_sppt='$tahun'
                  AND a.kd_propinsi='" . KD_PROPINSI . "' 
                  AND a.kd_dati2='" . KD_DATI2 . "' 

                GROUP BY a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok, a.no_urut, a.kd_jns_op, a.thn_pajak_sppt
                HAVING a.pbb_yg_harus_dibayar_sppt>sum(jml_sppt_yg_dibayar-denda_sppt)
            ) s
            ON k.kd_propinsi = s.kd_propinsi
              AND k.kd_dati2 = s.kd_dati2 
              AND k.kd_kecamatan = s.kd_kecamatan
            WHERE (1=1) AND (1=1)
            GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.nm_kecamatan
            ORDER BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan ";
    }
    
    function qry_realisasi_kb_kel($tahun, $kec_kd)
    {
        return "
        SELECT  k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan ||'.'|| k.kd_kelurahan kode, k.nm_kelurahan uraian,
                count(s.*) sppt1, sum(amount1) amount1,  sum(amount2) amount2, sum(amount1) - sum(amount2) amount3 
        FROM ref_kelurahan k
        LEFT JOIN (
                SELECT a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok , a.no_urut, a.kd_jns_op, 
		               a.nm_wp_sppt uraian, 1 sppt1, a.pbb_yg_harus_dibayar_sppt amount1, sum(jml_sppt_yg_dibayar-denda_sppt)  amount2
                FROM sppt a
                INNER JOIN pembayaran_sppt b
                  ON  a.kd_propinsi = b.kd_propinsi 
                    AND a.kd_dati2 = b.kd_dati2 
                    AND a.kd_kecamatan = b.kd_kecamatan 
                    AND a.kd_kelurahan = b.kd_kelurahan
                    AND a.kd_blok = b.kd_blok 
                    AND a.no_urut = b.no_urut 
                    AND a.kd_jns_op = b.kd_jns_op 
                    AND a.thn_pajak_sppt = b.thn_pajak_sppt 
                WHERE a.thn_pajak_sppt='$tahun'
                  AND a.kd_propinsi='" . KD_PROPINSI . "' 
                  AND a.kd_dati2='" . KD_DATI2 . "' 
                  AND a.kd_kecamatan='$kec_kd' 

                GROUP BY a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok, a.no_urut, a.kd_jns_op, a.thn_pajak_sppt
                HAVING a.pbb_yg_harus_dibayar_sppt>sum(jml_sppt_yg_dibayar-denda_sppt)
            ) s
            ON k.kd_propinsi = s.kd_propinsi
              AND k.kd_dati2 = s.kd_dati2 
              AND k.kd_kecamatan = s.kd_kecamatan
              AND k.kd_kelurahan = s.kd_kelurahan
              
            WHERE k.kd_propinsi='" . KD_PROPINSI . "' 
                  AND k.kd_dati2='" . KD_DATI2 . "' 
                  AND k.kd_kecamatan='$kec_kd' AND (1=1)
            GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.kd_kelurahan, k.nm_kelurahan 
            ORDER BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.kd_kelurahan";
    }
    
    
    function qry_realisasi_kb_op($tahun, $kec_kd, $kel_kd)
    {
        $sql = "
        SELECT  kode, uraian, sum(sppt1) sppt1, sum(amount1) amount1,  sum(amount2) amount2, sum(amount1) -sum(amount2) amount3
        FROM (
            SELECT a.kd_propinsi||'.'||a.kd_dati2||'-'||a.kd_kecamatan||'.'||a.kd_kelurahan ||'-'|| a.kd_blok ||'.'||a.no_urut||'.'|| a.kd_jns_op kode, 
		               a.nm_wp_sppt uraian, 1 sppt1, a.pbb_yg_harus_dibayar_sppt amount1, sum(jml_sppt_yg_dibayar-denda_sppt)  amount2
            FROM sppt a
            INNER JOIN pembayaran_sppt b
               ON  a.kd_propinsi = b.kd_propinsi 
                    AND a.kd_dati2 = b.kd_dati2 
                    AND a.kd_kecamatan = b.kd_kecamatan 
                    AND a.kd_kelurahan = b.kd_kelurahan
                    AND a.kd_blok = b.kd_blok 
                    AND a.no_urut = b.no_urut 
                    AND a.kd_jns_op = b.kd_jns_op 
                    AND a.thn_pajak_sppt = b.thn_pajak_sppt 
            WHERE a.thn_pajak_sppt='$tahun'
                  AND a.kd_propinsi='" . KD_PROPINSI . "' 
                  AND a.kd_dati2='" . KD_DATI2 . "' 
                  AND a.kd_kecamatan='$kec_kd' 
                  AND a.kd_kelurahan='$kel_kd'
                  AND (1=1)
            GROUP BY a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok, a.no_urut, a.kd_jns_op, a.thn_pajak_sppt
            HAVING a.pbb_yg_harus_dibayar_sppt>sum(jml_sppt_yg_dibayar-denda_sppt)
            ";
        $sql .= ") as c	
        GROUP BY kode, uraian
        ORDER BY kode";
        return $sql;
    }
    
    
    function realisasi_dashboard($where = "")
    {
        $thn  = date('Y');
        $prop = KD_PROPINSI;
        $dati = KD_DATI2;
        
        $sql = "select sum(cnt_daily) cnt_daily, sum(amt_daily) amt_daily, sum(cnt_weekly) cnt_weekly, sum(amt_weekly) amt_weekly, 
	    sum(cnt_monthly) cnt_monthly, sum(amt_monthly) amt_monthly, sum(cnt_yearly) cnt_yearly, sum(amt_yearly) amt_yearly,
        sum(pokok) pokok, sum(piutang) piutang, sum(denda) denda, sum(tetap) tetap
        from (
		--harian
		SELECT count(*) cnt_daily, coalesce(sum(p.jml_sppt_yg_dibayar),0) amt_daily, 0 cnt_weekly, 0 amt_weekly, 0 cnt_monthly, 
                0 amt_monthly, 0 cnt_yearly, 0 amt_yearly, 0 pokok, 0 piutang, 0 denda, 0 tetap
		FROM pembayaran_sppt p
		WHERE kd_propinsi='{$prop}' AND kd_dati2='{$dati}' AND extract(year from tgl_pembayaran_sppt)={$thn} 
		AND tgl_pembayaran_sppt >=now()::date AND tgl_pembayaran_sppt <= now()::date+1
        {$where}

		--mingguan
		UNION 
		SELECT 0 cnt_daily, 0 amt_daily, count(*) cnt_weekly, coalesce(sum(p.jml_sppt_yg_dibayar),0) amt_weekly, 0 cnt_monthly, 
                0 amt_monthly, 0 cnt_yearly, 0 amt_yearly, 0 pokok, 0 piutang, 0 denda, 0 tetap
		FROM pembayaran_sppt p
		WHERE kd_propinsi='{$prop}' AND kd_dati2='{$dati}' AND extract(year from tgl_pembayaran_sppt)={$thn} 
		AND extract (week from tgl_pembayaran_sppt) = extract (week from now()::date)
        {$where}

		--bulanan
		UNION 
		SELECT 0 cnt_daily, 0 amt_daily, 0 cnt_weekly, 0 amt_weekly, count(*) cnt_monthly, coalesce(sum(p.jml_sppt_yg_dibayar),0) amt_monthly, 0 cnt_yearly, 0 amt_yearly, 0 pokok, 0 piutang, 0 denda, 0 tetap
		FROM pembayaran_sppt p
		WHERE kd_propinsi='{$prop}' AND kd_dati2='{$dati}' AND extract(year from tgl_pembayaran_sppt)={$thn} 
		AND extract (month from tgl_pembayaran_sppt) = extract (month from now()::date) 
        {$where}

		--tahunan
		UNION 
		SELECT 0 cnt_daily, 0 amt_daily, 0 cnt_weekly, 0 amt_weekly, 0 cnt_monthly, 0 amt_monthly, count(*) cnt_yearly, coalesce(sum(p.jml_sppt_yg_dibayar),0) amt_yearly, 0 pokok, 0 piutang, 0 denda, 0 tetap
		FROM pembayaran_sppt p
		WHERE kd_propinsi='{$prop}' AND kd_dati2='{$dati}' AND extract(year from tgl_pembayaran_sppt)={$thn} 
		AND extract (year from tgl_pembayaran_sppt) = extract (year from now()::date) 
        {$where}
		
        
        --pokok
		UNION 
		SELECT 0 cnt_daily, 0 amt_daily, 0 cnt_weekly, 0 amt_weekly, 0 cnt_monthly, 0 amt_monthly, 0 cnt_yearly,  0 amt_yearly, 
            coalesce(sum(p.jml_sppt_yg_dibayar-p.denda_sppt),0) pokok, 0 piutang, 0 denda, 0 tetap
		FROM pembayaran_sppt p
		WHERE kd_propinsi='{$prop}' AND kd_dati2='{$dati}' AND extract(year from tgl_pembayaran_sppt)={$thn} 
		AND extract (year from tgl_pembayaran_sppt) = extract (year from now()::date)
        AND p.thn_pajak_sppt='{$thn}'
        {$where}
		
        
        --piutang
		UNION 
		SELECT 0 cnt_daily, 0 amt_daily, 0 cnt_weekly, 0 amt_weekly, 0 cnt_monthly, 0 amt_monthly, 0 cnt_yearly,  0 amt_yearly, 
            0 pokok, coalesce(sum(p.jml_sppt_yg_dibayar-p.denda_sppt),0) piutang, 0 denda, 0 tetap
		FROM pembayaran_sppt p
		WHERE kd_propinsi='{$prop}' AND kd_dati2='{$dati}' AND extract(year from tgl_pembayaran_sppt)={$thn} 
		AND extract (year from tgl_pembayaran_sppt) = extract (year from now()::date)
        AND p.thn_pajak_sppt<'{$thn}'
        {$where}
		
        --denda
        UNION 
		SELECT 0 cnt_daily, 0 amt_daily, 0 cnt_weekly, 0 amt_weekly, 0 cnt_monthly, 0 amt_monthly, 0 cnt_yearly,  0 amt_yearly, 
            0 pokok, 0 piutang, coalesce(sum(p.denda_sppt),0) denda, 0 tetap
		FROM pembayaran_sppt p
		WHERE kd_propinsi='{$prop}' AND kd_dati2='{$dati}' AND extract(year from tgl_pembayaran_sppt)={$thn} 
		AND extract (year from tgl_pembayaran_sppt) = extract (year from now()::date)
        {$where}

        --tetap
        UNION 
		SELECT 0 cnt_daily, 0 amt_daily, 0 cnt_weekly, 0 amt_weekly, 0 cnt_monthly, 0 amt_monthly, 0 cnt_yearly,  0 amt_yearly, 
            0 pokok, 0 piutang, 0 denda, coalesce(sum(p.pbb_yg_harus_dibayar_sppt),0) tetap
		FROM sppt p
		WHERE kd_propinsi='{$prop}' AND kd_dati2='{$dati}' AND p.thn_pajak_sppt='{$thn}'
                AND p.status_pembayaran_sppt<'2'
        {$where}
		) as penerimaan";
        
        return $this->db->query($sql)->row();
    }
}
/* End of file _model.php */
