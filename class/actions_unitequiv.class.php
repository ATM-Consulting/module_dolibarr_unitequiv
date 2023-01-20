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
				
				dol_include_once('/unitequiv/lib/unitequiv.lib.php');
				addJSunitEquiv($object->fk_product, 'input[name="qty'.$suffix.'"]');
				
				
				
		}

		
	}
	
	function printObjectLine($parameters, &$object, &$action, $hookmanager) {
		if ($action == 'editline' && in_array('propalcard', explode(':', $parameters['context'])))
		{
		
			$id_line = $parameters['selected'];
			$line = & $parameters['line'];
		
			if($line->id == $id_line && $line->fk_product>0) {
				
				dol_include_once('/unitequiv/lib/unitequiv.lib.php');
				addJSunitEquiv($line->fk_product, '#qty');
				
			}
			
				
			
		}
	}
	
	function formAddObjectLine($parameters, &$object, &$action, $hookmanager) {
		
				
		if (in_array('propalcard', explode(':', $parameters['context'])))
		{
            dol_include_once('/unitequiv/lib/unitequiv.lib.php');
			?>
			<script type="text/javascript">
			$(document).ready(function() {
				
				$('body').append('<div id="unitequivscriptexecution" style="display:none;"></div>');
				
				$("#idprod").change(function() {
					
					var fk_product = $(this).val();
					
					$.ajax({
						url:"<?php echo dol_buildpath('/unitequiv/script/interface.php',1) ?>"
						,data:{
							"get":"inputs"
							,"type":"vente"
							,"fk_product":fk_product
							,"field":"#qty"
						}
						,dataType:"html"
					}).done(function(data) {
						$("#unitequivscriptexecution").html(data);
						setUnitEquivInputs();
					});
					
				});
			});
			</script>
			<?php
		}
	}
}