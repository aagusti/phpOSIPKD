<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class op_rpt extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        
        if (active_module() != 'pospbb') {
            show_404();
            exit;
        }
        $this->load->model(array(
            'apps_model',
            'login_model',
            'pbbm_model'
        ));
        $this->pbbm_model->set_userarea();
        $this->load->model(array(
            'kecModel',
            'kelModel'
        ));
        
    }
    
    function index() {}
    
	function show_rpt() {
		$cls_mtd_html = $this->router->fetch_class()."/cetak/html/";
		$cls_mtd_pdf  = $this->router->fetch_class()."/cetak/pdf/";
		$data['rpt_html'] = active_module_url($cls_mtd_html. $_SERVER['QUERY_STRING']);;
		$data['rpt_pdf']  = active_module_url($cls_mtd_pdf . $_SERVER['QUERY_STRING']);;
        $this->load->view('vjasper_viewer', $data);
	}
	
	function cetak() {
        $type  = $this->uri->segment(4);
		$rptx  = 'op';
        $nopkd = $this->uri->segment(5);

        $nop   = str_replace('.', '', $nopkd);
        $nop   = str_replace('-', '', $nop);
				
        $kec_kd  = substr($nop, 4, 3);
        $kel_kd  = substr($nop, 7, 3);
        $blok_kd = substr($nop, 10, 3);
        $urut_no = substr($nop, 13, 4);
        $jns_kd  = substr($nop, 17, 1);
		
		$jasper = $this->load->library('Jasper');
		$params = array(
			"daerah" => LICENSE_TO,
			"kd_propinsi" => KD_PROPINSI, 
			"kd_dati2" => KD_DATI2, 
			"kd_kecamatan" => $kec_kd, 
			"kd_kelurahan" => $kel_kd,
			"kd_blok" => $blok_kd,
			"no_urut" => $urut_no,
			"kd_jns_op" => $jns_kd,
			"logo" => base_url("assets/img/logorpt__.jpg"),
			"dinas" => LICENSE_TO_SUB,
		);
		echo $jasper->cetak($rptx, $params, $type, false);
	}
}