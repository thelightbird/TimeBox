<?php

namespace TimeBox\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TimeBoxUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
