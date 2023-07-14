<?php

namespace Helper;

class Helper
{
    function random_unique_id()
    {
        return substr(bin2hex(random_bytes(16)), 0, 6);
    }
}
