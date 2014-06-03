<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class bayar extends CI_Controller
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
        
        $module = 'POSC';
        $this->load->library('module_auth', array(
            'module' => $module
        ));
        
        $this->load->model(array(
            'apps_model'
        ));
        $this->load->model(array(
            'sppt_model',
            'payment_model'
        ));
    }
    
    public function index()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        
        $filter         = $this->session->userdata('pos_filter');
        $filter         = isset($filter) ? $filter : '';
        $data['filter'] = $filter;
        $data['prefix'] = KD_PROPINSI . "." . KD_DATI2;
        $data['tpnm']   = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';
        
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('bayar/update_pmd');
        $data['current'] = 'stts';
        
        $this->load->view('bayarv', $data);
    }
    
    public function cari()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        
        $nop = $this->uri->segment(4);
        $thn = $this->uri->segment(5);
        
        if ($nop && $thn && $query = $this->sppt_model->get_by_nop_thn($nop, $thn)) {
            //
            
            $sisa  = (float) $query->pbb_yg_harus_dibayar_sppt - ($query->jml_sppt_yg_dibayar - (float) $query->denda_sppt);
            $denda = 0;
            if (date($query->tgl_jatuh_tempo_sppt) < date('Y-m-d'))
                $denda = hitdenda($sisa, $query->tgl_jatuh_tempo_sppt);
            
            $utang     = $sisa + $denda;
            $terbilang = terbilang($utang);
            $query     = (object) array_merge((array) $query, array(
                'found' => 1,
                'sisa' => $sisa,
                'denda' => $denda,
                'utang' => $utang,
                'terbilang' => $terbilang
            ));
            
            
            echo json_encode($query);
        } else {
            $result['found'] = 0;
            echo json_encode($result);
        }
    }
    
    private function fvalidation()
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('nop', 'NOP', 'required');
        $this->form_validation->set_rules('tahun', 'Tahun', 'required|numeric');
    }
    
    function update_pmd()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_insert);
            redirect('info');
        }
        
        $data['faction'] = active_module_url('bayar/update_pmb');
        $data['current'] = 'stts';
        
        
        $this->fvalidation();
        
        if ($this->form_validation->run() == TRUE) {
            $nop = trim($this->input->post('prefix')) . trim($this->input->post('nop'));
            
            $nop = urldecode($nop);
            $nop = str_replace('.', '', $nop);
            $nop = str_replace(' ', '', $nop);
            $nop = str_replace('-', '', $nop);
            
            $thn = $this->input->post('tahun');
            
            $kd_propinsi    = substr($nop, 0, 2);
            $kd_dati2       = substr($nop, 2, 2);
            $kd_kecamatan   = substr($nop, 4, 3);
            $kd_kelurahan   = substr($nop, 7, 3);
            $kd_blok        = substr($nop, 10, 3);
            $no_urut        = substr($nop, 13, 4);
            $kd_jns_op      = substr($nop, -1);
            $thn_pajak_sppt = $thn;
            
            $denda_sppt          = $this->input->post('denda');
            $jml_sppt_yg_dibayar = $this->input->post('utang');
            
            $data['sisa'] = $denda_sppt;
            
            if ($nop && $thn && $query = $this->sppt_model->get_by_nop_thn($nop, $thn)) {
                
                $sisa = (float) $query->pbb_yg_harus_dibayar_sppt - ($query->jml_sppt_yg_dibayar - (float) $query->denda_sppt);
                
                if ($sisa < 1) {
                    $data['yes'] = "no";
                    echo json_encode($data);
                    exit;
                }
            }
            
            $tgl_pembayaran_sppt = date('Y-m-d');
            $tgl_rekam_byr_sppt  = date('Y-m-d');
            $nip_rekam_byr_sppt  = $this->session->userdata('nip');
            $pembayaran_sppt_ke  = $this->payment_model->get_pembayaran_ke($nop, $thn);
            
            $data = array(
                'kd_propinsi' => $kd_propinsi,
                'kd_dati2' => $kd_dati2,
                'kd_kecamatan' => $kd_kecamatan,
                'kd_kelurahan' => $kd_kelurahan,
                'kd_blok' => $kd_blok,
                'no_urut' => $no_urut,
                'kd_jns_op' => $kd_jns_op,
                'thn_pajak_sppt' => $thn_pajak_sppt,
                'pembayaran_sppt_ke' => $pembayaran_sppt_ke,
                'denda_sppt' => $denda_sppt,
                'jml_sppt_yg_dibayar' => $jml_sppt_yg_dibayar,
                'tgl_pembayaran_sppt' => $tgl_pembayaran_sppt,
                'tgl_rekam_byr_sppt' => $tgl_rekam_byr_sppt,
                'nip_rekam_byr_sppt' => $nip_rekam_byr_sppt
            );
            
            $fields = explode(',', POS_FIELD);
            foreach ($fields as $f) {
                $f    = trim($f);
                $data = array_merge($data, array(
                    trim($f) => $this->session->userdata[$f]
                ));
            }
            $this->payment_model->update_pmb($data);
            
            $data['nop'] = $nop;
            $data['thn'] = $thn;
            $data['ke']  = $pembayaran_sppt_ke;
            $data['yes'] = "yes";
            echo json_encode($data);
        } else {
            $data['yes'] = "no";
            echo json_encode($data);
        }
    }
    
    public function cetak()
    {        
        $nop = $this->uri->segment(4);
        $thn = $this->uri->segment(5);
        $ke  = $this->uri->segment(6);
        $this->load->model(array(
            'payment_model'
        ));
        if ($nop && $thn && $ke && $query = $this->payment_model->get_by_nop_thn_ke($nop, $thn, $ke)) {
            $this->load->view('stts1v', $query);
        }
    }
    
    public function cetak_pdf()
    {        
        $nop = $this->uri->segment(4);
        $thn = $this->uri->segment(5);
        $ke  = $this->uri->segment(6);
        
        $this->load->model(array(
            'payment_model'
        ));
        if ($nop && $thn && $query = $this->payment_model->get_by_nop_thn_ke($nop, $thn, $ke)) {
            $kdprop  = '';
            $kddati  = '';
            $kdkec   = '';
            $kdkel   = '';
            $kdblok  = '';
            $nourut  = '';
            $jns     = '';
			$join    = '';
            $nop_num = preg_replace("/[^0-9]/", "", $nop);
            $nop_dot = preg_replace("/([0-9]{2})([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{1})/", "$1.$2.$3.$4.$5.$6.$7", $nop_num);
            
            $kode = explode(".", $nop_dot);
            list($kdprop, $kddati, $kdkec, $kdkel, $kdblok, $nourut, $jns) = $kode;
            
 			//tambahan parameter join untuk relasi tabel pembayaran sppt dgn tempat pembayaran 
			if (DEF_POS_TYPE==1) {
				$join =" ps.kd_kanwil=tp.kd_kanwil AND ps.kd_kantor=tp.kd_kantor AND ps.kd_tp=tp.kd_tp ";
			} elseif (DEF_POS_TYPE==2) {
				$join =" ps.kd_kanwil_bank=tp.kd_kanwil AND ps.kd_kppbb_bank=tp.kd_kppbb AND ps.kd_bank_tunggal=tp.kd_bank_tunggal AND ps.kd_bank_persepsi=tp.kd_bank_persepsi AND  ps.kd_tp=tp.kd_tp ";
			} 
            
            $sn = date('dmY', strtotime($query->tgl_pembayaran_sppt));
            $sn .= $kdprop . $kddati . $kdkec . $kdkel . $kdblok . $nourut . $jns . $thn;
            
            $params = array(
                "daerah" => LICENSE_TO,
                "dinas" => LICENSE_TO_SUB,
                "logo" => base_url("assets/img/logorpt__.jpg"),
                
                "kd_propinsi" => $kdprop,
                "kd_dati2" => $kddati,
                "kd_kecamatan" => $kdkec,
                "kd_kelurahan" => $kdkel,
                "kd_blok" => $kdblok,
                "no_urut" => $nourut,
                "kd_jenis_op" => $jns,
                "thn_pajak_sppt" => $thn,
                "pembayaran_sppt_ke" => $ke,
                "sn" => $sn,
                "join" => $join 
            );
            
            $jasper = $this->load->library('Jasper');
            echo $jasper->cetak(POS_WIL."/stts", $params, "pdf", false);
        }
    }
    
    public function cetak_bank()
    {        
        $nop = $this->uri->segment(4);
        $thn = $this->uri->segment(5);
        $ke  = $this->uri->segment(6);
        
        $this->load->model(array(
            'payment_model'
        ));
        if ($nop && $thn && $query = $this->payment_model->get_by_nop_thn_ke($nop, $thn, $ke)) {
            $kdprop  = '';
            $kddati  = '';
            $kdkec   = '';
            $kdkel   = '';
            $kdblok  = '';
            $nourut  = '';
            $jns     = '';
			$join    = '';
            $nop_num = preg_replace("/[^0-9]/", "", $nop);
            $nop_dot = preg_replace("/([0-9]{2})([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{1})/", "$1.$2.$3.$4.$5.$6.$7", $nop_num);
            
            $kode = explode(".", $nop_dot);
            list($kdprop, $kddati, $kdkec, $kdkel, $kdblok, $nourut, $jns) = $kode;
            
 			//tambahan parameter join untuk relasi tabel pembayaran sppt dgn tempat pembayaran 
			if (DEF_POS_TYPE==1) {
				$join =" ps.kd_kanwil=tp.kd_kanwil AND ps.kd_kantor=tp.kd_kantor AND ps.kd_tp=tp.kd_tp ";
			} elseif (DEF_POS_TYPE==2) {
				$join =" ps.kd_kanwil_bank=tp.kd_kanwil AND ps.kd_kppbb_bank=tp.kd_kppbb AND ps.kd_bank_tunggal=tp.kd_bank_tunggal AND ps.kd_bank_persepsi=tp.kd_bank_persepsi AND  ps.kd_tp=tp.kd_tp ";
			} 
            
            $sn = date('dmY', strtotime($query->tgl_pembayaran_sppt));
            $sn .= $kdprop . $kddati . $kdkec . $kdkel . $kdblok . $nourut . $jns . $thn;

			//tambahan terbilang
		    $terbilang=terbilang($query->jml_sppt_yg_dibayar);
            
            $params = array(
                "daerah" => LICENSE_TO,
                "dinas" => LICENSE_TO_SUB,
                "logo" => base_url("assets/img/logorpt__.jpg"),
                
                "kd_propinsi" => $kdprop,
                "kd_dati2" => $kddati,
                "kd_kecamatan" => $kdkec,
                "kd_kelurahan" => $kdkel,
                "kd_blok" => $kdblok,
                "no_urut" => $nourut,
                "kd_jenis_op" => $jns,
                "thn_pajak_sppt" => $thn,
                "pembayaran_sppt_ke" => $ke,
                "sn" => $sn,
                "join" => $join,
                "terbilang" => $terbilang, 
            );
            
            $jasper = $this->load->library('Jasper');
            echo $jasper->cetak(POS_WIL."/stts_bank", $params, "pdf", false);
        }
    }
}
