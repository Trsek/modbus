<?php
define (MODRESET,          0x00);      // MASTER reset
define (MODUNLOCKUSER,     0x10);      // odemknuti uzivatelske sekce
define (MODUNLOCKMETROLOG, 0x11);      // odemknuti metrologicke sekce
define (MODCLEARDATA,      0x24);      // nulovani datoveho archivu
define (MODCLEARDAY,       0x25);      // nulovani denniho archivu
define (MODCLEARMONTH,     0x26);      // nulovani mesicniho archivu
define (MODCLEAREXTREM,    0x27);      // nulovani archivu extremu
define (MODCLEARBINAR,     0x28);      // nulovani binarniho archivu
define (MODINITSTATUS,     0x29);      // nulovani souctoveho statusu
define (MODSTARTTEST,      0x2A);      // spusteni testu pristroje
define (MODFTPKLIENT,      0x31);      // inicializace klienta ftp, nastavi pocatecni cas, ktery bude odeslan v pristi session

$force_coil_code =
array(
		MODRESET           => array('MASTER reset'),
		MODUNLOCKUSER      => array('odemknuti uzivatelske sekce'),
		MODUNLOCKMETROLOG  => array('odemknuti metrologicke sekce'),
		MODCLEARDATA       => array('nulovani datoveho archivu'),
		MODCLEARDAY        => array('nulovani denniho archivu'),
		MODCLEARMONTH      => array('nulovani mesicniho archivu'),
		MODCLEAREXTREM     => array('nulovani archivu extremu'),
		MODCLEARBINAR      => array('nulovani binarniho archivu'),
		MODINITSTATUS      => array('nulovani souctoveho statusu'),
		MODSTARTTEST       => array('spusteni testu pristroje'),
		MODFTPKLIENT       => array('inicializace klienta ftp, nastavi pocatecni cas, ktery bude odeslan v pristi session'),
 );
		

/********************************************************************
* @brief Human information about Force coil function
* @param $address - identification in dec format
*/
function analyze_force_coil(&$FRAME, $address)
{
	global $force_coil_code;

	if( substr_cut($FRAME, 2) == "FF00" )
	{
		if( !empty($force_coil_code[$address]))
			$answer[] = dechex($address) . 'h - '. $force_coil_code[$address][0];
		else
			$answer[] = dechex($address) . 'h - run action at address '. $address;
		
	}

	return $answer;
}

/*----------------------------------------------------------------------------*/
/* END OF FILE */
