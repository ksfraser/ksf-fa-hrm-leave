<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\HRM\Leave\DTO;
final class LeaveRequestDTO {
    public $id;
    public $person_id;
    public $person_name;
    public $leave_type_id;
    public $leave_type_name;
    public $from_date;
    public $to_date;
    public $days;
    public $reason;
    public $state;
    public $current_approver_id;
    public $current_approver_name;
    public $created_at;
    public $updated_at;
    public function __construct(
        int $id,
        int $person_id = 0,
        string $person_name = '',
        int $leave_type_id = 0,
        string $leave_type_name = '',
        string $from_date = '',
        string $to_date = '',
        string $days = '0',
        string $reason = '',
        string $state = 'draft',
        int $current_approver_id = 0,
        string $current_approver_name = '',
        string $created_at = '',
        string $updated_at = ''
    ) {
        $this->id = $id;
        $this->person_id = $person_id;
        $this->person_name = $person_name;
        $this->leave_type_id = $leave_type_id;
        $this->leave_type_name = $leave_type_name;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->days = $days;
        $this->reason = $reason;
        $this->state = $state;
        $this->current_approver_id = $current_approver_id;
        $this->current_approver_name = $current_approver_name;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }
    public function getDisplayLabel(): string {
        $parts = array();
        if ($this->person_name !== '') { $parts[] = $this->person_name; }
        if ($this->from_date !== '') { $parts[] = $this->from_date . ' to ' . $this->to_date; }
        return $parts === [] ? _('Leave Request #') . (string) $this->id : implode(' — ', $parts);
    }
    public function toArray(): array {
        return array(
            'id' => $this->id,
            'person_id' => $this->person_id,
            'person_name' => $this->person_name,
            'leave_type_id' => $this->leave_type_id,
            'leave_type_name' => $this->leave_type_name,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'days' => $this->days,
            'reason' => $this->reason,
            'state' => $this->state,
            'current_approver_id' => $this->current_approver_id,
            'current_approver_name' => $this->current_approver_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        );
    }
    public static function fromArray(array $data): self {
        return new self(
            (int) ($data['id'] ?? 0),
            (int) ($data['person_id'] ?? 0),
            (string) ($data['person_name'] ?? ''),
            (int) ($data['leave_type_id'] ?? 0),
            (string) ($data['leave_type_name'] ?? ''),
            (string) ($data['from_date'] ?? ''),
            (string) ($data['to_date'] ?? ''),
            (string) ($data['days'] ?? '0'),
            (string) ($data['reason'] ?? ''),
            (string) ($data['state'] ?? 'draft'),
            (int) ($data['current_approver_id'] ?? 0),
            (string) ($data['current_approver_name'] ?? ''),
            (string) ($data['created_at'] ?? ''),
            (string) ($data['updated_at'] ?? '')
        );
    }
}
