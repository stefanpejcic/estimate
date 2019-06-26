/**
 *The following code fixes one of Microsoft's little presents to the developer community:
 */
var isOpera, isIE = false;
if(typeof(window.opera) != 'undefined'){isOpera = true;}
if(!isOpera && navigator.userAgent.indexOf('Internet Explorer')){isIE = true;}

//fix both IE and Opera (adjust when they implement this method properly)
if(isOpera || isIE){
  document.nativeGetElementById = document.getElementById;
  //redefine it!
  document.getElementById = function(id){
    var elem = document.nativeGetElementById(id);
    if(elem){
      //verify it is a valid match!
      if(elem.attributes['id'] && elem.attributes['id'].value == id){
        //valid match!
        return elem;
      } else {
        //not a valid match!
        //the non-standard, document.all array has keys for all name'd, and id'd elements
        //start at one, because we know the first match, is wrong!
        for(var i=1;i<document.all[id].length;i++){
          if(document.all[id][i].attributes['id'] && document.all[id][i].attributes['id'].value == id){
            return document.all[id][i];
          }
        }
      }
    }
    return null;
  };
}
/**Micro-unFUBAR done

/**
 * Global variable to track the index of the next row to be added.
 */
var estimator_next_row_index = 15;

/**
 * Switches a Task label to an input box.
 */
function estimator_edit_task(i){

    document.getElementById('task_field_' + i).style.display = 'inline';
    document.getElementById('task_label_' + i).style.display = 'none';
    document.getElementById('task_edit_' + i).style.display = 'none';
    document.getElementById('task_delete_' + i).style.display = 'none';
    document.getElementById('task_save_' + i).style.display = '';

}

/**
 * Removes a Task Row.
 */
function estimator_delete_task(i){

    var tbody_element, j;

    tbody_element = document.getElementById('estimator_tbody');

    for(j = 0; j<tbody_element.rows.length; j++){

        if(tbody_element.rows.item(j).id == 'task_row_' + i){
            tbody_element.deleteRow(j);
            break;
        }

    }

    estimator_calc(-1, 1);

}

/**
 * Switches a Task Row input box to a label.
 */
function estimator_save_task(i){

    var element_label, element_field;

    element_label = document.getElementById('task_label_' + i);

    element_field = document.getElementById('task_field_' + i);

    element_field.style.display = 'none';

    element_label.innerHTML = element_field.value;

    element_label.style.display = 'inline';

    document.getElementById('task_edit_' + i).style.display = '';
    document.getElementById('task_delete_' + i).style.display = '';
    document.getElementById('task_save_' + i).style.display = 'none';

}

/**
 * Adds commas and proper decimals to caculated values.
 */
function estimator_format_currency(amount)
{
    var i, minus, s, delimiter, a, d, n, nn;

    amount = amount.toString();
    
    if(amount.indexOf('.') > -1){
        i = parseFloat(amount);
        if(isNaN(i)) {
            i = 0.00;
        }
        minus = '';
        if(i < 0) {
            minus = '-';
        }
        i = Math.abs(i);
        i = parseInt(((i + 0.005) * 100),0);
        i = i / 100;
        s = i.toString();
        if(s.indexOf('.') < 0) {
            s += '.00';
        }
        if(s.indexOf('.') == (s.length - 2)) {
            s += '0';
        }
        s = minus + s;
        amount = s;
    }

    delimiter = ","; // replace comma if desired
    a = amount.split('.',2);
    d = a[1];
    i = parseInt(a[0],0);
    
    if(isNaN(i)) {
        return '';
    }
    minus = '';
    if(i < 0) {
        minus = '-';
    }
    i = Math.abs(i);
    n = i.toString();
    a = [];
    
    while(n.length > 3)
    {
        nn = n.substr(n.length-3);
        a.unshift(nn);
        n = n.substr(0,n.length-3);
    }
        
    if(n.length > 0) {
        a.unshift(n);
    }
    
    n = a.join(delimiter);

    if(!d ){
        if(n){

            amount = n;
        }
    } else if(d.length < 1) {
        amount = n;
    } else {
        amount = n + '.' + d;
    }

    amount = minus + amount;
    
    return amount;
}

/**
 * Iterates through the current Task Rows, reorganizing them and calculating values.
 */
