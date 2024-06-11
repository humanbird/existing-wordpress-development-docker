<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RM_Frontend_Field_Price_Addon
{

    public function get_pfbc_field($parent_model)
    {
        if ($parent_model->pfbc_field)
            return $parent_model->pfbc_field;
        else
        {
            $pfbc_field_array = array();
            $paypal_field = new RM_PayPal_Fields();
            $res = $paypal_field->load_from_db($parent_model->pp_field_id);

            if (!$res)
                return null;

            $label = $parent_model->get_formatted_label();
            $name = $parent_model->field_name;
            //$pfbc_field_array[] = new Element_Hidden('rm_payment_form', 'pp');

            $properties = array();
            $cond_class = '';

            if (isset($parent_model->field_options['required']))
                $properties['required'] = '1';
            
            if (isset($parent_model->field_options['longDesc']))
                $properties['longDesc'] = $parent_model->field_options['longDesc'];
            
            if (isset($parent_model->field_options['style']))
                $properties['style'] = $parent_model->field_options['style']; 
            
            if (isset($parent_model->field_options['labelStyle']))
                $properties['labelStyle'] = $parent_model->field_options['labelStyle'];

            if(isset($parent_model->field_model->field_options->conditions)) {
                $rule_counter = 1;
                if(!empty($parent_model->field_model->field_options->conditions['rules'])) {
                    foreach($parent_model->field_model->field_options->conditions['rules'] as $rule) {
                        $field = new RM_Fields();
                        $field->load_from_db(absint($rule['controlling_field']));
                        $ftype = $field->get_field_type();
                        $properties['data-cond-option'] = $rule_counter > 1 ? $properties['data-cond-option'] . '|' . $ftype . '_' . $rule['controlling_field'] : $ftype . '_' . $rule['controlling_field'];
                        $properties['data-cond-operator'] = $rule_counter > 1 ? $properties['data-cond-operator'] . '|' . $rule['op'] : $rule['op'];
                        $properties['data-cond-value'] = $rule_counter > 1 ? $properties['data-cond-value'] . '|' . $rule['values'][0] : $rule['values'][0];
                        $rule_counter++;
                    }
                    $cond_class = 'data-conditional';
                    if(isset($parent_model->field_model->field_options->conditions['settings']['combinator'])) {
                        $properties['data-cond-comb'] = $parent_model->field_model->field_options->conditions['settings']['combinator'];
                    }
                    $properties['data-cond-action'] = $parent_model->field_model->field_options->conditions['action'];
                }
            }
            
            $properties['data-rmfieldtype'] = 'price';
            
            if($paypal_field->get_extra_options('allow_quantity') == 'yes') {
                if($paypal_field->get_extra_options('max_quantity') == 0) {
                    $quantity_field = new Element_Number('&times;',$name."_qty", array('value' => intval($paypal_field->get_extra_options('min_quantity')) > 1 ? $paypal_field->get_extra_options('min_quantity') : 1, 'min' => $paypal_field->get_extra_options('min_quantity'), 'step' => 1, 'class' => 'rm_price_field_quantity'));
                } else {
                    $quantity_field = new Element_Number('&times;',$name."_qty", array('value' => intval($paypal_field->get_extra_options('min_quantity')) > 1 ? $paypal_field->get_extra_options('min_quantity') : 1, 'min' => $paypal_field->get_extra_options('min_quantity'), 'max' => $paypal_field->get_extra_options('max_quantity'), 'step' => 1, 'class' => 'rm_price_field_quantity'));
                }
            } else {
                $quantity_field = null;
            }
                        
            switch ($paypal_field->get_type())
            {
                case "fixed":
                    if ($parent_model->currency_pos == 'before')
                        $properties['value'] = $paypal_field->get_name() . " (" . $parent_model->curr_symbol . " " . $paypal_field->get_value() . ")";
                    else
                        $properties['value'] = $paypal_field->get_name() . " (" . $paypal_field->get_value() . " " . $parent_model->curr_symbol . ")";
                    $properties['readonly'] = 1;
                    $properties['class'] = $paypal_field->get_class() . " " . $cond_class;
                    $properties['data-rmfieldprice'] = $paypal_field->get_value();
                    
                    if ($paypal_field->get_extra_options('show_on_form') != 'yes')
                        $element = new Element_Hidden($name, $properties['value'], $properties);
                    else
                        $element = new Element_Textbox($label, $name, $properties, array('exclass_row'=>'rm_pricefield_row','sub_element'=>$quantity_field));
                    break;

                case "userdef":
                    if (isset($properties['readonly']))
                        unset($properties['readonly']);
                    if (isset($properties['value']))
                        unset($properties['value']);
                    $properties['class'] = $paypal_field->get_class() . " " . $cond_class;
                    $properties['placeholder'] = $paypal_field->get_name();
                    $properties['min'] = 0.01;
                    //$properties['step'] = 0.01;
                    $element = new Element_Number($label, $name, $properties);
                    break;


                case "multisel":
                case "dropdown":
                    //echo '<pre>'; var_dump($paypal_field); die;
                    $labels = maybe_unserialize($paypal_field->get_option_label());
                    $prices = maybe_unserialize($paypal_field->get_option_price());
                    $vals = array();
                    $temp = array();
                    $i = 0;
                    foreach ($prices as $price)
                    {
                        {
                            if ($parent_model->currency_pos == 'before')
                                $vals["_" . $i] = $labels[$i] . " (" . $parent_model->curr_symbol . " " . $price . ")";
                            else
                                $vals["_" . $i] = $labels[$i] . " (" . $price . " " . $parent_model->curr_symbol . ")";
                        }
                        
                        $temp["_".$i] = $price;
                        
                        $i++;
                    }

                    $properties['data-rmfieldprice'] = json_encode($temp);
                    $properties['id'] = 'id_rm_multisel_paypal_field_'.$parent_model->field_id;
                    $properties['class'] = $cond_class;

                    if ($paypal_field->get_type() == 'multisel')
                        $element = new Element_Checkbox($label, $name, $vals, $properties, array('exclass_row'=>'rm_pricefield_checkbox','sub_element'=>$quantity_field));
                    else
                    {
                        if (!isset($field_data->properties['required']))
                            $vals = array(null => RM_UI_Strings::get('SELECT_FIELD_FIRST_OPTION')) + $vals;
                        $element = new Element_Select($label, $name, $vals, $properties, array('exclass_row'=>'rm_pricefield_row','sub_element'=>$quantity_field));
                    }
                    break;
            }

            $pfbc_field_array[] = $element;

            $parent_model->pfbc_field = $pfbc_field_array;

            return $parent_model->pfbc_field;
        }
    }

    public function get_prepared_data($request,$parent_model)
    {
        $paypal_field = new RM_PayPal_Fields();
        $res = $paypal_field->load_from_db($parent_model->pp_field_id);

        if (!$res)
            return null;

        switch ($paypal_field->get_type())
        {
            case "fixed":
                if(isset($request[$parent_model->field_name."_qty"]))
                    $quantity = intval($request[$parent_model->field_name."_qty"]);
                else
                    $quantity = 1;
                $value = isset($request[$parent_model->field_name]) ? $request[$parent_model->field_name] : null;
                $value = $value ? "$value &times; $quantity" : null;
                break;
                
            case "userdef":
                $data = new stdClass;
                $data->field_id = $parent_model->get_field_id();
                $data->type = 'Price';
                $data->label = $parent_model->get_field_label();
                $price_entered = isset($request[$parent_model->field_name])? $request[$parent_model->field_name] : null;
                $data->value = !$price_entered? null : $price_entered;
                
                if($data->value)
                {
                    if ($parent_model->currency_pos == 'before')
                        $data->value = $parent_model->curr_symbol . " " . $data->value;
                    else
                        $data->value = $data->value . " " . $parent_model->curr_symbol;
                }                
                return $data;              
            
            case "multisel":
                $tmp_v = maybe_unserialize($paypal_field->get_option_price());
                $tmp_l = maybe_unserialize($paypal_field->get_option_label());
                $gopt = new RM_Options;
                $val_arr = array();
                $value = isset($request[$parent_model->field_name]) ? $request[$parent_model->field_name] : null;

                if ($value)
                {
                    foreach ($value as $pf_single_val)
                    {
                        $index = (int) substr($pf_single_val, 1);
                        if (!isset($tmp_v[$index]))
                            continue;    
                        if(isset($request[$parent_model->field_name."_qty"], $request[$parent_model->field_name."_qty"][$pf_single_val]))
                            $quantity = intval($request[$parent_model->field_name."_qty"][$pf_single_val]);
                        else
                            $quantity = 1;
                        $val_arr[] = $tmp_l[$index] . " (" . $gopt->get_formatted_amount($tmp_v[$index]) . ") &times; $quantity";
                    }
                    $value = $val_arr;
                }
                break;

            case "dropdown":
                $tmp_v = maybe_unserialize($paypal_field->get_option_price());
                $tmp_l = maybe_unserialize($paypal_field->get_option_label());
                $gopt = new RM_Options;
                $value = isset($request[$parent_model->field_name]) ? $request[$parent_model->field_name] : null;
                if ($value)
                {
                    $index = (int) substr($value, 1);
                    if (!isset($tmp_v[$index]))
                        break;
                    
                    if(isset($request[$parent_model->field_name."_qty"]))
                        $quantity = intval($request[$parent_model->field_name."_qty"]);
                    else
                        $quantity = 1;
                    
                    $value = $tmp_l[$index] . " (" . $gopt->get_formatted_amount($tmp_v[$index]) . ") &times; $quantity";
                }
                break;
        }
        
        $data = new stdClass;
        $data->field_id = $parent_model->get_field_id();
        $data->type = 'Price';
        $data->label = $parent_model->get_field_label();
        $data->value = $value;
        return $data;
    }
    
    //Returns details of the price fields submitted by user in an stdClass object containing following fields..
    // billing ==> it contains an array of individual objects holding product name, price and quantity in 'label', 'price' and 'qty' fields respectively.
    // total ==> total amount for that particular field as submitted by user after multiplying with quantity (and adding all user selected prices in case of multi-select field). (It is NOT the total amount for all the price fields in a form).
    
    public function get_pricing_detail($request,$parent_model)
    {
        $paypal_field = new RM_PayPal_Fields();
        $res = $paypal_field->load_from_db($parent_model->pp_field_id);

        if (!$res)
            return null;
        
        $total_price = 0.0;
        $billing = array();
        
        switch ($paypal_field->get_type())
        {
            case "fixed":
                if(!isset($request[$parent_model->field_name])) {
                    break;
                }

                if(isset($request[$parent_model->field_name."_qty"]) && intval($request[$parent_model->field_name."_qty"]) > -1)
                    $quantity = intval($request[$parent_model->field_name."_qty"]);
                else
                    $quantity = 1;
                
                $price = floatval($paypal_field->get_value());
                $total_price = $price * $quantity;
                $tmp_billing = (object)array('label'=>$paypal_field->get_name(), 'price'=>$price, 'qty' => $quantity);
                $billing[] = apply_filters('rm_field_product_billing_'.$parent_model->pp_field_id, $tmp_billing);
                //$billing[] = (object)array('label'=>$paypal_field->get_name(), 'price'=>$price, 'qty' => $quantity);;
                break;
                
            case "userdef":
                if (!isset($request[$parent_model->field_name]) || $request[$parent_model->field_name] == "")
                    break;                    
                $total_price = floatval($request[$parent_model->field_name]);   
                $total_price = round($total_price, 2);
                $billing[] = (object)array('label'=>$paypal_field->get_name(), 'price'=>$total_price, 'qty' => 1);           
                break;
                
            case "multisel":
                $tmp_v = maybe_unserialize($paypal_field->get_option_price());
                $tmp_l = maybe_unserialize($paypal_field->get_option_label());
                $gopt = new RM_Options;
                
                $value = isset($request[$parent_model->field_name]) ? $request[$parent_model->field_name] : null;
                
                if ($value)
                {
                    foreach ($value as $pf_single_val)
                    {
                        $index = (int) substr($pf_single_val, 1);
                        if (!isset($tmp_v[$index]))
                            continue;
                        
                        if(isset($request[$parent_model->field_name."_qty"], $request[$parent_model->field_name."_qty"][$pf_single_val]) && intval($request[$parent_model->field_name."_qty"][$pf_single_val]) > -1)
                            $quantity = intval($request[$parent_model->field_name."_qty"][$pf_single_val]);
                        else
                            $quantity = 1;
                        
                        $total_price += floatval($tmp_v[$index]) * $quantity;
                        $billing[] = (object)array('label'=>$tmp_l[$index], 'price'=>floatval($tmp_v[$index]),'qty' => $quantity);
                    }
                    
                }
                break;

            case "dropdown":
                $tmp_v = maybe_unserialize($paypal_field->get_option_price());
                $tmp_l = maybe_unserialize($paypal_field->get_option_label());
                $gopt = new RM_Options;
                $value = isset($request[$parent_model->field_name]) ? $request[$parent_model->field_name] : null;
                if ($value)
                {
                    $index = (int) substr($value, 1);
                    if (!isset($tmp_v[$index]))
                        break;
                    
                    if(isset($request[$parent_model->field_name."_qty"]) && intval($request[$parent_model->field_name."_qty"]) > -1)
                        $quantity = intval($request[$parent_model->field_name."_qty"]);
                    else
                        $quantity = 1;
                    
                    $total_price = floatval($tmp_v[$index]) * $quantity;                   
                    $billing[] = (object)array('label'=>$tmp_l[$index], 'price'=>floatval($tmp_v[$index]), 'qty' => $quantity);
                }
                break;
        }
        
         $value = (object)array('billing'=>$billing, 'total_price'=>$total_price);
         return $value;
    }

}
