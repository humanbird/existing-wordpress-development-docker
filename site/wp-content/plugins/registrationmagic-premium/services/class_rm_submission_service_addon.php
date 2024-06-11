<?php

class RM_Submission_Service_Addon
{
   
    public function get_notes($submission_id,$parent_service){
       return RM_DBManager::get_all_notes_for_submission($submission_id);
    }
   
}