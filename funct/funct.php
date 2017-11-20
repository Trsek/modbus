<?php
define (MODBUS_READ_COIL,       0x01);   // read coil status
define (MODBUS_READ_INPUT,      0x02);   // read input status
define (MODBUS_READ_HOLD_REG,   0x03);   // read holding registers
define (MODBUS_READ_INPUT_REG,  0x04);   // read input registers
define (MODBUS_FORCE_COIL,      0x05);   // force single coil
define (MODBUS_WRITE_REGISTER,  0x10);   // write holding registers
define (MODBUS_SERVER_ID,       0x11);   // Report Server ID
define (MODBUS_READ_FILE_REC,   0x14);   // Read File Record
define (MODBUS_TUNEL,           0x53);

$funct_code = 
	array(
			MODBUS_READ_COIL      => array('Read coil status'),
			MODBUS_READ_INPUT     => array('Read input status'),
			MODBUS_READ_HOLD_REG  => array('Read holding registers'),
			MODBUS_READ_INPUT_REG => array('Read input registers'),
			MODBUS_FORCE_COIL     => array('Force single coil'),
			MODBUS_WRITE_REGISTER => array('Write holding registers'),
			MODBUS_SERVER_ID      => array('Report Server ID'),
			MODBUS_READ_FILE_REC  => array('Read File Record'),
			MODBUS_TUNEL          => array('Elgas tunel'),
	);
	

/********************************************************************
* @brief Human information about Function
* @param $funct_text - identification in dec format
*/
function modbus_funct_name($funct_id)
{
	global $funct_code;
	
	if( $funct_code[$funct_id]==null )
		$answer = strtoupper(dechex($funct_id)). "h - unknown";
	else
		$answer = strtoupper(dechex($funct_id)). "h - ". $funct_code[$funct_id][0];

	return $answer;
}

/*----------------------------------------------------------------------------*/
/* END OF FILE */
