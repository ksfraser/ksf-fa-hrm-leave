<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\HRM\Leave\App;

use ksfraser\FrontAccounting\Common\App\AbstractAppShell;
use ksfraser\FrontAccounting\Common\Menu\FAModuleMenu;
use Ksfraser\FrontAccounting\HRM\Leave\Controller\LeaveTabController;

class LeaveAppShell extends AbstractAppShell
{
    public function __construct()
    {
        parent::__construct('leave', 'index.php', 'view', 'leave_requests');

        $this->add_module(_("Leave"));

        $menu = new FAModuleMenu(
            'modules/ksf_FA_HRM_Leave/index.php',
            'view',
            ''
        );

        $menu->addItem('leave_requests', _("Leave Requests"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_HRMBANK_VIEW');

        $this->add_extensions();
    }
}
