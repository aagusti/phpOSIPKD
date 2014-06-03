<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class status extends CI_Controller
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
        
        $module = 'possb';
        $this->load->library('module_auth', array(
            'module' => $module
        ));
        
        $this->load->model(array(
            'apps_model',
            'sppt_model'
        ));
    }
    
    public function index()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        
        $filter          = $this->session->userdata('pos_filter');
        $filter          = isset($filter) ? $filter : '';
        $data['filter']  = $filter;
        $data['current'] = 'stts';
        $data['prefix']  = KD_PROPINSI . KD_DATI2;
        $data['tpnm']    = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('vstatusbayar', $data);
    }
    
    function grid()
    {
        $filter = $this->uri->segment(4);
        $unitid = $this->uri->segment(5);
        
        $this->session->set_userdata('pos_filter', $filter);
        
        $aColumns = array(
            'nop',
            'thn_pajak_sppt',
            'nm_wp_sppt',
            'pbb_yg_harus_dibayar_sppt',
            'status_pembayaran_sppt'
        );
        
        $iDisplayStart  = $this->input->get_post('iDisplayStart', true);
        $iDisplayLength = $this->input->get_post('iDisplayLength', true);
        $iSortCol_0     = $this->input->get_post('iSortCol_0', true);
        $iSortingCols   = $this->input->get_post('iSortingCols', true);
        $sSearch        = $this->input->get_post('sSearch', true);
        $sEcho          = $this->input->get_post('sEcho', true);
        
        
        // Paging
        $str_limit = '';
        if (isset($iDisplayStart) && $iDisplayLength != '-1') {
            $str_limit = "limit $iDisplayLength offset $iDisplayStart ";
        }
        // $str_limit = "limit 15 offset 1 ";
        
        // Ordering
        $str_order_by = '';
        if (isset($iSortCol_0)) {
            for ($i = 0; $i < intval($iSortingCols); $i++) {
                $iSortCol  = $this->input->get_post('iSortCol_0');
                $bSortable = $this->input->get_post('bSortable_' . intval($iSortCol), true);
                $sSortDir  = $this->input->get_post('sSortDir_' . $i, true);
                
                if ($bSortable == 'true') {
                    $col_num = intval($iSortCol) + 1;
                    $str_order_by .= $col_num . ' ' . $sSortDir . ',';
                }
            }
            $str_order_by = substr($str_order_by, 0, -1);
            $str_order_by = "order by $str_order_by ";
        }
        
        // Filtering
        $str_where = '';
        if (isset($sSearch) && !empty($sSearch)) {
            for ($i = 0; $i < count($aColumns); $i++) {
                $bSearchable = $this->input->get_post('bSearchable_' . $i, true);
                
                // Individual column filtering
                if (isset($bSearchable) && $bSearchable == 'true') {
                    if ($i == 0) {
                        $str_where .= "s.kd_propinsi||'.'||s.kd_dati2||'.'||s.kd_kecamatan||'.'||s.kd_kelurahan||'.'||s.kd_blok||'.'||s.no_urut||'.'||s.kd_jns_op like '%" . $this->db->escape_like_str($sSearch) . "%' or ";
                    } else {
                        $str_where .= " lower(" . $aColumns[$i] . ") like lower('%" . $this->db->escape_like_str($sSearch) . "%') or ";
                    }
                }
            }
            if ($str_where != '') {
                $str_where = substr($str_where, 0, -3);
                $str_where = " and $str_where ";
            }
        }
        
        for ($i = 0; $i < count($aColumns); $i++) {
            $sSearch_i = $this->input->get_post('sSearch_' . $i, true);
            if (isset($sSearch_i) && !empty($sSearch_i)) {
                
                if ($i == 0) {
                    $str_where .= " s.kd_propinsi||'.'||s.kd_dati2||'.'||s.kd_kecamatan||'.'||s.kd_kelurahan||'.'||s.kd_blok||'.'||s.no_urut||'.'||s.kd_jns_op like '%" . $this->db->escape_like_str($sSearch_i) . "%' or ";
                } else {
                    $str_where .= " lower(" . $aColumns[$i] . ") like lower('%" . $this->db->escape_like_str($sSearch_i) . "%') or ";
                }
            }
        }
        
        if (strlen($str_where) > 0) {
            $str_where = substr($str_where, 0, -3);
            $str_where = " and ({$str_where}) ";
        }
        
        
        //
        $result = $this->sppt_model->data_grid($str_where, $str_limit, $str_order_by, $filter);
        
        // Output
        $output = array(
            'sEcho' => intval($sEcho),
            'iTotalRecords' => $result['tot_rows'],
            'iTotalDisplayRecords' => $result['num_rows'],
            'sql' => $result['sql'],
            'aaData' => array()
        );
        
        foreach ($result['query'] as $aRow) {
            $row = array();            
            foreach ($aColumns as $col) {
                if ($col == 'status_pembayaran_sppt')
                    $row[] = ($aRow[$col] > 0 ? 'Sudah' : 'Belum');
                elseif ($col == 'pbb_yg_harus_dibayar_sppt')
                    $row[] = number_format($aRow[$col],0,'.',',');
                else
                    $row[] = $aRow[$col]; 
            }
            
            $output['aaData'][] = $row;
        }
        
        echo json_encode($output);
    }
    
    function grid_pmb()
    {
        $nop = $this->uri->segment(4);
        
        $i = 0;
        if ($nop && $query = $this->sppt_model->data_grid_pmb($nop)) {
            foreach ($query as $row) {
                $responce->aaData[$i][] = $row->thn_pajak_sppt;
                $responce->aaData[$i][] = number_format($row->pbb_yg_harus_dibayar_sppt, 0, ',', '.');
                $responce->aaData[$i][] = ($row->status_pembayaran_sppt > 0 ? 'Sudah' : 'Belum');
                $i++;
            }
        } else {
            $responce->sEcho                = 1;
            $responce->iTotalRecords        = "0";
            $responce->iTotalDisplayRecords = "0";
            $responce->aaData               = array();
        }
        echo json_encode($responce);
    }
}