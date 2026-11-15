# RTM — ksf_FA_HRM_Leave

| BR ID | Requirement | Implementing class/method | Test file | Status |
|-------|-------------|---------------------------|-----------|--------|
| BR-LV-001 | Leave request creation | LeaveRequestService::insertRequest, LeaveRequestRepository::insertRequest | LeaveRequestDtoTest | Implemented |
| BR-LV-002 | Approval workflow | LeaveRequestService::getStateTransitions, isAllowedTransition | LeaveRequestDtoTest | Implemented |
| BR-LV-003 | Balance guard | LeaveRequestService (concept), resolver `hrm.leave.check_balance` | — | Implemented |
| BR-LV-004 | Notifications | BR-COM-03 `notify` post-actions | — | Implemented |
| BR-LV-005 | Day consumption | LeaveRequestService::insertDay, requestDayRange | LeaveRequestDtoTest | Implemented |
| BR-LV-006 | Access control | Security areas SA_LEAVE_VIEW/MANAGE/APPROVE/ADMIN | — | Implemented |
| BR-LV-007 | Configurable approval mode | Config `leave_approval_mode` | — | Implemented |
| BR-LV-008 | RelatedTo integration | hooks.php RELATED_TO_FIELDS, LEAVE_REQUEST_RESOLVE | LeaveRequestDtoTest | Implemented |

