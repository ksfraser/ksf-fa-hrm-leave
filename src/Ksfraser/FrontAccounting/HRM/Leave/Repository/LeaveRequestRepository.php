<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\HRM\Leave\Repository;
final class LeaveRequestRepository {
    private string $table = 'hrm_leave_requests';
    private string $tableDays = 'hrm_leave_request_days';
    public function findAll(): array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return array(); }
        $sql = "SELECT r.*, c.name AS person_name FROM " . TB_PREF . $this->table . " r"
            . " LEFT JOIN " . TB_PREF . "crm_persons c ON r.person_id = c.id"
            . " ORDER BY r.created_at DESC, r.id DESC";
        $result = db_query($sql, 'Could not load leave requests');
        if ($result === false) { return array(); }
        $rows = array();
        while (($row = db_fetch_assoc($result)) !== false && $row !== null) { $rows[] = $row; }
        return $rows;
    }
    public function findById(int $id): ?array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return null; }
        $sql = "SELECT r.*, c.name AS person_name FROM " . TB_PREF . $this->table . " r"
            . " LEFT JOIN " . TB_PREF . "crm_persons c ON r.person_id = c.id"
            . " WHERE r.id = " . (int) $id;
        $result = db_query($sql, 'Could not load leave request');
        if ($result === false) { return null; }
        $row = db_fetch_assoc($result);
        return $row === false ? null : $row;
    }
    public function findByPerson(int $personId, string $state = ''): array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return array(); }
        $sql = "SELECT r.*, c.name AS person_name FROM " . TB_PREF . $this->table . " r"
            . " LEFT JOIN " . TB_PREF . "crm_persons c ON r.person_id = c.id"
            . " WHERE r.person_id = " . (int) $personId;
        if ($state !== '') {
            $sql .= " AND r.state = " . $this->escape($state);
        }
        $sql .= " ORDER BY r.from_date DESC";
        $result = db_query($sql, 'Could not load leave requests by person');
        if ($result === false) { return array(); }
        $rows = array();
        while (($row = db_fetch_assoc($result)) !== false && $row !== null) { $rows[] = $row; }
        return $rows;
    }
    public function listPendingForApprover(int $personId): array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return array(); }
        $sql = "SELECT r.*, c.name AS person_name FROM " . TB_PREF . $this->table . " r"
            . " LEFT JOIN " . TB_PREF . "crm_persons c ON r.person_id = c.id"
            . " WHERE r.state = 'pending' AND r.current_approver_id = " . (int) $personId
            . " ORDER BY r.from_date ASC";
        $result = db_query($sql, 'Could not load pending leave requests');
        if ($result === false) { return array(); }
        $rows = array();
        while (($row = db_fetch_assoc($result)) !== false && $row !== null) { $rows[] = $row; }
        return $rows;
    }
    public function insertRequest(array $data): int {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return 0; }
        $sql = "INSERT INTO " . TB_PREF . $this->table . " (
                person_id, leave_type_id, from_date, to_date, days, reason, state, current_approver_id, created_at, updated_at)
            VALUES ("
            . $this->intVal($data['person_id'] ?? 0) . ", "
            . $this->intVal($data['leave_type_id'] ?? 0) . ", "
            . $this->escape($data['from_date'] ?? '') . ", "
            . $this->escape($data['to_date'] ?? '') . ", "
            . $this->floatVal($data['days'] ?? 0) . ", "
            . $this->escape($data['reason'] ?? '') . ", "
            . $this->escape($data['state'] ?? 'draft') . ", "
            . ($data['current_approver_id'] !== null ? $this->intVal($data['current_approver_id']) : 'NULL') . ", "
            . $this->escape(date('Y-m-d H:i:s')) . ", "
            . $this->escape(date('Y-m-d H:i:s')) . ")";
        $this->dbQuery($sql);
        return $this->dbInsertId();
    }
    public function updateState(int $requestId, string $state, ?int $currentApproverId = null): void {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return; }
        $sets = ["state = " . $this->escape($state)];
        if ($currentApproverId !== null) {
            $sets[] = "current_approver_id = " . $this->intVal($currentApproverId);
        }
        $sets[] = "updated_at = " . $this->escape(date('Y-m-d H:i:s'));
        $sql = "UPDATE " . TB_PREF . $this->table . " SET " . implode(', ', $sets)
            . " WHERE id = " . $this->intVal($requestId);
        $this->dbQuery($sql);
    }
    public function insertDay(int $requestId, string $date, float $consumed): void {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return; }
        $sql = "INSERT INTO " . TB_PREF . $this->tableDays . "
            (request_id, date, consumed, created_at)
            VALUES (" . $this->intVal($requestId) . ", "
            . $this->escape($date) . ", "
            . $this->floatVal($consumed) . ", "
            . $this->escape(date('Y-m-d H:i:s')) . ")";
        $this->dbQuery($sql);
    }
    public function requestDayRange(int $requestId): array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return array(); }
        $sql = "SELECT `date` FROM " . TB_PREF . $this->tableDays
            . " WHERE request_id = " . $this->intVal($requestId) . " ORDER BY `date` ASC";
        $result = $this->dbQuery($sql);
        $rows = array();
        while (($row = db_fetch_assoc($result)) !== false && $row !== null) { $rows[] = $row; }
        return $rows;
    }
    private function dbQuery($sql, $errorMsg = null) {
        $result = db_query($sql, $errorMsg);
        return $result;
    }
    private function dbInsertId() {
        return db_insert_id();
    }
    private function dbFetchAssoc($result) {
        return db_fetch_assoc($result);
    }
    private function dbFetchAll($result) {
        $rows = array();
        while (($row = db_fetch_assoc($result)) !== false && $row !== null) { $rows[] = $row; }
        return $rows;
    }
    private function escape($value): string {
        return function_exists('db_escape') ? db_escape($value) : addslashes((string) $value);
    }
    private function intVal($value): int {
        return (int) $value;
    }
    private function floatVal($value): float {
        return (float) $value;
    }
}
