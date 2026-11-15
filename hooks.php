<?php
declare(strict_types=1);
require_once __DIR__ . '/ComposerDependencies.php';
\Ksfraser\FrontAccounting\HRM\Leave\Utils\ComposerDependencies::ensure(__DIR__);
$autoload_path = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($autoload_path)) { require_once $autoload_path; }
require_once __DIR__ . '/includes/common.inc';
require_once __DIR__ . '/includes/events.inc';
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
define('SS_ksf_FA_HRM_Leave', 174 << 8);
if (!defined('SA_LEAVE_VIEW')) {
    define('SA_LEAVE_VIEW', SS_ksf_FA_HRM_Leave | 1);
    define('SA_LEAVE_MANAGE', SS_ksf_FA_HRM_Leave | 2);
    define('SA_LEAVE_APPROVE', SS_ksf_FA_HRM_Leave | 3);
    define('SA_LEAVE_ADMIN', SS_ksf_FA_HRM_Leave | 4);
}
class hooks_ksf_FA_HRM_Leave extends hooks {
    const RESPONDERS = array('RELATED_TO_FIELDS', 'LEAVE_REQUEST_RESOLVE', 'LEAVE_REQUEST_FIELDS', 'LEAVE_REQUEST_LIST');
    var $module_name = 'ksf_FA_HRM_Leave';
    var $version = '1.0.0';
    function install_tabs($app) { }
    function install_options($app) { }
    function install_access() {
        $security_sections[SS_ksf_FA_HRM_Leave] = _("Leave Management");
        $security_areas['SA_LEAVE_VIEW'] = array(SA_LEAVE_VIEW, _("View leave"));
        $security_areas['SA_LEAVE_MANAGE'] = array(SA_LEAVE_MANAGE, _("Manage leave"));
        $security_areas['SA_LEAVE_APPROVE'] = array(SA_LEAVE_APPROVE, _("Approve leave"));
        $security_areas['SA_LEAVE_ADMIN'] = array(SA_LEAVE_ADMIN, _("Administer leave"));
        return array($security_areas, $security_sections);
    }
    function activate_extension($company, $check_only = true) {
        if ($check_only) { return true; }
        \Ksfraser\FrontAccounting\HRM\Leave\Utils\ComposerDependencies::ensure(__DIR__);
        $this->update_databases($company, array(
            '0_hrm_leave_requests.sql' => array('hrm_leave_requests'),
            '0_hrm_leave_request_days.sql' => array('hrm_leave_request_days'),
        ), false);
        return true;
    }
    private function ensure_composer_dependencies() {
        \Ksfraser\FrontAccounting\HRM\Leave\Utils\ComposerDependencies::ensure(__DIR__);
    }
    public function getModuleConstants(&$data, $opts = null) {
        $data['constants'] = array(
            'SS_ksf_FA_HRM_Leave' => SS_ksf_FA_HRM_Leave,
            'SA_LEAVE_VIEW' => SA_LEAVE_VIEW,
            'SA_LEAVE_MANAGE' => SA_LEAVE_MANAGE,
            'SA_LEAVE_APPROVE' => SA_LEAVE_APPROVE,
            'SA_LEAVE_ADMIN' => SA_LEAVE_ADMIN,
        );
        return true;
    }
    public function getModuleCapabilities(&$data, $opts = null) {
        $data['capabilities'] = self::RESPONDERS;
        return true;
    }
    public function hasCapability(&$data, $opts) {
        $capability = is_array($opts) && isset($opts['capability']) ? $opts['capability'] : null;
        if ($capability === null && isset($data['capability'])) { $capability = $data['capability']; }
        $data['has_capability'] = in_array($capability, self::RESPONDERS, true);
        return $data['has_capability'];
    }
    public function respondToCapabilityRequest(&$data, $opts = null) {
        $request = is_array($opts) && isset($opts['request']) ? $opts['request'] : (isset($data['request']) ? $data['request'] : 'capabilities');
        $data['request'] = $request;
        $data['module'] = $this->module_name;
        if (strpos($request, 'has:') === 0) { return $this->hasCapability($data, array('capability' => substr($request, 4))); }
        switch ($request) {
            case 'capabilities': return $this->getModuleCapabilities($data, $opts);
            case 'constants': return $this->getModuleConstants($data, $opts);
            default: $data['error'] = 'Unknown request type: ' . $request; return null;
        }
    }
    public function RELATED_TO_FIELDS(&$data, $opts = null) {
        if (!isset($data['module']) || !is_string($data['module'])) { $data['error'] = 'RELATED_TO_FIELDS requires data[module]'; return null; }
        if (!isset($data['type']) || !is_string($data['type'])) { $data['error'] = 'RELATED_TO_FIELDS requires data[type]'; return null; }
        if ($data['type'] !== 'leave_request') { return null; }
        if (!isset($data['fields']) || !is_array($data['fields'])) { $data['fields'] = array(); }
        $data['fields'] = array_merge($data['fields'], array(
            'request_id' => _('Request ID'),
            'person_id' => _('Employee'),
            'leave_type_id' => _('Leave Type'),
            'from_date' => _('From Date'),
            'to_date' => _('To Date'),
            'days' => _('Days'),
            'state' => _('State'),
            'reason' => _('Reason'),
        ));
        return null;
    }
    public function LEAVE_REQUEST_RESOLVE(&$data, $opts = null) {
        if (!isset($data['request_id']) || !is_numeric($data['request_id'])) { $data['error'] = 'LEAVE_REQUEST_RESOLVE requires data[request_id]'; return null; }
        if (!isset($data['dto']) || !$data['dto'] instanceof RelatedRecordDto) { $data['error'] = 'LEAVE_REQUEST_RESOLVE requires data[dto] as RelatedRecordDto'; return null; }
        $service = new \Ksfraser\FrontAccounting\HRM\Leave\Service\LeaveRequestService();
        $data['result'] = $service->resolveById((int) $data['request_id'], $data['dto']);
        return null;
    }
    public function LEAVE_REQUEST_FIELDS(&$data, $opts = null) {
        $data['fields'] = array(
            'request_id' => _('Request ID'),
            'person_id' => _('Employee'),
            'leave_type_id' => _('Leave Type'),
            'from_date' => _('From Date'),
            'to_date' => _('To Date'),
            'days' => _('Days'),
            'state' => _('State'),
            'reason' => _('Reason'),
        );
        return null;
    }
    public function LEAVE_REQUEST_LIST(&$data, $opts = null) {
        if (!isset($data['person_id']) || !is_numeric($data['person_id'])) { $data['error'] = 'LEAVE_REQUEST_LIST requires data[person_id]'; return null; }
        $state = isset($data['state']) ? (string) $data['state'] : '';
        $service = new \Ksfraser\FrontAccounting\HRM\Leave\Service\LeaveRequestService();
        $data['result'] = $service->listForPerson((int) $data['person_id'], $state);
        return null;
    }
}
