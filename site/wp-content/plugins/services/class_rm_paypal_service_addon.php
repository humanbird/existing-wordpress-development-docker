<?php

class RM_Paypal_Service_Addon {

    public function check_approval_settings($trasaction_id) {
        $where=array("txn_id"=>$trasaction_id);
        $data_specifier=array("%s");
        $form_id=RM_DBManager::get('PAYPAL_LOGS', $where, $data_specifier, $result_type = 'col', $offset = 0, $limit = 9999999, $column = 'form_id', $sort_by = null, $descending = false);
        $form_model=new RM_Forms;
        $form_model->load_from_db($form_id['0']);

        if($form_model->form_options->user_auto_approval=='default') {
            $gopt=new RM_Options;
            return $gopt->get_value_of('user_auto_approval');
        } else {
            return $form_model->form_options->user_auto_approval;
        }
    }
}