function estimator_calc(old_rate, skip_save){
 
    var element, children, default_rate, total, count, alt, child, id, index, element_qty, element_price, price, qty, subtotal, element_task_field, element_edit, element_delete, element_save, i, estimator_next_row;

    element = document.getElementById('estimator_tbody');

    children = element.getElementsByTagName('*');

    default_rate = document.getElementById('default_rate_field').value;

    total = 0;

    count = 1;

    alt = 'alt';

    for (i = 0; i < children.length; i++) {

        child = children[i];

        id = child.getAttribute("id");

        if(id){

            if(id.indexOf('task_row_') === 0){

                if(alt === ''){
                    alt = 'alt';
                } else {
                    alt = '';
                }
                child.className = alt;

                child.id = 'task_row_' + count;

                index=id.charAt(id.length-1);

                if(id.charAt(id.length-2) != '_'){
                    index = id.charAt(id.length-2) + index;
                }
      
                element_qty = document.getElementById('qty_item_' + index);

                if(!element_qty.value){
                    element_qty.value = '0';
                }

                element_price = document.getElementById('price_item_' + index);

                if(!element_price.value){

                    element_price.value = '0';
                }

                if(element_price.value == old_rate){

                    element_price.value = default_rate;
                }

                price=element_price.value.replace(/,/g,'');
     
                qty=element_qty.value.replace(/,/g,'');

				subtotal = parseFloat(price) * parseFloat(qty);
                
                total += subtotal;

                subtotal = estimator_format_currency(subtotal);

                document.getElementById('total_item_' + index).innerHTML = subtotal;

                element_qty.id = 'qty_item_' + count;
                element_qty.name = 'qty_item_' + count;
                element_price.id = 'price_item_' + count;
                element_price.name = 'price_item_' + count;

                element_task_field = document.getElementById('task_field_' + index);
                element_task_field.id = 'task_field_' + count;
                element_task_field.name = 'task_field_' + count;

                document.getElementById('total_item_' + index).id = 'total_item_' + count;
                document.getElementById('task_label_' + index).id = 'task_label_' + count;

                element_edit = document.getElementById('task_edit_' + index);
                element_edit.id = 'task_edit_' + count;
                element_edit.index = count;
                element_edit.onclick = function(){
                    estimator_edit_task(this.index);
                    return false;
                };

                element_delete = document.getElementById('task_delete_' + index);
                element_delete.id = 'task_delete_' + count;
                element_delete.index = count;
                element_delete.onclick = function(){
                    estimator_delete_task(this.index);
                    return false;
                };

                element_save = document.getElementById('task_save_' + index);
                element_save.id = 'task_save_' + count;
                element_save.index = count;
                element_save.onclick = function(){
                    estimator_save_task(this.index);
                    return false;
                };

                if(!skip_save){
                    estimator_save_task(count);
                }
                count++;


            }
        }

        estimator_next_row = count;

    }

    total += '';

    total = estimator_format_currency(total);

    document.getElementById('total').innerHTML = total;

    document.getElementById('currency_total').innerHTML = document.getElementById('currency_label').innerHTML;

}

/**
 * Called when the page is loaded to override the values that are in place for non-javascript clients.
 */
function estimator_init() {
  
    document.getElementById('estimator_form').target = 'form_target';

    document.getElementById('add_task_button').style.display = 'inline';

    document.getElementById('title_field').style.display = 'none';
    document.getElementById('title_label').style.display = 'inline';
    document.getElementById('title_edit').style.display = '';

    document.getElementById('default_rate_field').style.display = 'none';
    document.getElementById('currency_field').style.display = 'none';
    document.getElementById('default_rate_label').style.display = 'inline';
    document.getElementById('default_rate_edit').style.display = '';
    document.getElementById('currency_label').style.display = 'inline';

    document.getElementById('form_submit').style.display = 'none';
    document.getElementById('form_link').style.display = '';

    document.getElementById('reset_submit').style.display = 'none';
    document.getElementById('reset_link').style.display = '';

    estimator_next_row_index = parseInt(document.getElementById('estimator_row_count').value,0);

    estimator_calc();


}

/**
 * Switches the Title label to an input box.
 */
function estimator_edit_title(){

    document.getElementById('title_field').style.display = 'inline';
    document.getElementById('title_label').style.display = 'none';
    document.getElementById('title_edit').style.display = 'none';
    document.getElementById('title_save').style.display = '';

}

/**
 * Switches the Title input box to a label.
 */
