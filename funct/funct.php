<?php
define (MODBUS_READ_COIL,       0x01);   // read coil status
define (MODBUS_READ_INPUT,      0x02);   // read input status
define (MODBUS_READ_HOLD_REG,   0x03);   // read holding registers
define (MODBUS_READ_INPUT_REG,  0x04);   // read input registers
define (MODBUS_FORCE_COIL,      0x05);   // force single coil
define (MODBUS_06,              0x06);   // Preset Single Register
define (MODBUS_07,              0x07);   // Read Exception Status
define (MODBUS_08,              0x08);   // Diagnostics
define (MODBUS_09,              0x09);   // Program 484
define (MODBUS_0A,              0x0A);   // Poll 484
define (MODBUS_0B,              0x0B);   // Fetch Comm. Event Ctrl.
define (MODBUS_0C,              0x0C);   // Fetch Comm. Event Log
define (MODBUS_0D,              0x0D);   // Program Controller
define (MODBUS_0E,              0x0E);   // Poll Controller
define (MODBUS_0F,              0x0F);   // Force Multiple Coils
define (MODBUS_WRITE_REGISTER,  0x10);   // write holding registers
define (MODBUS_SERVER_ID,       0x11);   // Report Server ID
define (MODBUS_12,              0x12);   // Program 884/M84
define (MODBUS_13,              0x13);   // Reset Comm Link
define (MODBUS_READ_FILE_REC,   0x14);   // Read File Record
define (MODBUS_15,              0x15);   // Write General Reference
define (MODBUS_16,              0x16);   // Mask Write 4X Registers
define (MODBUS_17,              0x17);   // Read/Write 4X Registers
define (MODBUS_18,              0x18);   // Read FIFO Queue
define (MODBUS_44,              0x44);   // Přechod do klidového stavu
define (MODBUS_45,              0x45);   // Start jednorázového měření
define (MODBUS_46,              0x46);   // Synchronizace
define (MODBUS_47,              0x47);   // Mazání dat
define (MODBUS_48,              0x48);   // Nastavení  adresy
define (MODBUS_50,              0x50);   // Čtení registrů s nulováním INT
define (MODBUS_51,              0x51);   // Zápis výstupů
define (MODBUS_52,              0x52);   // Mazání dat s novým časem
define (MODBUS_TUNEL,           0x53);
define (MODBUS_64,              0x64);   // prepnuti na loader
define (MODBUS_65,              0x65);   // zapis sektoru v loaderu
define (MODBUS_66,              0x66);   // kontrola a prehrani firmware
define (MODBUS_6E,              0x6E);   // pro speciální účely

define (m_Ileg,          0x00);
define (m_IlegFunc,      0x01);
define (m_IlegDataAdr,   0x02);
define (m_IlegDataVal,   0x03);
define (m_IlegSlaveFail, 0x04);
define (m_IlegDevBusy,   0x06);

$funct_code = 
	array(
            MODBUS_READ_COIL      => array('Read coil status'),
            MODBUS_READ_INPUT     => array('Read input status'),
            MODBUS_READ_HOLD_REG  => array('Read holding registers'),
            MODBUS_READ_INPUT_REG => array('Read input registers'),
            MODBUS_FORCE_COIL     => array('Force single coil'),
            MODBUS_06             => array('Preset Single Register'),
            MODBUS_07             => array('Read Exception Status'),
            MODBUS_08             => array('Diagnostics'),
            MODBUS_09             => array('Program 484'),
            MODBUS_0A             => array('Poll 484'),
            MODBUS_0B             => array('Fetch Comm. Event Ctrl.'),
            MODBUS_0C             => array('Fetch Comm. Event Log'),
            MODBUS_0D             => array('Program Controller'),
            MODBUS_0E             => array('Poll Controller'),
            MODBUS_0F             => array('Force Multiple Coils'),
            MODBUS_WRITE_REGISTER => array('Write holding registers'),
            MODBUS_SERVER_ID      => array('Report Server ID'),
            MODBUS_READ_FILE_REC  => array('Read File Record'),
            MODBUS_15             => array('Write General Reference'),
            MODBUS_16             => array('Mask Write 4X Registers'),
            MODBUS_17             => array('Read/Write 4X Registers'),
            MODBUS_18             => array('Read FIFO Queue'),
            MODBUS_44             => array('Přechod do klidového stavu'),
            MODBUS_45             => array('Start jednorázového měření'),
            MODBUS_46             => array('Synchronizace'),
            MODBUS_47             => array('Mazání dat'),
            MODBUS_48             => array('Nastavení  adresy'),
            MODBUS_50             => array('Čtení registrů s nulováním INT'),
            MODBUS_51             => array('Zápis výstupů'),
            MODBUS_52             => array('Mazání dat s novým časem'),
            MODBUS_TUNEL          => array('Elgas tunel'),
            MODBUS_64             => array('prepnuti na loader'),
            MODBUS_65             => array('zapis sektoru v loaderu'),
            MODBUS_66             => array('kontrola a prehrani firmware'),
            MODBUS_6E             => array('pro speciální účely'),
	    );

$error_code =
	array(
            m_Ileg          => array('None'),
            m_IlegFunc      => array('Illegal function'),
            m_IlegDataAdr   => array('Illegal data address'),
            m_IlegDataVal   => array('Illegal data value'),
            m_IlegSlaveFail => array('Slave system fail'),
            m_IlegDevBusy   => array('Device bussy'),
    );

/********************************************************************
* @brief Human information about Function
* @param $funct_id - identification in dec format
*/
function modbus_funct_name($funct_id)
{
	global $funct_code;
	
	$is_error = false;
	if( $funct_id & 0x80 ) {
		$is_error = true;
		$funct_id -= 0x80;
	}
	
	if( $funct_code[$funct_id] == null )
		$answer[] = strtoupper(dechex($funct_id)). "h - unknown";
	else
		$answer[] = strtoupper(dechex($funct_id)). "h - ". $funct_code[$funct_id][0];
	
	if( $is_error )
		$answer[] = "80h - Error";

	return $answer;
}

/********************************************************************
* @brief Parse error code
*/
function analyze_error(&$FRAME)
{
	global $error_code;
	
	$error = hexdec(substr_cut($FRAME, 1));
	$answer[] = strtoupper(dechex($error)). "h - ". $error_code[$error][0];
	return $answer;
}

/*----------------------------------------------------------------------------*/
/* END OF FILE */
