<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\HRM\Leave\App;

use ksfraser\FrontAccounting\Common\App\AbstractFrontController;

class HRMBankFrontController extends AbstractFrontController
{
    public function __construct(
        HRMBankAppShell $appShell,
        string $fallbackSecurityArea,
        string $path_to_root
    ) {
        parent::__construct($appShell, $fallbackSecurityArea, $path_to_root);
    }
}
