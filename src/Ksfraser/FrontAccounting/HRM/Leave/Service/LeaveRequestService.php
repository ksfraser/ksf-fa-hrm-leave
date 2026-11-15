<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\HRM\Leave\Service;
use Ksfraser\FrontAccounting\HRM\Leave\DTO\LeaveRequestDTO;
use Ksfraser\FrontAccounting\HRM\Leave\Repository\LeaveRequestRepository;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
final class LeaveRequestService {
    private $repo;
    public function __construct(?LeaveRequestRepository $repo = null) { $this->repo = $repo ?? new LeaveRequestRepository(); }
    public function resolveById(int $id, ?RelatedRecordDto $dto = null): array {
        $row = $this->repo->findById($id);
        if ($row === null) {
            return array('dto' => new RelatedRecordDto('leave_request', '0', 'leave_request', '0', null), 'fields' => $this->fieldMap(), 'labels' => $this->labelMap(), 'url' => '#', 'label' => _('Leave Request #') . (string) $id);
        }
        $r = LeaveRequestDTO::fromArray($row);
        $dto = $dto ?? new RelatedRecordDto('leave_request', (string) $r->id, 'leave_request', (string) $r->id, null);
        return array(
            'dto' => $dto,
            'fields' => array(
                'request_id' => (string) $r->id,
                'person_id' => $r->person_name,
                'leave_type_id' => $r->leave_type_name,
                'from_date' => $r->from_date,
                'to_date' => $r->to_date,
                'days' => $r->days,
                'state' => $r->state,
                'reason' => $r->reason,
            ),
            'labels' => $this->labelMap(),
            'url' => '/modules/ksf_FA_HRM_Leave/pages/leave_request_form.php?action=edit&request_id=' . $r->id,
            'label' => $r->getDisplayLabel(),
        );
    }
    public function listForPerson(int $personId, string $state = ''): array {
        $rows = $this->repo->findByPerson($personId, $state);
        $results = array();
        foreach ($rows as $row) {
            $r = LeaveRequestDTO::fromArray($row);
            $results[] = array(
                'id' => $r->id,
                'dto' => new RelatedRecordDto('leave_request', (string) $r->id, 'leave_request', (string) $r->id, null),
                'fields' => array(
                    'request_id' => (string) $r->id,
                    'person_id' => $r->person_name,
                    'leave_type_id' => $r->leave_type_name,
                    'from_date' => $r->from_date,
                    'to_date' => $r->to_date,
                    'days' => $r->days,
                    'state' => $r->state,
                    'reason' => $r->reason,
                ),
                'labels' => $this->labelMap(),
                'url' => '/modules/ksf_FA_HRM_Leave/pages/leave_request_form.php?action=edit&request_id=' . $r->id,
                'label' => $r->getDisplayLabel(),
            );
        }
        return $results;
    }
    public function listPendingForApprover(int $personId): array {
        $rows = $this->repo->listPendingForApprover($personId);
        $results = array();
        foreach ($rows as $row) {
            $r = LeaveRequestDTO::fromArray($row);
            $results[] = array(
                'id' => $r->id,
                'dto' => new RelatedRecordDto('leave_request', (string) $r->id, 'leave_request', (string) $r->id, null),
                'fields' => array(
                    'request_id' => (string) $r->id,
                    'person_id' => $r->person_name,
                    'leave_type_id' => $r->leave_type_name,
                    'from_date' => $r->from_date,
                    'to_date' => $r->to_date,
                    'days' => $r->days,
                    'state' => $r->state,
                    'reason' => $r->reason,
                ),
                'labels' => $this->labelMap(),
                'url' => '/modules/ksf_FA_HRM_Leave/pages/leave_request_form.php?action=edit&request_id=' . $r->id,
                'label' => $r->getDisplayLabel(),
            );
        }
        return $results;
    }
    public function insertRequest(array $data): int {
        return $this->repo->insertRequest($data);
    }
    public function updateState(int $requestId, string $state, ?int $currentApproverId = null): void {
        $this->repo->updateState($requestId, $state, $currentApproverId);
    }
    public function insertDay(int $requestId, string $date, float $consumed): void {
        $this->repo->insertDay($requestId, $date, $consumed);
    }
    public function requestDayRange(int $requestId): array {
        return $this->repo->requestDayRange($requestId);
    }
    public function fieldMap(): array { return $this->labelMap(); }
    public function labelMap(): array {
        return array(
            'request_id' => _('Request ID'),
            'person_id' => _('Employee'),
            'leave_type_id' => _('Leave Type'),
            'from_date' => _('From Date'),
            'to_date' => _('To Date'),
            'days' => _('Days'),
            'state' => _('State'),
            'reason' => _('Reason'),
        );
    }
    public function getStateTransitions(string $currentState): array {
        $transitions = array(
            'draft' => array('submitted' => _('Submit')),
            'submitted' => array('pending' => _('Submit for Approval')),
            'pending' => array('approved' => _('Approve'), 'rejected' => _('Reject')),
            'approved' => array('scheduled' => _('Schedule')),
        );
        return isset($transitions[$currentState]) ? $transitions[$currentState] : array();
    }
    public function isAllowedTransition(string $fromState, string $toState): bool {
        $transitions = $this->getStateTransitions($fromState);
        return isset($transitions[$toState]);
    }
}
