<?php

namespace Pinq;

interface IJoiningToTraversable
{    
    /**
     * Returns the appropriate values according to the supplied join function.
     * Both values will be passed as arguments to the supplied function.
     * 
     * @param callable $JoinFunction 
     * @return ITraversable
     */
    public function To(callable $JoinFunction);
}
