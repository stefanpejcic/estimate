<?php

/**
* Template - Class that functions as a template
 *
 * This is a very simple templating system. Every function is a template block.
 * The head of the block is as follows:
 *

  	public function <function name>($params = array()){foreach($params as $key => $val){$$key = $val;}$output = <<<EOT

 *
 *  You must replace <function name> with a valid PHP 5 function name.
 *
 * The foot of the block is as follows:
 *

EOT;
 		return $output;}

 *
 * EOT must be alone on its own line.
 *
 * If you follow this format, you can place any text you want between the head and foot and it will be interpreted literally, with one important exception.
 * This exception is that any key/value pairs that are passed to the function in an array will be available as regular PHP substitution variables.
 *
 * Say you have the following function included in this Template class:
 *
 *

    public function my_template_block($params = array()){foreach($params as $key => $val){$$key = $val;}$output = <<<EOT

        Hi, I <strong>really</strong> love the $football_team.
EOT;
		return $output;}

 *
 * From another class, you can then do the following:
 *
 * 1) include this Template:

require_once('Template.php');

 * 2) Start your class and instantiate the Template class:
 *

class Controller {
    function callTemplate(){
        $tpl = new Template();

 * 3) Call the template block and pass it an array:
 *

    $output = $tpl->my_template_block(array('football_team' => 'Packers'));

 * 4) Print it to the screen:

    print $output;

 *
 */

class Template{
		
	public function header($params = array()){foreach($params as $key => $val){$$key = $val;}$output = <<<EOT

<!doctype html>
<html lang="en">
    <head>
        <meta charset=utf-8"/>
        <title>Web Development Project Estimator</title>
        <link rel="stylesheet" type="text/css" media="screen" href="./estimator.css" />
		<link rel="stylesheet" type="text/css" media="print" href="./estimator-print.css" />

        <script src="./estimator.js" type="text/javascript"></script>


    </head>

EOT;
		return $output;}
	
	public function form_header($params = array()){foreach($params as $key => $val){$$key = $val;}$output = <<<EOT

        <body onload="estimator_init()">
        
				<div class="estimator">

EOT;
        return $output;}

    public function form_table_header($params = array()){foreach($params as $key => $val){$$key = $val;}$output = <<<EOT

                    <form action="index.php" method="post" target="_blank" id="estimator_form">
                        <input type="hidden" name="action" value="estimate" />
                        <table id="estimator_table" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th colspan="5" class="project-title">Project Title:</th>
                                    <th colspan="1" class="rate">Default Rate:</th>
                                </tr>

                                <tr>
                                    <td colspan ="5" class="project-title big">
                                        
                                            <input type="text" id="title_field" name="title_field" value="$title_field"/>
                                            <span class="title" id="title_label" style="display:none">$title_field</span>
                                            <a href="#" id="title_edit" onclick="estimator_edit_title()" style="display:none">Edit</a>
                                            <a href="#" id="title_save" onclick="estimator_save_title()" style="display:none">Save</a>
                                    </td>
                                    <td colspan="1" class="rate big">

                                        <select id="currency_field" name="currency_field">$currency_field_select</select>
                                        <input type="text" id="default_rate_field" name="default_rate_field" value="$default_rate_field"/>
                                        <span id="currency_label" style="display:none" class="currency">$</span> <span id="default_rate_label" style="display:none">$default_rate_field</span>
                                        <a href="#" id="default_rate_edit" onclick="estimator_edit_default_rate()" style="display:none">Edit</a>
                                        <a href="#" id="default_rate_save" onclick="estimator_save_default_rate()" style="display:none">Save</a>

                                    </td>
                                </tr>
                                <tr class="col-heads">
                                    <th class="left">Description of Task</th>
                                    <th>Hours</th>

                                    <th class="operator">&nbsp;</th>
                                    <th>Rate</th>
                                    <th class="operator">&nbsp;</th>
                                    <th class="right">Estimated Fee</th>
                                </tr>
                            </thead>
                            <tbody id="estimator_tbody">

                                <tr style="display:none" class="" id="clone_row">
                                    <td class="left">

                                        <input type="text" class="task_field" id="clone_field" name="clone_field" value="New Task"/>
                                        <span id="clone_label" style="display:none">New Task</span>
                                        <a href="#" id="clone_edit" style="display:none">Edit</a>
                                        <a href="#" id="clone_delete" style="display:none">Delete</a>
                                        <a href="#" id="clone_save" style="display:none">Save</a>
                                    </td>

                                    <td><input type="text" class="qty_field" name="clone_qty" id="clone_qty" value="" size="3" onchange="estimator_calc()"/></td>
                                    <td class="operator">&times;</td>
                                    <td><input type="text" class="price_item_field" name="clone_price" id="clone_price" value="" size="3" onchange="estimator_calc()"/></td>
                                    <td class="operator">=</td>
                                    <td class="right" id="clone_total"></td>
                                </tr>
EOT;
		return $output;}

