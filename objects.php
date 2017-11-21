<?php
/********************************************************************
 * @brief Rot hex to other endian
 */
function rotOrder($cut_str, $len)
{
	$answer = "";
	while( $len-- ) 
	{
		$answer .= substr($cut_str, 2*$len, 2);
		$cut_str = substr($cut_str, 0, 2*$len);
	}
	
	return $answer;
}

/********************************************************************
* @brief Strip 'len' chars from start of string 
*/
function substr_cut(&$SMS, $len)
{
	if( $len < 0 )
	{
		$cut_str = substr($SMS, 2*$len);
		$SMS = substr($SMS, 0, strlen($SMS) + 2*$len);	// $len je -
	}
	else
	{
		$cut_str = substr($SMS, 0, 2*$len);
		$SMS = substr($SMS, 2*$len, strlen($SMS) - 2*$len);
	}
	return $cut_str;
}

/********************************************************************
* @brief hex2bin in PHP < 5.4.0
*/
if (PHP_VERSION_ID < 50400) {
function hex2bin($hex_string)
{
	return pack("H*" , $hex_string);
}
}

/********************************************************************
* @brief Convert hex to string
*/
function hexToStr($hex)
{
	$string='';
	for ($i=0; $i < strlen($hex)-1; $i+=2){
		$string .= chr(hexdec($hex[$i].$hex[$i+1]));
	}
	return $string;
}

/********************************************************************
* @brief Modbus date presentation
*/
function modbus_date($DATI)
{
	$data_second = hexdec(substr_cut($DATI, 1));
	$data_minute = hexdec(substr_cut($DATI, 1));	
	$data_hour   = hexdec(substr_cut($DATI, 1));	
	$data_day    = hexdec(substr_cut($DATI, 1));	
	$data_month  = hexdec(substr_cut($DATI, 1));	
	$data_year   = hexdec(substr_cut($DATI, 1))+2000;	
	
	return sprintf("%04d-%02d-%02d %02d:%02d:%02d", $data_year, $data_month, $data_day, $data_hour, $data_minute, $data_second);
}

/********************************************************************
* @brief When need value to one line
*/
function array_val_line($value)
{
	if(!is_array($value))
		return $value[0];

	$answer = "";
	foreach ($value[0] as $value_line)
	{
		$answer .= (empty($answer)? "": ", "). $value_line;
	}
	return $answer;
}

/*----------------------------------------------------------------------------*/
/* END OF FILE */
