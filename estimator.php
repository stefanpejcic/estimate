<?php

/** Template */
require_once('Template.php');

/**
* Estimator - Controller for calculating and displaying the form and report.
*
*/
class Estimator {

    /**
    * Stores the output to ultimately return to the screen.
    *
    * @var string
    */
    protected $_output = null;

    /**
    * Stores the Template Object.
    *
    * @var Template
    */
    protected $_tpl = null;

    /**
    * __construct
    *
    * 1) Instantiates the template.
    * 2) Directs requests to the appropriate function.
    *
    * @return void
    */
    public function __construct(){

        $this->_tpl = new Template();

        $action = $this->_getArg('action');

        switch ($action) {
            case 'estimate':
                $this->_estimate();
                break;
            case 'reset':
                $this->_reset();
                break;
            default:
                $this->_form();
            }

        }

        /**
        * _reset
        *
        * Resets the state of the form to the default.
        *
        * @return void
        */
        protected function _reset(){
            setcookie('Astuteo_Estimator', '', time(), '/');

            //$this->_form();
            header('Location: .');
        }

        /**
        * _estimate
        *
        * Generates the estimate report.
        *
        * 1) Formats the POST variables.
        * 2) Iterates through the values, calculting totals and building the report.
        * 3) If selected, saves the data to a cookie.
        *
        * @return void
        */
        protected function _estimate(){

            $params = $_POST;
            foreach($params as $key => $val){
                $params[$key] = stripslashes($val);
            }
			$params['page_title'] = 'Project Estimate: '.$params['title_field'];

            $this->_setOutput($this->_tpl->header($params));

            $count = 15;

            $total = 0;

            $this->_setOutput($this->_tpl->estimate_header($params));

            foreach($params as $key => $val){

                if(substr($key, 0, 11) == 'task_field_'){

                    $index = substr($key, -2);

                    if(substr($index, 0, 1) == '_'){

                        $index = substr($key, -1);
                    }

                    $params['qty_item_'.$index] = $this->_checkKey('qty_item_'.$index, $params);
                    $default_rate = $this->_checkKey('default_rate_field', $params);
                    $params['price_item_'.$index] = $this->_checkKey('price_item_'.$index, $params, $default_rate);

                    $qty = str_replace(',', '', $params['qty_item_'.$index]);
                    $price = str_replace(',', '', $params['price_item_'.$index]);

                    $params['fee_'.$index] = $qty * $price;

                    $total += $params['fee_'.$index];

                    if(strpos($params['fee_'.$index], '.' !== FALSE)){
                        $params['fee_'.$index] = number_format($params['fee_'.$index], 2, '.', ',');
                    }

                    $fee = number_format($params['fee_'.$index], 2, '.', ',');
                    if(strpos($fee, '.') !== FALSE){
                        if(substr($fee, -2) == '00'){
                            $fee = substr($fee, 0, -3);
                        }
                    }

                    $params_row = array(

                        'task_field' => $this->_checkKey('task_field_'.$index, $params),
                        'qty_item' => $this->_checkKey('qty_item_'.$index, $params, '0'),
                        'price_item' => $this->_checkKey('price_item_'.$index, $params, '0'),
                        'fee' => $fee,
                        'count' => $index
                    );

                    $this->_setOutput($this->_tpl->estimate_row($params_row));

                }
            }

            $total = number_format($total, 2, '.', ',');
            if(strpos($total, '.') !== FALSE){
                if(substr($total, -2) == '00'){
                    $total = substr($total, 0, -3);
                }
            }
            $params['total'] = $total;

            $this->_setOutput($this->_tpl->estimate_footer($params));

            $save = $this->_checkKey('save', $params);

            if($save){
                //expires in 1 year
                $cookie = $this->_implodeAssoc('|',$params);

                //$cookie = urlencode($cookie);
                setcookie('Astuteo_Estimator', $cookie, time()+60*60*24*30*12, '/');

            } else {
                setcookie('Astuteo_Estimator', '', time()+60*60*24*30*12, '/');

            }

            $this->_setOutput($this->_tpl->footer());


        }

