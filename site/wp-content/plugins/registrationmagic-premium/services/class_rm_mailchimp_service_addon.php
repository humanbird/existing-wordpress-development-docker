<?php

class RM_MailChimp_Service_Addon {
    
    public function mc_field_mapping($form, $form_options, $list = null,$parent_service)
    {
        if(!$list)
        {
            $list = $form_options->mailchimp_list;
        }
        
        $details = $parent_service->get_list_field($list);
        $service = new RM_Services();
        $all_field_objects = $service->get_all_form_fields($form);
        if (is_array($all_field_objects) || is_object($all_field_objects))
            $form_fields = '';
        $form_fields_email = '';
        $field_type_array = array();
        $field_type_array['text'] = array();
        $field_type_array['number'] = array();
        $field_type_array['dropdown'] = array();
        $field_type_array['radio'] = array();
        $field_type_array['date'] = array();
        $field_type_array['phone'] = array();
        foreach ($all_field_objects as $obj) {
            if ($obj->field_type == 'Email' || $obj->field_type == 'SecEmail'  ) {
                $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                if ($form_type_id == $form_options->mailchimp_mapped_email)
                    $form_fields_email .='<option value=' . $form_type_id . ' selected>' . $obj->field_label . '</option>';
                else
                    $form_fields_email .='<option value=' . $form_type_id . '>' . $obj->field_label . '</option>';
                //$data->all_fields[$obj->field_type . '_' . $obj->field_id] = $obj->field_label;
            }
            $field_type = $obj->field_type;


            switch ($field_type) {
                case 'Textbox':
                case 'HTMLP':
                case 'Country':
                case 'Terms':
                case 'Fname':
                case 'Lname':
                case 'BInfo':
                    $field_type = 'text';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
                case 'Hidden':
                    $field_type = 'text';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
                case 'Select':
                    $field_type = 'dropdown';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
                case 'Gender':
                case 'Radio':
                    $field_type = 'radio';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
                case 'jQueryUIDate':
                case 'Bdate':
                    $field_type = 'date';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
                case 'Number':
                    $field_type = 'number';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
                case 'Price':
                    $field_type = 'text';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
                case 'Mobile':
                case 'Phone':
                    $field_type = 'phone';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
            }
            //$data->all_fields[$obj->field_type . '_' . $obj->field_id] = $obj->field_label;
        }


        $content = '<div class="rmrow">
                            <div class="rmfield">
                                     <label> <b>Email</b></label>
                                     </div>
                            <div class="rminput">
                                     <Select name="email" selected="' . $form_options->mailchimp_mapped_email . '"><option value="">' . RM_UI_Strings::get('SELECT_FIELD') . '</option>' . $form_fields_email . '</Select>'
                . '</div>'
                . '</div>';

        if($details && isset($details['merge_fields']))
        foreach ($details['merge_fields'] as $det) {
            $options = '<option value="">' . RM_UI_Strings::get('SELECT_FIELD') . '</option>';
            foreach ($field_type_array as $type => $fld) {

                if ($fld != null && $type == $det['type']) {
                    foreach ($fld as $field_type_id => $field_type_value) {

                        if (isset($form_options->mailchimp_relations->{$list . '_' . $det['tag']}) && $field_type_id == $form_options->mailchimp_relations->{$list . '_' . $det['tag']}) {

                            $options .='<option value="' . $field_type_id . '" selected>' . $field_type_value . '</option>';
                        } else {
                            $options .='<option value="' . $field_type_id . '">' . $field_type_value . '</option>';
                        }
                    }
                }
            }


            $content .='<div class="rmrow">
                            <div class="rmfield">
                                     <label> <b>' . $det['name'] . '</b></label>
                            </div>
                            <div class="rminput">
                                     <Select name="' . $list . '_' . $det['tag'] . '">' . $options . '</Select>'
                    . '</div>'
                    . '</div>';
        }
       
        return $content;

    }

}