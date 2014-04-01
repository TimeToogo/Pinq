<?php

namespace Pinq;

class PinqException extends \Exception
{
    /**
     * @param \Exception $InnerException
     * @param type       $MessageFormat
     * @param mixed ... The values to interpole the message with
     */
    public function __construct($MessageFormat = '', $_ = null)
    {
        if (func_num_args() === 1) {
            $Message = $MessageFormat;
        } 
        else {
            $Message = call_user_func_array('sprintf', array_merge([$MessageFormat], array_slice(func_get_args(), 1)));
        }

        parent::__construct($Message, null, null);
    }
}
