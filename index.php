<?php
declare(strict_types=1);

use Ksfraser\FrontAccounting\HRM\Leave\App\LeaveFrontController;
use Ksfraser\FrontAccounting\HRM\Leave\App\LeaveAppShell;

$path_to_root = '../..';

if (file_exists(__DIR__ . '/bootstrap.php')) {
    require_once __DIR__ . '/bootstrap.php';
}

$frontController = new LeaveFrontController(
    new LeaveAppShell(),
    'SA_HRMBANK_VIEW',
    $path_to_root
);

$page_security = $frontController->getSecurityArea();
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");

$frontController->run();