    public function form_table_row($params = array()){foreach($params as $key => $val){$$key = $val;}$output = <<<EOT

                                <tr class="$class" id="task_row_$count">
                                    <td class="left">

                                        <input type="text" class="task_field" id="task_field_$count" name="task_field_$count" value="$task_field"/>
                                        <span id="task_label_$count" style="display:none">$task_field</span>
                                        <a href="#" id="task_edit_$count" style="display:none">Edit</a>
                                        <a href="#" id="task_delete_$count" style="display:none">Delete</a>
                                        <a href="#" id="task_save_$count" style="display:none">Save</a>
                                    </td>

                                    <td><input type="text" class="qty_field" name="qty_item_$count" id="qty_item_$count" value="$qty_item" size="3" onchange="estimator_calc()"/></td>
                                    <td class="operator">&times;</td>
                                    <td><input type="text" class="price_item_field" name="price_item_$count" id="price_item_$count" value="$price_item" size="3" onchange="estimator_calc()"/></td>
                                    <td class="operator">=</td>
                                    <td class="right" id="total_item_$count">$fee</td>
                                </tr>

EOT;
		return $output;}

    public function form_table_footer($params = array()){foreach($params as $key => $val){$$key = $val;}$output = <<<EOT

							</tbody>

						</table>
						
						<div class="estimator-footer">
						
							<div id="tf_buttons">
								<span id="add_task_button" style="display:none;">
									<a href="#" id="add_task_link" onclick="estimator_add_task();return false;">Add a New Task</a>
								</span>
								<a href="#" id="reset_link" onclick="estimator_reset();return false;">Reset Form</a>
							</div>
							
							<div id="grandTotal">
								<span id="currency_total">$currency_field</span><span id="total">$total</span>
							</div>
							
						</div>

                        <label for="save_changes">Remember Changes: <input type="checkbox" id="save_changes" name="save" value="checked" $save /></label>
                        <input type="submit" id="form_submit" value="View Estimate in Print-Ready Format"/>
                        <a href="#" id="form_link" onclick="estimator_submit();return false;" style="display:none">View Estimate in Print-Ready Format</a>
                        <input type="hidden" name="row_count" id="estimator_row_count" value="$row_count"/>
                    </form>
                    
					<form action="index.php" method="post" id="estimator_reset">
                    	<input type="hidden" name="action" value="reset" />
                        <input type="submit" value="Reset Form" id="reset_submit"/>
                    </form>
EOT;
		return $output;}

	public function form_footer($params = array()){foreach($params as $key => $val){$$key = $val;}$output = <<<EOT

				</div> <!-- end estimate-form -->
			
        <iframe id="form_target" name="form_target" src="#" style="width:0;height:0;border:0px solid #fff;display:none"></iframe>
EOT;
		return $output;}

    public function estimate_header($params = array()){foreach($params as $key => $val){$$key = $val;}$output = <<<EOT
    <body class="ep-body">
        <div class="estimator">
			<table id="estimator-table" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
					    <th colspan="5" class="project-title">Project Title:</th>
					    <th colspan="1" class="rate">Default Rate:</th>
					</tr>
					<tr>
					    <td colspan ="5" class="project-title big"><span class="title">$title_field</span></td>
					    <td colspan="1" class="rate big"><span class="currency">$currency_field
					    </span> <span class="default_rate">$default_rate_field</span></td>
					</tr>
					<tr class="col-heads">
					    <th class="left">Description of Task</th>
					    <th>Hours</th>

					    <th class="operator">&nbsp;</th>
					    <th>Rate</th>
					    <th class="operator">&nbsp;</th>
					    <th class="right">Estimated Fee</th>
					</tr>
				</thead>
			<tbody>

EOT;
		return $output;}

    public function estimate_row($params = array()){foreach($params as $key => $val){$$key = $val;}$output = <<<EOT

                                <tr>
                                    <td class="left"><span class="task">$task_field</span></td>

                                    <td>$qty_item</td>
                                    <td class="operator">x</td>
                                    <td>$price_item</td>
                                    <td class="operator">=</td>
                                    <td class="right" id="total_item_$count">$fee</td>
                                </tr>


EOT;
		return $output;}

    public function estimate_footer($params = array()){foreach($params as $key => $val){$$key = $val;}$output = <<<EOT

                                </tbody>

							</table>

							<div class="estimator-footer">

								<div id="grandTotal">
									<span id="currency_total">$currency_field</span><span id="total">$total</span>
								</div>

							</div>

EOT;
		return $output;}

	public function footer($params = array()){foreach($params as $key => $val){$$key = $val;}$output = <<<EOT

    </body>

    
</html>

EOT;
		return $output;}	
}
