<?php

class RM_Editor_Actions_Service_Addon
{

    public function add_fields($form_id, $parent_service){
        $where= array("form_id"=>$form_id);
        $data_specifier= array("%s","%d");
        $email_fields= RM_DBManager::get(RM_Fields::get_identifier(),$where, $data_specifier, $result_type = 'results', $offset = 0, $limit = 1000, $column = '*', $sort_by = null, $descending = false);
        $fields= array();
        
        if(is_array($email_fields) || is_object($email_fields))
        foreach($email_fields as $field){
           if(!in_array($field->field_type,array('Price','File','Terms','Divider','Spacing','Image','HTMLH','HTMLP','RichText','Timer','YouTubeV','Link','Iframe','Feed','Form_Chart','PriceV','SubCountV','MapV','FormData','ImageV'))){
                $fields[]= $field;
            }
        }

        return $fields;
    }

}