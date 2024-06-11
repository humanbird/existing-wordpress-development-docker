<?php

class RM_Frontend_Field_Base_Addon
{

    public function add_custom_validations($parent_model)
    {
        if(isset($parent_model->field_model->field_options->field_is_unique))
        {  
            $exclude_value= '';
            if(isset($_GET['submission_id']))
            {
                $sub_id= absint($_GET['submission_id']);
                if($sub_id>0){
                    $submision_row= RM_DBManager::get("SUBMISSION_FIELDS",array('field_id' => $parent_model->field_model->field_id,'submission_id'=>$sub_id),array('%d','%d'),'results',0,1,'value');
                    if(!empty($submision_row[0])){
                        $exclude_value= $submision_row[0]->value;
                    }
                }
            }
        
            $parent_model->pfbc_field->addValidation(new Validation_Unique($parent_model->field_model->field_options->un_err_msg,new RM_Unique_Validator($parent_model->field_model->field_id,$exclude_value)));
        }
        
        if($parent_model->is_primary() && $parent_model->field_type=='Email')
        {  
           $parent_model->pfbc_field->addValidation(new Validation_SubLimitByEmail(RM_UI_Strings::get('ERR_SUB_LIMIT_USER'),new RM_Sub_Limit_Validator($parent_model->field_model->field_id,$parent_model->field_model->get_form_id())));
        }

        if($parent_model->field_type=='Textbox' && isset($parent_model->field_model->field_options->field_min_length) && !empty($parent_model->field_model->field_options->field_min_length)) {
            $parent_model->pfbc_field->addValidation(new Validation_MinLength(absint($parent_model->field_model->field_options->field_min_length)));
        }

        if($parent_model->field_type=='Textbox' && isset($parent_model->field_model->field_options->field_max_length) && !empty($parent_model->field_model->field_options->field_max_length)) {
            $parent_model->pfbc_field->addValidation(new Validation_MaxLength(absint($parent_model->field_model->field_options->field_max_length)));
        }
    }

}