        /**
        * _form
        *
        * Generates the form.
        *
        * 1) Tries to get values back from the cookie.
        * 2) Iterates through the values, building the form.
        *
        * @return void
        */
        protected function _form(){

            $this->_setOutput($this->_tpl->header(array('page_title' => 'Web Development Project Estimator')));

            $this->_setOutput($this->_tpl->form_header());

            $params = $this->_explodeAssoc('|',$this->_checkKey('Astuteo_Estimator', $_COOKIE));

            $params['title_field'] = $this->_checkKey('title_field', $params, 'Untitled Project');

            $params['default_rate_field'] = $this->_checkKey('default_rate_field', $params, '0');

            $params['currency_field'] = $this->_checkKey('currency_field', $params, '$');

            $params['currency_field_select'] = $this->_getCurrency($this->_checkKey('currency_field', $params));

            $this->_setOutput($this->_tpl->form_table_header($params));

            $tasks = $this->_getDefaultTaskNames();

            $class = 'alt';

            $count = 0;

            $row_count = 1;

            foreach($params as $key => $val){

                if(substr($key, 0, 11) == 'task_field_'){

                    $index = substr($key, -2);

                    if(substr($index, 0, 1) == '_'){

                        $index = substr($key, -1);
                    }

                    if(!$class) {
                        $class = 'alt';
                    } else {
                        $class = '';
                    }

                    $params_row = array(
                    'class' => $class,
                    'task_field' => $val,
                    'qty_item' => $this->_checkKey('qty_item_'.$index, $params, ''),
                    'price_item' => $this->_checkKey('price_item_'.$index, $params, ''),
                    'fee' => $this->_checkKey('fee_'.$index, $params, '0'),
                    'count' => $row_count
                    );

                    $this->_setOutput($this->_tpl->form_table_row($params_row));
                    $row_count++;

                }

            }

            if($row_count == 1){
                for($i = 1; $i < 11; $i++){

                    if(!$class) {
                        $class = 'alt';
                    } else {
                        $class = '';
                    }

                    $task_field = $tasks[$i];

                    $params_row = array(
                        'class' => $class,
                        'task_field' => $task_field,
                        'qty_item' => '0',
                        'price_item' => $params['default_rate_field'],
                        'fee' => '0',
                        'count' => $i
                    );

                    $this->_setOutput($this->_tpl->form_table_row($params_row));
                    $row_count++;
                }
            }

            $params['row_count'] = $row_count;

            if($this->_checkKey('save', $params)){ $params['save'] = 'checked="checked"'; } else { $params['save'] = ''; }

            $this->_setOutput($this->_tpl->form_table_footer($params));

            $this->_setOutput($this->_tpl->form_footer());

            $this->_setOutput($this->_tpl->footer());


        }

        /**
        * _getDefaultTaskNames
        *
        * Provides the default form task names, in the event that there is no cookie.
        *
        * 1) Tries to get values back from the cookie.
        * 2) Iterates through the values, building the form.
        *
        * @return array
        */
        protected function _getDefaultTaskNames(){

            return array(
        '',
        'Information Architecture',
        'Design Research',
        'Initial Drafts &amp; Sketches',
        'Design Revisions',
        'HTML+CSS Development',
        'Server-Side Development',
        'Testing &amp; Debugging',
        'Copywriting',
        'Photography',
        'Client Meetings'
            );
        }

        /**
        * _getCurrency
        *
        * Provides an option list for the current drop-down
        *
        * 1) Iterates through currency types.
        * 2) Sets the currently selected type as "selected".
        *
        * @param  string $selected - the currently selected currency type
        * @return string
        */
        protected function _getCurrency($selected){


            $arr = array('$', '&pound;', '&euro;', '&yen;' );
            $output = '';
            foreach($arr as $val){


                $output .= "<option value=\"$val\"";

                if($val == $selected){
                    $output .= " selected=\"selected\"";
                }

                $output .= ">$val</option>";

            }

            return $output;
        }

        /**
        * _setOutput
        *
        * Adds text to what will ultimately be outputted to the screen
        *
        *
        * @param  string $input
        * @return void
        */
        protected function _setOutput($input){
            $this->_output .= $input;
        }

        /**
        * getOutput
        *
        * Returns the current state of the output variable.
        *
        * @return string
        */
        public function getOutput(){
            print $this->_output;
        }

        /**
         * _checkKey
         *
         * Avoids the php warning that is thrown if an array key does not exist
         *
         * @param string $key
         * @param array $arr
         * @param string $default_val
         * @return var
         */
        protected function _checkKey($key, $arr, $default_val = null){

            if(is_array($arr)){
                if(array_key_exists($key, $arr) && $arr[$key]){

                    return $arr[$key];
                } else {
                    return $default_val;
                }
            }
        }

        /**
         * _getArg
         *
         * Tries to return a value from the request parameters.
         *
         * @param string $key
         * @param array $arr
         * @param string $default_val
         * @return var
         */
        protected function _getArg($key){

            $val = $this->_checkKey($key, $_POST);
            return $val;
        }

        /**
        * _implodeAssoc($glue,$arr)
        *
        * Makes a string from an assiciative array
        *
        * @param string $glue - the string to glue the parts of the array with
        * @param array $arr - array to implode
        */
        protected function _implodeAssoc($glue,$arr)
        {
            foreach($arr as $key => $val){
                $arr[$key] = urlencode($val);
            }

            $keys=array_keys($arr);
            $values=array_values($arr);

            return(implode($glue,$keys).$glue.implode($glue,$values));
        }

        /**
        * _explodeAssoc($glue,$arr)
        *
        * Makes an assiciative array from a string
        * @param $glue - the string to glue the parts of the array with
        * @param $arr - array to explode
        */
        protected function _explodeAssoc($glue,$str)
        {
            $arr=explode($glue,$str);

            $size=count($arr);

            for ($i=0; $i < $size/2; $i++)
            $out[$arr[$i]]=urldecode($arr[$i+($size/2)]);

            return($out);
        }
    }

