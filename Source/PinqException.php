<?php

namespace Pinq;

class PinqException extends \Exception
{
    /**
     * @param type       $MessageFormat
     * @param mixed ... The values to interpole the message with
     */
    public function __construct($MessageFormat = '', $_ = null)
    {
        if (func_num_args() === 1) {
            $Message = $MessageFormat;
        } 
        else {
            $Message = call_user_func_array('sprintf', func_get_args());
        }

        parent::__construct($Message, null, null);
    }
    
    public static function Construct(array $Parameters) {
        if ($Parameters === 1) {
            $MessageFormat = array_shift($MessageFormat);
            $Message = $MessageFormat;
        } 
        else {
            $Message = call_user_func_array('sprintf', $Parameters);
        }

        return new static($Message);
    }
    
    public static function InvalidIterable($Method, $Value)
    {
        return new self(
                    'Invalid argument for %s: expecting \Traversable or array, %s given',
                    $Method,
                    \Pinq\Utilities::GetTypeOrClass($Value));
    }
    
    public static function NotSupported($Method)
    {
        return new self('Invalid call to %s: Method is not supported', $Method);
    }
}
