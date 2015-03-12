<?php

use Luki\Storage;

function fd($value, $name = NULL)
{
    if ( Storage::isProfiler() ) {
        Storage::Profiler()->debug($value, $name);
    }
    
    unset($value, $name);
}

# End of file