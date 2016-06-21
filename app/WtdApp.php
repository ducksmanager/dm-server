<?php

namespace Wtd;

use Silex;


class WtdApp extends Silex\Application
{

    public function __construct()
    {
        parent::__construct();
        
        $app = $this;
        $app->mount('/', new Wtd());
    }

}
