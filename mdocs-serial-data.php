<?php
/**
* Extract what remains from an unintentionally truncated serialized string
*
* Example Usage:
* 
* the native unserialize() function returns false on failure
* $data = @unserialize($serialized); // @ silences the default PHP failure notice
* if ($data === false) // could not unserialize
* { 
*   $data = repairSerializedArray($serialized); // salvage what we can
* }
*
* $data contains your original array (or what remains of it).
 
* @param string The serialized array
*/

class repairSerial {

public function repairSerializedArray($serialized)
{
    $tmp = preg_replace('/^a:\d+:\{/', '', $serialized);
    return $this->repairSerializedArray_R($tmp); // operates on and whittles down the actual argument
}
 
/**
* The recursive function that does all of the heavy lifing. Do not call directly.
* @param string The broken serialzized array
* @return string Returns the repaired string
*/
private function repairSerializedArray_R(&$broken)
{
    // array and string length can be ignored
    // sample serialized data
    // a:0:{}
    // s:4:"four";
    // i:1;
    // b:0;
    // N;
    $data       = array();
    $index      = null;
    $len        = strlen($broken);
    $i          = 0;
 
    while(strlen($broken))
    {
        $i++;
        if ($i > $len)
        {
            break;
        }
 
        if (substr($broken, 0, 1) == '}') // end of array
        {
            $broken = substr($broken, 1);
            return $data;
        }
        else
        {
            $bite = substr($broken, 0, 2);
            switch($bite)
            {   
                case 's:': // key or value
                    $re = '/^s:\d+:"([^\"]*)";/';
                    if (preg_match($re, $broken, $m))
                    {
                        if ($index === null)
                        {
                            $index = $m[1];
                        }
                        else
                        {
                            $data[$index] = $m[1];
                            $index = null;
                        }
                        $broken = preg_replace($re, '', $broken);
                    }
                break;
 
                case 'i:': // key or value
                    $re = '/^i:(\d+);/';
                    if (preg_match($re, $broken, $m))
                    {
                        if ($index === null)
                        {
                            $index = (int) $m[1];
                        }
                        else
                        {
                            $data[$index] = (int) $m[1];
                            $index = null;
                        }
                        $broken = preg_replace($re, '', $broken);
                    }
                break;
 
                case 'b:': // value only
                    $re = '/^b:[01];/';
                    if (preg_match($re, $broken, $m))
                    {
                        $data[$index] = (bool) $m[1];
                        $index = null;
                        $broken = preg_replace($re, '', $broken);
                    }
                break;
 
                case 'a:': // value only
                    $re = '/^a:\d+:\{/';
                    if (preg_match($re, $broken, $m))
                    {
                        $broken         = preg_replace('/^a:\d+:\{/', '', $broken);
                        $data[$index]   = $this->repairSerializedArray_R($broken);
                        $index = null;
                    }
                break;
 
                case 'N;': // value only
                    $broken = substr($broken, 2);
                    $data[$index]   = null;
                    $index = null;
                break;
            }
        }
    }
 
    return $data;
}
}

?>