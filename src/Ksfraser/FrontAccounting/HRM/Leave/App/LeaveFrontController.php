<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\HRM\Leave\App;

use ksfraser\FrontAccounting\Common\App\AbstractFrontController;

class LeaveFrontController extends AbstractFrontController
{
    public function __construct(
        LeaveAppShell $appShell,
        string $fallbackSecurityArea,
        string $path_to_root
    ) {
        parent::__construct($appShell, $fallbackSecurityArea, $path_to_root);
    }
}
