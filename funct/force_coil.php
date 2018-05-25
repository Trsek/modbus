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
		MODUNLOCKUSER      => array('Odemknuti uzivatelske sekce'),
		MODUNLOCKMETROLOG  => array('Odemknuti metrologicke sekce'),
		MODCLEARDATA       => array('Nulovani datoveho archivu'),
		MODCLEARDAY        => array('Nulovani denniho archivu'),
		MODCLEARMONTH      => array('Nulovani mesicniho archivu'),
		MODCLEAREXTREM     => array('Nulovani archivu extremu'),
		MODCLEARBINAR      => array('Nulovani binarniho archivu'),
		MODINITSTATUS      => array('Nulovani souctoveho statusu'),
		MODSTARTTEST       => array('Spusteni testu pristroje'),
		MODFTPKLIENT       => array('Inicializace klienta ftp, nastavi pocatecni cas, ktery bude odeslan v pristi session'),
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
	    if( !empty($force_coil_code[hexdec($address)]))
	        $answer[] = 'FF00h - '. $force_coil_code[hexdec($address)][0];
		else
		    $answer[] = 'FF00h - Run action at address '. $address .'h';
		
	}

	return $answer;
}

/*----------------------------------------------------------------------------*/
/* END OF FILE */
