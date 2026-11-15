<?php
declare(strict_types=1);
use Ksfraser\FrontAccounting\HRM\Leave\Service\LeaveRequestService;
use ksfraser\RelatedTo\DTO\RelatedQueryDto;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
use ksfraser\RelatedTo\UI\DefaultRelatedRecordsRenderer;
$path_to_root = '../../..';
include_once($path_to_root . '/includes/session.inc');
add_access_extensions();
$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) { display_error(_('Leave module dependencies are not installed')); exit; }
require_once $autoload;
$service = new LeaveRequestService();
$action = isset($_GET['action']) ? (string) $_GET['action'] : 'list';
if ($action === 'list') {
    page(_('Leave Requests'));
    $personId = isset($_GET['person_id']) ? (int) $_GET['person_id'] : 0;
    $state = isset($_GET['state']) ? (string) $_GET['state'] : '';
    $requests = $personId > 0 ? $service->listForPerson($personId, $state) : array();
    echo '<p>' . _('Leave Requests') . '</p>';
    echo '<table class="selection">';
    echo '<thead><tr>'
        . '<th>' . _('Employee') . '</th>'
        . '<th>' . _('Leave Type') . '</th>'
        . '<th>' . _('From') . '</th>'
        . '<th>' . _('To') . '</th>'
        . '<th>' . _('Days') . '</th>'
        . '<th>' . _('State') . '</th>'
        . '<th>' . _('Actions') . '</th>'
        . '</tr></thead><tbody>';
    foreach ($requests as $r) {
        echo '<tr>'
            . '<td>' . htmlspecialchars($r['fields']['person_id'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars($r['fields']['leave_type_id'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars($r['fields']['from_date'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars($r['fields']['to_date'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars($r['fields']['days'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars($r['fields']['state'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td><a href="' . htmlspecialchars($r['url'], ENT_QUOTES, 'UTF-8') . '" target="_blank">'
            . _('View') . '</a></td>'
            . '</tr>';
    }
    echo '</tbody></table>';
    // Related records rendered via RelatedTo hook
    $lhsId = isset($_GET['id']) ? (string) $_GET['id'] : '';
    if (function_exists('hook_invoke') && class_exists(DefaultRelatedRecordsRenderer::class) && $lhsId !== '') {
        $queryDto = new RelatedQueryDto('leave_request', $lhsId);
        $queryResult = $queryDto;
        hook_invoke('ksf_FA_RelatedTo', 'RELATED_TO', $queryResult, null);
        if (is_array($queryResult) && isset($queryResult['related']) && is_array($queryResult['related'])) {
            $renderer = new DefaultRelatedRecordsRenderer(array(
                'label_resolver' => function ($type, $id) { return (string) $id; },
                'url_resolver' => function ($type, $id) { return '#'; },
                'type_labels' => array('leave_request' => _('Leave Requests')),
            ));
            echo $renderer->renderRelatedRecords('leave_request', $lhsId, $queryResult['related'], array(
                'css_class' => 'related-records',
                'header_class' => 'related-header',
                'table_class' => 'related-table',
                'table_border' => true,
            ));
        }
    }
    end_page();
    exit;
}
display_error(_('Unknown action'));
exit;
