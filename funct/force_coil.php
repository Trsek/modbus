<?php
define ('MODRESET',          0x00);      // MASTER reset
define ('MODUNLOCKUSER',     0x10);      // odemknuti uzivatelske sekce
define ('MODUNLOCKMETROLOG', 0x11);      // odemknuti metrologicke sekce
define ('MODCLEARDATA',      0x24);      // nulovani datoveho archivu
define ('MODCLEARDAY',       0x25);      // nulovani denniho archivu
define ('MODCLEARMONTH',     0x26);      // nulovani mesicniho archivu
define ('MODCLEAREXTREM',    0x27);      // nulovani archivu extremu
define ('MODCLEARBINAR',     0x28);      // nulovani binarniho archivu
define ('MODINITSTATUS',     0x29);      // nulovani souctoveho statusu
define ('MODSTARTTEST',      0x2A);      // spusteni testu pristroje
define ('MODFTPKLIENT',      0x31);      // inicializace klienta ftp, nastavi pocatecni cas, ktery bude odeslan v pristi session

$force_coil_code =
array(
		MODRESET           => array('MASTER reset'),
        0x01               => array('Write EEPROM'),
        0x02               => array('Read EEPROM'),
        0x03               => array('Stop measurement'),
        0x04               => array('Start single measurement'),
        0x05               => array('Start kontinuálního měření'),
        0x06               => array('Modul reset'),
        0x07               => array('Synchro LED a normální režim'),
        0x08               => array('Synchro LED a úsporný režim'),
        0x09               => array('Refresh  timeoutu TIMEOUT_RUN'),
        0x0A               => array('Vypnutí zdroje modemu se zpožděním'),
        0x0B               => array('Vypnutí zdroje modemu bez zpoždění'),
        0x0C               => array('Vymazání statusového slova – bity e_RECOVERY, e_WDG, e_RUN'),
        0x0D               => array('Zap / vyp interface pro přepočítávač'),
        0x0E               => array('Zapnutí / vypnutí diody LED u SD karty'),
        0x0F               => array('1 .. stav přístroje  ERROR, 0 .. stav přístroje OK (varovné blikání Status LED)'),
        MODUNLOCKUSER      => array('Odemknutí uživatelské sekce'),
		MODUNLOCKMETROLOG  => array('Odemknutí servisní sekce'),
        0x12               => array('Unlock Factory configuration section'),
        0x18               => array('Change user password'),
        0x19               => array('Change service password'),
        0x20               => array('Set / Reset INT signál'),
        0x21               => array('Napájení čidel na svorkách'),
        0x22               => array('Napájení čidel na konektoru Cannon'),
        0x23               => array('Nulováni skryte diagnostiky'),
        MODCLEARDATA       => array('Nulováni datového archivu'),
		MODCLEARDAY        => array('Nulováni denního archivu'),
		MODCLEARMONTH      => array('Nulováni mesičního archivu'),
		MODCLEAREXTREM     => array('Nulováni archívu extremu'),
		MODCLEARBINAR      => array('Nulováni binarního archivu'),
		MODINITSTATUS      => array('Nulováni součtového statusu'),
		MODSTARTTEST       => array('Spuštení testovacího režimu'),
        0x2B               => array('Zap / vyp napájení int. sběrnice'),
        0x2C               => array('Zapnutí modemu'),
        0x2D               => array('Zapnuti analogové části (miniElcor)'),
        0x2E               => array('Čtení encoderu'),
        0x2F               => array('Rízení spotreby modemu (Sleep)'),
        0x30               => array('Zapnutí encoderu SCR'),
        MODFTPKLIENT       => array('Inicializace klienta ftp, nastaví počáteční čas, který bude odeslán v príští session'),
        0x32               => array('Test optické závory LED1'),
        0x33               => array('Test optické závory LED2'),
        0x34               => array('Zap/ vyp hlavního napájení (PWRDN_N)'),
        0x35               => array('Zap/ vyp hlavního napájení (COMP_ZAP)'),
);


/********************************************************************
* @brief Human information about Force coil function
* @param $address - identification in dec format
*/
function analyze_force_coil(&$FRAME, $address)
{
	global $force_coil_code;
	$answer = [];

	if( substr_cut($FRAME, 2) == "FF00" )
	{
	    if( !empty($force_coil_code[hexdec($address)]))
	        $answer[] = 'FF00h - '. $force_coil_code[hexdec($address)][0];
		else
		    $answer[] = 'FF00h - Run action at address '. $address .'h';
		
	}

	return $answer;
}

/********************************************************************
 * @brief Human information about Force coil function
 * @param $address - identification in dec format
 */
function analyze_read_coils(&$FRAME)
{
	global $force_coil_code;
	$answer = [];

	$len = strlen($FRAME)/2*8;
	$coil = 0x00;

	for($i=0; $i<$len; $i++)
	{
		if(($i % 8) == 0)
		{
		    $coil = hexdec(substr_cut($FRAME, 1));
		    $answer[] = $coil . "h coil";
		}

		$is_on = ($coil & (1 << ($i % 8)))? true: false;
		$answer[] = ($is_on? 'ON': 'OFF');
	}

	return $answer;
}

/********************************************************************
 * @brief Human information about Force coil function
 * @param $address - identification in dec format
 */
function analyze_force_multi_coils(&$FRAME, $address, $count)
{
	global $force_coil_code;
	$answer = [];
	$coil = 0x00;

	if( strlen($FRAME) < 2)
	    return $answer;

	$answer[] = hexdec(substr_cut($FRAME, 1)) . ' - Bytes';
	for($i=0; $i<$count; $i++)
	{
		if(($i % 8) == 0)
		    $coil = hexdec(substr_cut($FRAME, 1));

		$is_on = ($coil & (1 << ($i % 8)))? true: false;
		$answer[] = ($is_on? 'ON': 'OFF') . ' - ' . ($address + $i) .'h';
	}

	return $answer;
}

/*----------------------------------------------------------------------------*/
/* END OF FILE */
