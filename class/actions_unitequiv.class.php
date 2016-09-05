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
 * \file    class/actions_unitequiv.class.php
 * \ingroup unitequiv
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class ActionsUnitEquiv
 */
class ActionsUnitEquiv
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function formObjectLineOptions($parameters, &$object, &$action, $hookmanager)
	{
		
		if (in_array('supplierorderdispatch', explode(':', $parameters['context'])))
		{
		 		$suffix = &$parameters['suffix'];
				global $db,$langs;
				$prod=new Product($db);
				if($prod->fetch($object->fk_product)>0) {

					$weight = $prod->weight * pow(10, $prod->weight_units);
					$length = $prod->length * pow(10, $prod->length_units);
					$surface = $prod->surface * pow(10, $prod->surface_units);
					$volume = $prod->volume * pow(10, $prod->volume_units);

					$unite_achat = 'weight';
					
					?>
					<script type="text/javascript">
					$(document).ready(function() {
					<?php
					if($unite_achat == 'weight') { 
					?>	
						$qty = $('input[name="qty<?php echo $suffix; ?>"]');
						<?php
						
					
						if(!empty($surface)) {
							echo '$qty.after(\'<br />  '.$langs->trans('Surface').' : <input id="qty_surface'.$suffix.'" type="text" value="" size="8" name="qty_surface'.$suffix.'" />\');';
						}	
					
						?>
						$qty.change(function() {
							$qty_surface = $('input[name="qty_surface<?php echo $suffix; ?>"]');
							if($qty_surface.length>0) {
								var weight = $(this).val();
								var new_value = <?php echo $surface / $weight ?> * weight;
								
								$qty_surface.val(new_value);
								
							}
						});
						
						$('input[name="qty_surface<?php echo $suffix; ?>"]').change(function() {
							var surface = $(this).val();
							var new_value = surface / <?php echo $surface / $weight ?>;
							
							$qty.val( new_value );
						});
						
						$qty.change();
						
					<?php } ?>
					});

					</script>
					<?php
					
				}
				
				
		}

		
	}
}