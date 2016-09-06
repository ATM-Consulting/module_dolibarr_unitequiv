<?php
	require '../config.php';
	
	$get = GETPOST('get');
	
	$langs->load('unitequiv@unitequiv');
	
	switch ($get) {
		case 'inputs':
			
			dol_include_once('/unitequiv/lib/unitequiv.lib.php');
			addJSunitEquiv((int)GETPOST('fk_product'), GETPOST('field'), false);
				
			break;
		
		
	}
