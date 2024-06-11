<?php

/**
 * This class represents the Notes model of the plugin
 * 
 * This class is data entity for the notes section.It contains the data for notes.
 * and some basic database operations for this model.
 *
 * @author CMSHelplive.
 */
class RM_Notes_Addon
{

    public function get_note_type($parent_model) {
        $opt= maybe_unserialize($parent_model->note_options);
        return $opt->type;
    }
    
    public function set_type($type,$parent_model) {
         $parent_model->note_options->type = $type;
    }
    
    public function set_initialized($status,$parent_model) {
        $parent_model->initialized = $status;
    }

}