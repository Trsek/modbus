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
			MODBUS_WRITE_REGISTER => array('Write holding registers'),
			MODBUS_SERVER_ID      => array('Report Server ID'),
			MODBUS_READ_FILE_REC  => array('Read File Record'),
			MODBUS_TUNEL          => array('Elgas tunel'),
	);
	
$error_code =
	array(
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
