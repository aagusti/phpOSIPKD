<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class trans_rpt extends CI_Controller {
	private $module = 'pbbmt';
	
	function __construct() {
		parent::__construct();

		if(active_module()!='pbbm') { 
			show_404();
			exit;
		}
		$this->load->model(array('apps_model', 'login_model', 'pbbm_model'));
		$this->pbbm_model->set_userarea();
    $this->load->model(array('kecModel','kelModel'));
    
	}
  
  /*
  // fungsi cetak sebelumnya (blm menggunakan jasper)
  function bulan(){
        $buku    = (isset($_GET['buku'])) ? $_GET['buku'] : '11';
        $bukumin = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
        $tahun = (isset($_GET['tahun'])) ? $_GET['tahun'] : date('Y');
        $tahun_sppt1 = (isset($_GET['tahun_sppt1'])) ? $_GET['tahun_sppt1'] : date('Y');
        $tahun_sppt2 = (isset($_GET['tahun_sppt2'])) ? $_GET['tahun_sppt2'] : date('Y');

        $kec_kd = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';

        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();

        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kel_kd = get_user_kel_kd();
        
        $path_to_root = active_module_url();

        $where = "WHERE extract(year FROM p.tgl_pembayaran_sppt)= $tahun 
             AND p.kd_propinsi='" . KD_PROPINSI . "' 
             AND p.kd_dati2='" . KD_DATI2 . "'
             AND k.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax 
             AND p.thn_pajak_sppt between '$tahun_sppt1' AND '$tahun_sppt2' ";
        if ($kec_kd != "000")
            $where .= " AND p.kd_kecamatan='$kec_kd'";
        if ($kel_kd != "000")
            $where .= " AND p.kd_kelurahan='$kel_kd'";
       
        $sql_query_r = "
      SELECT  Extract(month FROM tgl_pembayaran_sppt) kode,
          tp.kd_kanwil||tp.kd_kantor||tp.kd_tp||':'||tp.nm_tp uraian, sum(k.pbb_yg_harus_dibayar_sppt)  pokok, 
          sum(p.denda_sppt) denda, sum(p.jml_sppt_yg_dibayar) bayar
          FROM sppt k 
          INNER JOIN pembayaran_sppt p 
            ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 
          LEFT JOIN tempat_pembayaran tp ON p.kd_kanwil=tp.kd_kanwil and p.kd_kantor=tp.kd_kantor AND p.kd_tp=tp.kd_tp
          $where 
          GROUP BY 1,2 ";
        $sql_query_r .= "ORDER BY kode";
        //die($sql_query_r);
        $qry       = $this->db->query($sql_query_r);
        $data['detail'] = $qry->result();
        for ($i=1; $i<=5; $i++){
          for ($j=$i;$j<=5;$j++){
              $r="";
              for ($k=$i;$k<=$j;$k++) $r.="$k,";
              $r=substr($r,0,strlen($r)-1);
              if ($buku=="$i$j") $data['buku']= "$r";
            }
          }
        if ($kec_kd=='000') $kec_kd='999';  
        $data['kec_nm'] = $this->kecModel->getrecord($kec_kd);
        if ($kel_kd=='000') $kel_kd='999';
        $data['kel_nm'] = $this->kelModel->getrecord($kec_kd,$kel_kd);

        $data['tahun'] = $tahun;
        $data['tahun_sppt1'] = $tahun_sppt1;
        $data['tahun_sppt2'] = $tahun_sppt2;
        
        $this->load->view('rpt_trans_bulan', $data);
  }
  function trans1(){
        $buku    = (isset($_GET['buku'])) ? $_GET['buku'] : '11';
        $bukumin = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
        $tglm = (isset($_GET['tglawal'])) ? $_GET['tglawal'] : date('d-m-Y');
        $tgls = (isset($_GET['tglakhir'])) ? $_GET['tglakhir'] : date('d-m-Y');
        $data['tglawal']=$tglm;
        $data['tglakhir']=$tgls;

        $tglm = substr($tglm, 6, 4) . '-' . substr($tglm, 3, 2) . '-' . substr($tglm, 0, 2);
        $tgls = substr($tgls, 6, 4) . '-' . substr($tgls, 3, 2) . '-' . substr($tgls, 0, 2);

        $tahun_sppt1 = (isset($_GET['tahun_sppt1'])) ? $_GET['tahun_sppt1'] : date('Y');
        $tahun_sppt2 = (isset($_GET['tahun_sppt2'])) ? $_GET['tahun_sppt2'] : date('Y');

        $kec_kd = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';

        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();

        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kel_kd = get_user_kel_kd();
        
        

        $path_to_root = active_module_url();

        $where = "WHERE p.tgl_pembayaran_sppt BETWEEN '$tglm' AND '$tgls'
             AND k.kd_propinsi='" . KD_PROPINSI . "' 
             AND k.kd_dati2='" . KD_DATI2 . "' 
             AND p.thn_pajak_sppt BETWEEN '$tahun_sppt1' AND '$tahun_sppt2'
             AND k.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax ";
        if ($kec_kd != "000")
            $where .= " AND p.kd_kecamatan='$kec_kd'";
        if ($kel_kd != "000")
            $where .= " AND p.kd_kelurahan='$kel_kd'";
       
        $sql_query_r = "
       SELECT  
            k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan||'.'||k.kd_kelurahan ||'-'|| k.kd_blok ||'.'||k.no_urut||'.'|| k.kd_jns_op ||' '|| k.thn_pajak_sppt kode, 
            k.nm_wp_sppt uraian, 
            k.pbb_yg_harus_dibayar_sppt pokok, p.denda_sppt denda, p.jml_sppt_yg_dibayar bayar, p.tgl_pembayaran_sppt tanggal
          FROM sppt k 
          INNER JOIN pembayaran_sppt p 
            ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 
          $where   ";
        $sql_query_r .= "ORDER BY kode";
        //die($sql_query_r);
        $qry       = $this->db->query($sql_query_r);
        $data['detail'] = $qry->result();
        for ($i=1; $i<=5; $i++){
          for ($j=$i;$j<=5;$j++){
              $r="";
              for ($k=$i;$k<=$j;$k++) $r.="$k,";
              $r=substr($r,0,strlen($r)-1);
              if ($buku=="$i$j") $data['buku']= "$r";
            }
          }
        if ($kec_kd=='000') $kec_kd='999';  
        $data['kec_nm'] = $this->kecModel->getrecord($kec_kd);
        if ($kel_kd=='000') $kel_kd='999';
        $data['kel_nm'] = $this->kelModel->getrecord($kec_kd,$kel_kd);
        $data['tahun_sppt1'] = $tahun_sppt1;
        $data['tahun_sppt2'] = $tahun_sppt2;
        
        $this->load->view('rpt_trans_1', $data);

  }    
  
  function trans2(){
        $buku    = (isset($_GET['buku'])) ? $_GET['buku'] : '11';
        $bukumin = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
        $tglm = (isset($_GET['tglawal'])) ? $_GET['tglawal'] : date('d-m-Y');
        $tgls = (isset($_GET['tglakhir'])) ? $_GET['tglakhir'] : date('d-m-Y');
        $data['tglawal']=$tglm;
        $data['tglakhir']=$tgls;

        $tglm = substr($tglm, 6, 4) . '-' . substr($tglm, 3, 2) . '-' . substr($tglm, 0, 2);
        $tgls = substr($tgls, 6, 4) . '-' . substr($tgls, 3, 2) . '-' . substr($tgls, 0, 2);

        $tahun_sppt1 = (isset($_GET['tahun_sppt1'])) ? $_GET['tahun_sppt1'] : date('Y');
        $tahun_sppt2 = (isset($_GET['tahun_sppt2'])) ? $_GET['tahun_sppt2'] : date('Y');

        $kec_kd = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';

        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();

        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kel_kd = get_user_kel_kd();
        
        

        $path_to_root = active_module_url();

        $where = "WHERE p.tgl_pembayaran_sppt BETWEEN '$tglm' AND '$tgls'
             AND p.kd_propinsi='" . KD_PROPINSI . "' 
             AND p.kd_dati2='" . KD_DATI2 . "' 
             AND p.thn_pajak_sppt BETWEEN '$tahun_sppt1' AND '$tahun_sppt2'
             AND k.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax ";

        if ($kec_kd != "000")
            $where .= " AND p.kd_kecamatan='$kec_kd'";
        if ($kel_kd != "000")
            $where .= " AND p.kd_kelurahan='$kel_kd'";
       
        $sql_query_r = "
      SELECT  tgl_pembayaran_sppt kode,tp.kd_kanwil||tp.kd_kantor||tp.kd_tp||':'||tp.nm_tp uraian, 
              sum(k.pbb_yg_harus_dibayar_sppt)  pokok, sum(p.denda_sppt) denda, 
              sum(p.jml_sppt_yg_dibayar) bayar
          FROM sppt k 
          INNER JOIN pembayaran_sppt p 
            ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 
          LEFT JOIN tempat_pembayaran tp ON p.kd_kanwil=tp.kd_kanwil and p.kd_kantor=tp.kd_kantor AND p.kd_tp=tp.kd_tp
          $where 
          GROUP BY 1,2  ";
        $sql_query_r .= "ORDER BY kode";
        //die($sql_query_r);
        $qry       = $this->db->query($sql_query_r);
        $data['detail'] = $qry->result();
        for ($i=1; $i<=5; $i++){
          for ($j=$i;$j<=5;$j++){
              $r="";
              for ($k=$i;$k<=$j;$k++) $r.="$k,";
              $r=substr($r,0,strlen($r)-1);
              if ($buku=="$i$j") $data['buku']= "$r";
            }
          }
        if ($kec_kd=='000') $kec_kd='999';  
        $data['kec_nm'] = $this->kecModel->getrecord($kec_kd);
        if ($kel_kd=='000') $kel_kd='999';
        $data['kel_nm'] = $this->kelModel->getrecord($kec_kd,$kel_kd);
        $data['tahun_sppt1'] = $tahun_sppt1;
        $data['tahun_sppt2'] = $tahun_sppt2;
        
        $this->load->view('rpt_trans_2', $data);

  }    
*/
    
    // report
	function show_rpt() {
		$cls_mtd_html = $this->router->fetch_class()."/cetak/html/";
		$cls_mtd_pdf  = $this->router->fetch_class()."/cetak/pdf/";
		$data['rpt_html'] = active_module_url($cls_mtd_html. $_SERVER['QUERY_STRING']);;
		$data['rpt_pdf']  = active_module_url($cls_mtd_pdf . $_SERVER['QUERY_STRING']);;
        $this->load->view('vjasper_viewer', $data);
	}
	
	function cetak() {
        $kec_kd=NULL;
        $kel_kd=NULL;
        $tahun=NULL;
        $tahun2=NULL;
        $bukumin=NULL;
        $bukumax=NULL;
        $buku=NULL;
        $tglawal=NULL;
        $tglakhir=NULL;
        $kd_tp='';
        
        $type = $this->uri->segment(4);
		$jenis = $this->uri->segment(5);

        $kec_kd = $this->uri->segment(6);
        $kel_kd = $this->uri->segment(7);
        $tahun_sppt1 = $this->uri->segment(8);
        $tahun_sppt2 = $this->uri->segment(9);
        $buku = $this->uri->segment(10);
        $bukumin = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];

        if ($jenis == 3) {
		   $tahun = $this->uri->segment(11);
		   $kd_tp = $this->uri->segment(12);
		} else {
		   $tglawal = $this->uri->segment(11);
		   $tglakhir = $this->uri->segment(12);
		   $kd_tp = $this->uri->segment(13);
		}
        
		if ($kec_kd == '000' && $kel_kd == '000') {
		   if ( $jenis==3 ) {$rptx = 'trans_bulan';}
		   elseif ( $jenis==2 ) {$rptx = 'trans_2';}
		   elseif ( $jenis==1 ) {$rptx = 'trans_1';}
           }
		if ($kec_kd != '000' && $kel_kd == '000') {
		   if ( $jenis==3 ) {$rptx = 'trans_bulan_kec';}
		   elseif ( $jenis==2 ) {$rptx = 'trans_2_kec';}
		   elseif ( $jenis==1 ) {$rptx = 'trans_1_kec';}
           }

	    if ($kec_kd != '000' && $kel_kd != '000')  {
		   if ( $jenis==3 ) {$rptx = 'trans_bulan_kel';}
		   elseif ( $jenis==2 ) {$rptx = 'trans_2_kel';}
		   elseif ( $jenis==1 ) {$rptx = 'trans_1_kel';}
           }
			
        $fields     = explode(',', POS_FIELD);
        $pos_fld    = '';
        $pos_join   = '';
        $pos_uraian = '';
        $fs = '';
        foreach ($fields as $f) {
            $fs = $f;
            if ($f == 'kd_kanwil_bank')
                $fs = 'kd_kanwil';
            else if ($f == 'kd_kppbb_bank')
                $fs = 'kd_kppbb';
                
            $pos_fld .= "p.{$f}, ";
            $pos_join .= "p.{$f}=tp.{$fs} and ";
            $pos_uraian .= "tp.{$fs}||";
        }
        $pos_fld = substr($pos_fld, 0, -2);
        $pos_join = substr($pos_join, 0, -4);
        $pos_uraian = substr($pos_uraian, 0, -2);
        
        if($kd_tp!='')
            $where_tp = " and {$pos_uraian}='{$kd_tp}' ";
        else
            $where_tp = '';
        
		$jasper = $this->load->library('Jasper');
		$params = array(
			"daerah" => LICENSE_TO,
			"kd_propinsi" => KD_PROPINSI, 
			"kd_dati2" => KD_DATI2, 
			"kd_kecamatan" => $kec_kd, 
			"kd_kelurahan" => $kel_kd, 
			"tahun" => $tahun, 
			"tahun_sppt1" => $tahun_sppt1, 
			"tahun_sppt2" => $tahun_sppt2, 
			"bukumin" => $bukumin, 
			"bukumax" => $bukumax, 
			"buku" => $buku, 
			"tglawal" => date('Y-m-d', strtotime($tglawal)),
			"tglakhir" => date('Y-m-d', strtotime($tglakhir)),
			"logo" => base_url("assets/img/logorpt__.jpg"),
			"dinas" => LICENSE_TO_SUB,
            
            "pos_fld" => $pos_fld,
            "pos_join" => $pos_join,
            "pos_uraian" => $pos_uraian,
            "where_tp" => $where_tp,
		);
		echo $jasper->cetak($rptx, $params, $type, false);
	}
}