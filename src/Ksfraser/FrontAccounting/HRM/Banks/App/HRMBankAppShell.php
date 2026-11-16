<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\HRM\Banks\App;

use ksfraser\FrontAccounting\Common\App\AbstractAppShell;
use ksfraser\FrontAccounting\Common\Menu\FAModuleMenu;
use Ksfraser\FrontAccounting\HRM\Banks\Controller\HRMBankAccountController;

class HRMBankAppShell extends AbstractAppShell
{
    public function __construct()
    {
        parent::__construct('hrm_bank_account', 'index.php', 'view', 'bank_accounts');

        $this->add_module(_("HRM Bank"));

        $menu = new FAModuleMenu(
            'modules/ksf_FA_HRMBanks/index.php',
            'view',
            ''
        );

        $menu->addItem('bank_accounts', _("Bank Accounts"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_HRMBANK_VIEW');

        $this->add_extensions();
    }
}
