<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file		lib/unitequiv.lib.php
 *	\ingroup	unitequiv
 *	\brief		This file is an example module library
 *				Put some comments here
 */

function unitequivAdminPrepareHead()
{
    global $langs, $conf;

    $langs->load("unitequiv@unitequiv");

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/unitequiv/admin/unitequiv_setup.php", 1);
    $head[$h][1] = $langs->trans("Parameters");
    $head[$h][2] = 'settings';
    $h++;
    $head[$h][0] = dol_buildpath("/unitequiv/admin/unitequiv_about.php", 1);
    $head[$h][1] = $langs->trans("About");
    $head[$h][2] = 'about';
    $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    //$this->tabs = array(
    //	'entity:+tabname:Title:@unitequiv:/unitequiv/mypage.php?id=__ID__'
    //); // to add new tab
    //$this->tabs = array(
    //	'entity:-tabname:Title:@unitequiv:/unitequiv/mypage.php?id=__ID__'
    //); // to remove a tab
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'unitequiv');

    return $head;
}

function addJSunitEquiv($fk_product, $qty_input_selector, $executiononload=true, $type = 'stock') {
	
	global $db,$langs,$conf,$user;
	
	dol_include_once('/product/class/product.class.php');
	
	$prod=new Product($db);
	if($prod->fetch($fk_product)>0) {

		$weight = $prod->weight * pow(10, $prod->weight_units);
		$length = $prod->length * pow(10, $prod->length_units);
		$surface = $prod->surface * pow(10, $prod->surface_units);
		$volume = $prod->volume * pow(10, $prod->volume_units);

		$unite_achat = !empty( $prod->array_options['options_unite_'.$type] ) ? $prod->array_options['options_unite_'.$type] :  'unite';
		
		
		$div_id_inputs = 'inputs'.md5($qty_input_selector);
		?>
		<script type="text/javascript">
		<?php
		
		if($executiononload) {
			?>
			$(document).ready(function() {
				setUnitEquivInputs();
			});
			<?php
		}		
		?>
		function setUnitEquivInputs() {
		
			$qty = $('<?php echo $qty_input_selector; ?>');
			
			var coef_qty_to_weight = 1;
			var coef_qty_to_surface = 1;
			var coef_qty_to_volume = 1;
			var coef_qty_to_length = 1;
			
			<?php
			if($unite_achat == 'weight' && $weight>0) {
				echo 'coef_qty_to_surface = '.($surface / $weight).';';		
				echo 'coef_qty_to_volume = '.($volume / $weight).';';		
				echo 'coef_qty_to_length = '.($length / $weight).';';		
			}
			elseif($unite_achat == 'volume' && $volume >0) { 
				echo 'coef_qty_to_weight = '.($weight / $volume).';';
				echo 'coef_qty_to_surface = '.($surface / $volume).';';		
				echo 'coef_qty_to_length = '.($length / $volume).';';		
			}
			 
			elseif($unite_achat == 'surface' && $surface >0) { 
				echo 'coef_qty_to_weight = '.($weight / $surface).';';
				echo 'coef_qty_to_volume = '.($volume / $surface).';';		
				echo 'coef_qty_to_length = '.($length / $surface).';';		
			}
			 
			elseif(($unite_achat == 'size' || $unite_achat == 'length') && $length >0) { 
				echo 'coef_qty_to_surface = '.($surface / $length).';';		
				echo 'coef_qty_to_weight = '.($weight / $length).';';
				echo 'coef_qty_to_volume = '.($volume / $length).';';		
			}
			 
			elseif($unite_achat == 'unite') { 
				echo 'coef_qty_to_weight = '.($weight).';';
				echo 'coef_qty_to_volume = '.($volume).';';		
				echo 'coef_qty_to_surface = '.($surface).';';		
				echo 'coef_qty_to_length = '.($length).';';		
			}
			 
			?>	
			$("#<?php echo $div_id_inputs ?>").remove();
			$qty.after('<div id="<?php echo $div_id_inputs ?>" style="white-space:nowrap;"></div>');
			
			if(coef_qty_to_weight > 0 && coef_qty_to_weight!=1)$("#<?php echo $div_id_inputs ?>").append('<div><?php echo $langs->trans('Weight') ?> : <input id="qty_weight" type="text" value="" size="8" name="qty_weight" /></div>');
			if(coef_qty_to_surface > 0 && coef_qty_to_surface!=1) $("#<?php echo $div_id_inputs ?>").append('<div><?php echo $langs->trans('Surface') ?> : <input id="qty_surface" type="text" value="" size="8" name="qty_surface" /></div>');
			if(coef_qty_to_volume > 0 && coef_qty_to_volume!=1) $("#<?php echo $div_id_inputs ?>").append('<div><?php echo $langs->trans('Volume') ?> : <input id="qty_volume" type="text" value="" size="8" name="qty_volume" /></div>');
			if(coef_qty_to_length > 0 && coef_qty_to_length!=1) $("#<?php echo $div_id_inputs ?>").append('<div><?php echo $langs->trans('Length') ?> : <input id="qty_length" type="text" value="" size="8" name="qty_length" /></div>');
				
			$qty.change(function() {
				$qty_surface = $('input[name="qty_surface"]');
				if($qty_surface.length>0) {
					var new_value = coef_qty_to_surface * $(this).val();
					
					$qty_surface.val(new_value);
					
				}
				
				$qty_volume = $('input[name="qty_volume"]');
				if($qty_volume.length>0) {
					var new_value = coef_qty_to_volume * $(this).val();
					
					$qty_volume.val(new_value);
					
				}
				
				$qty_weight = $('input[name="qty_weight"]');
				if($qty_weight.length>0) {
					var new_value = coef_qty_to_weight * $(this).val();
					
					$qty_weight.val(new_value);
					
				}
				
				
				$qty_length = $('input[name="qty_length"]');
				if($qty_length.length>0) {
					var new_value = coef_qty_to_length * $(this).val();
					
					$qty_length.val(new_value);
					
				}
				
			});
			
			$('input[name="qty_surface"]').change(function() {
				var new_value = $(this).val()/ coef_qty_to_surface;
				
				$qty.val( new_value );
				$qty.change();
			});
			
			$('input[name="qty_volume"]').change(function() {
				var new_value = $(this).val() / coef_qty_to_volume;
				
				$qty.val( new_value );
				$qty.change();
			});
			
			
			$('input[name="qty_weight"]').change(function() {
				var new_value = $(this).val() / coef_qty_to_weight;
				
				$qty.val( new_value );
				$qty.change();
			});
			
			
			$('input[name="qty_length"]').change(function() {
				var new_value = $(this).val() / coef_qty_to_length;
				
				$qty.val( new_value );
				$qty.change();
			});
			
			$qty.change();
			
		
		}

		</script>
		<?php
		
	}
	
}
