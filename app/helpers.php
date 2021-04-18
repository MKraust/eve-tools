<?php
function logMemory()
{
    /* Currently used memory */
    $mem_usage = memory_get_usage();

    logger(round($mem_usage / 1024) . ' KB');
}
