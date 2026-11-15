<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\HRM\Leave\Tests\Unit;
use Ksfraser\FrontAccounting\HRM\Leave\DTO\LeaveRequestDTO;
use PHPUnit\Framework\TestCase;
class LeaveRequestDtoTest extends TestCase {
    public function testGetDisplayLabel(): void {
        $dto = new LeaveRequestDTO(7, 123, 'Alice Smith', 1, 'Annual Leave', '2026-10-01', '2026-10-05', '5.00', 'Vacation', 'pending', 456, 'Bob Manager', '', '');
        $this->assertSame('Alice Smith — 2026-10-01 to 2026-10-05', $dto->getDisplayLabel());
    }
    public function testGetDisplayLabelMinimal(): void {
        $dto = new LeaveRequestDTO(3, 0, '', 0, '', '', '', '', '', 'draft', 0, '', '', '');
        $this->assertSame('Leave Request #3', $dto->getDisplayLabel());
    }
    public function testToArrayShape(): void {
        $dto = new LeaveRequestDTO(7, 123, 'Alice Smith', 1, 'Annual Leave', '2026-10-01', '2026-10-05', '5.00', 'Vacation', 'pending', 456, 'Bob Manager', '', '');
        $array = $dto->toArray();
        $this->assertSame(7, $array['id']);
        $this->assertSame(123, $array['person_id']);
        $this->assertSame('Alice Smith', $array['person_name']);
        $this->assertSame('pending', $array['state']);
    }
    public function testFromArrayReconstructs(): void {
        $dto = LeaveRequestDTO::fromArray(array(
            'id' => 9, 'person_id' => 456, 'person_name' => 'Bob Jones',
            'leave_type_id' => 2, 'leave_type_name' => 'Sick Leave',
            'from_date' => '2026-11-01', 'to_date' => '2026-11-02', 'days' => '2.00',
            'reason' => 'Flu', 'state' => 'approved', 'current_approver_id' => 789,
            'current_approver_name' => 'Carol Approver', 'created_at' => '', 'updated_at' => ''
        ));
        $this->assertSame(9, $dto->id);
        $this->assertSame(456, $dto->person_id);
        $this->assertSame('Bob Jones', $dto->person_name);
        $this->assertSame('Sick Leave', $dto->leave_type_name);
        $this->assertSame('approved', $dto->state);
        $this->assertSame(789, $dto->current_approver_id);
    }
}