function estimator_save_title(){

    var element_label, element_field;
    element_label = document.getElementById('title_label');

    element_field = document.getElementById('title_field');

    element_field.style.display = 'none';

    element_label.innerHTML = element_field.value;

    element_label.style.display = 'inline';

    document.getElementById('title_edit').style.display = '';
    document.getElementById('title_save').style.display = 'none';
}

/**
 * Switches the Default Rate label to an input box.
 */
function estimator_edit_default_rate(){

    document.getElementById('default_rate_field').style.display = 'inline';
    document.getElementById('currency_field').style.display = 'inline';
    document.getElementById('default_rate_label').style.display = 'none';
    document.getElementById('default_rate_edit').style.display = 'none';
    document.getElementById('currency_label').style.display = 'none';
    document.getElementById('default_rate_save').style.display = '';

}

/**
 * Switches the Default Rate input box to a label.
 */
function estimator_save_default_rate(){

    var element_label, element_field, index, old_rate, val;

    element_label = document.getElementById('default_rate_label');

    element_field = document.getElementById('default_rate_field');

    old_rate = element_label.innerHTML;

    element_label.innerHTML = element_field.value;

    element_field.style.display = 'none';

    element_label.style.display = 'inline';

    element_label = document.getElementById('currency_label');

    element_field = document.getElementById('currency_field');
    
    index = element_field.selectedIndex;

    if(index > -1){
        val = element_field.options[index].value;
    }

    element_label.innerHTML = val;

    element_label.style.display = 'inline';

    element_field.style.display = 'none';

    document.getElementById('default_rate_edit').style.display = '';
    document.getElementById('currency_label').style.display = 'inline';
    document.getElementById('default_rate_save').style.display = 'none';

    //update blank prices, subtotals, total, including currency
    document.getElementById('currency_total').innerHTML = document.getElementById('currency_label').innerHTML;

    estimator_calc(old_rate);
}

/**
 * Adds a Task Row.
 */
function estimator_add_task(){

    var next_row_index, clone_row, clone, children, i, element, id, element_tbody;

    /*global estimator_next_row_index */
    next_row_index = estimator_next_row_index;

    clone_row = document.getElementById('clone_row');

    clone = clone_row.cloneNode(true);

    clone.id = 'task_row_' + next_row_index;
    clone.style.display = '';
    
    children = clone.getElementsByTagName('*');
    for (i = 0; i < children.length; i++) {

        element = children[i];

        id = element.getAttribute("id");
        
        if(id == 'clone_field'){

            element.id = 'task_field_' + next_row_index;

            element.setAttribute("name", 'task_field_' + next_row_index);

            element.value = 'New Task';
           
            element.style.display = 'inline';

        } else if(id == 'clone_label'){

            element.id = 'task_label_' + next_row_index;

            element.innerHTML = 'New Task';

            element.style.display = 'none';

        } else if(id == 'clone_edit'){

            element.id = 'task_edit_' + next_row_index;

            element.setAttribute("id", 'task_edit_' + next_row_index);

            element.style.display = 'none';

        } else if(id == 'clone_delete'){

            element.id = 'task_delete_' + next_row_index;

            element.style.display = 'none';

        } else if(id == 'clone_save'){

            element.id = 'task_save_' + next_row_index;

            element.style.display = '';

        }  else if(id == 'clone_qty'){

            element.id = 'qty_item_' + next_row_index;

            element.name = 'qty_item_' + next_row_index;

        }  else if(id == 'clone_price'){

            element.id = 'price_item_' + next_row_index;

            element.name = 'price_item_' + next_row_index;

        }  else if(id == 'clone_total'){

            element.id = 'total_item_' + next_row_index;

        }

    }
    
    element_tbody = document.getElementById('estimator_tbody');

    element_tbody.appendChild(clone);

    estimator_calc();

    /*global estimator_next_row_index */
    estimator_next_row_index++;
    
}

/**
 * Submits the form.
 */
function estimator_submit(){
    
    document.getElementById('estimator_form').target = '_blank';
    document.getElementById('estimator_form').submit();
    document.getElementById('estimator_form').target = 'form_target';

}

/**
 * Resets the form.
 */
function estimator_reset(){

    var x = confirm('Are you sure you want to reset this form?');
    if(x){
        document.getElementById('estimator_reset').submit();
    }
}

/**
 * Provides a handy alternative to the "alert" function for ad hoc debugging.
 */
function estimator_debug(message){
    var x = confirm(message);
    if(!x){

        throw "exit";

    }

}