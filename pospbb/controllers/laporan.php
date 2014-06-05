<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class laporan extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('login')) {
            $this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
            redirect('login');
            exit;
        }

        if (!is_super_admin() && !isset($this->session->userdata['tpnm'])) {
            show_404();
            exit;
        }

        $module = 'POSL';
        $this->load->library('module_auth', array(
            'module' => $module
        ));

        $this->load->model(array(
            'apps_model'
        ));
        $this->load->model(array(
            'pbb/refkelurahan_model',
            'pbb/tp_model',
            'rpt_model',
            'pos_user_model'
        ));
    }

    public function index()
    {
       if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }

        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('laporan/harian');
        $data['current'] = 'laporan';
        $data['keldata'] = $r = $this->refkelurahan_model->get_array();
        $data['tpnm']    = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';
        $data['users']   =  $this->pos_user_model->get_tp_user();
        //print_r($data['users']);
        $this->fvalidation();
        $this->load->view('lapv', $data);
    }

    private function fvalidation()
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('tgl', 'Tanggal', 'required');
        $this->form_validation->set_rules('buku', 'Jenis Buku', 'required');
        $this->form_validation->set_rules('buku', 'Jenis Buku', 'required');
    }

    public function harian()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        $this->fvalidation();
        $tgl = '';
        if (isset($_POST['tgl'])) {
            if ($_POST['tgl'] != '')
                $tgl = date('Y-m-d', strtotime($_POST['tgl']));
        }
        if ($tgl == '')
            $tgl = date('Y-m-d');

        $data['kelnm']  = $_POST['kel'];
        $data['bukunm'] = buku_name($_POST['buku']);
        $data['banknm'] = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : 'TP Tidak Valid';
        $data['user_id'] = $_POST['user'];
        die ($data['user_id']);
        /*$this->tp_model->get_nama(
        $this->session->userdata('kd_kanwil'),
        $this->session->userdata('kd_kantor'),
        $this->session->userdata('kd_tp')
        );*/

        $data['tgl']  = date('d-m-Y', strtotime($tgl));
        $r            = $this->rpt_model->get_lap_harian($tgl);
        
        $data['rows'] = $r;
        $this->load->view('lapharianrpt', $data);
    }


    public function mingguan()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }

        //$filter = $this->session->userdata('pos_filter');
        //$filter = isset($filter) ? $filter : '';
        //$data['filter']  = $filter;

        $data['apps']    = $this->apps_model->get_active_only();
        $data['current'] = 'stts';
        $this->load->view('lapmingguv', $data);
    }

    public function lppm()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }

        //$filter = $this->session->userdata('pos_filter');
        //$filter = isset($filter) ? $filter : '';
        //$data['filter']  = $filter;

        $data['apps']    = $this->apps_model->get_active_only();
        $data['current'] = 'stts';
        $this->load->view('laplppmv', $data);
    }

    public function  cetak_pdf() {
		$tgl  = $_GET['tgl'];
		$buku = $_GET['buku'];
		$urut = $_GET['urut'];
        $kel  = $_GET['kel'];

        $b_awal  = buku_bawah($buku);
        $b_akhir = buku_atas($buku);
        
		//tambahan parameter join untuk relasi tabel pembayaran sppt dgn tempat pembayaran 
		$join = '';
		if (DEF_POS_TYPE==1) {
			$join =" AND a.kd_kanwil=tp.kd_kanwil AND a.kd_kantor=tp.kd_kantor AND a.kd_tp=tp.kd_tp 
				AND tp.nm_tp= '" . $this->session->userdata['tpnm'] ."' ";
		} elseif (DEF_POS_TYPE==2) {
			$join =" AND a.kd_kanwil_bank=tp.kd_kanwil AND a.kd_kppbb_bank=tp.kd_kppbb AND a.kd_bank_tunggal=tp.kd_bank_tunggal AND a.kd_bank_persepsi=tp.kd_bank_persepsi AND  a.kd_tp=tp.kd_tp 
				AND tp.nm_tp= '" . $this->session->userdata['tpnm'] ."' ";
		} 
		
        $where = '';
        $kel = substr($kel, 0, 7);
        if ($kel != '000.000') {
            $where .= " and a.kd_kecamatan='" . substr($kel, 0, 3) . "' and a.kd_kelurahan='" . substr($kel, -3) . "' ";
        }

        $uid = $_POST['user'];
        if ($uid != '') {
            $where .= " and a.user_id=" . $uid;
        }
                 
        $order = "";
        if ($urut == 1)
            $order = " order by  b.nm_wp_sppt";
        elseif ($urut == 2)
            $order = " order by  a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan, a.kd_blok, a.no_urut, a.kd_jns_op";
        else if ($urut == 3)
            $order = " order by  a.thn_pajak_sppt";
        else
            $order = " order by  a.jml_sppt_yg_dibayar";
            
        $params = array(
            "daerah" => LICENSE_TO,
            "dinas" => LICENSE_TO_SUB,
            "logo" => base_url("assets/img/logorpt__.jpg"),

            "kd_propinsi" => KD_PROPINSI,
            "kd_dati2" => KD_DATI2,
            
            "tanggal" => date('Y-m-d', strtotime($tgl)),
            "bukumin" => $b_awal,
            "bukumax" => $b_akhir,
            "buku"    => $buku,
            "join"    => $join,
            "kondisi" => $where.$order,
        );

        $jasper = $this->load->library('Jasper');
        echo $jasper->cetak("harian", $params, "pdf", false);
    }
    
	public function csv_download() {
        $tgl    = date('Y-m-d', strtotime($_POST['tgl']));
        $kel    = $_POST['kel'];
        $bukunm = buku_name($_POST['buku']);
        
		header("Content-type: text/plain"); 
		header("Cache-Control: no-store, no-cache"); 
		header('Content-Disposition: attachment; filename="Laporan Harian '.$tgl.' '.$kel.' - Buku '.$bukunm.'.csv"'); 
		
        if($rows = $this->rpt_model->get_lap_harian2($tgl)){
            $title = array('NOP','THN','NAMA WP','PBB','DENDA','TOTAL','TGL.BAYAR');
            $this->csv_encode( $rows, $title ); 
        } else {
            echo "Tidak ada data";
        }
        exit;
	}
	
	function csv_encode($aaData, $aHeaders = NULL) {
		// output headers
		if ($aHeaders) echo implode('|', $aHeaders ) . "\r\n";

		foreach ($aaData as $aRow) {
			echo implode('|', $aRow) . "\r\n";
		}
	}
}
