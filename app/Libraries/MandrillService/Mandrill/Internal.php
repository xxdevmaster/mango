<?php
namespace App\Libraries\MandrillService;

class Mandrill_Internal {
    public function __construct(Mandrill $master) {
        $this->master = $master;
    }

}


