# Business Requirements — ksf_FA_HRM_Leave

**Package:** `ksfraser/ksf-fa-hrm-leave`
**Version:** `1.0.0`
**Namespace:** `Ksfraser\FrontAccounting\HRM\Leave\*`
**Module sequence:** LV (Leave)

---

## BR-LV-001 — Leave request creation

**Status:** IMPLEMENTED

Employees can submit leave requests `(person_id, leave_type_id, from_date, to_date, days, reason)`.

1. Request rows live in `0_hrm_leave_requests` with append-only day book `0_hrm_leave_request_days`.
2. Default state is `draft`; submitting moves to `submitted` → auto `pending` for first approver.
3. Days are computed from date range (inclusive).
4. Reason is optional free text.

---

## BR-LV-002 — Approval workflow (BR-COM-02 state machine)

**Status:** IMPLEMENTED

Leave approval uses the BR-COM-02 process `leave_request.approval` with states:
`draft → submitted → pending → approved/rejected/rejected_exhausted → scheduled`.

1. Approver chain resolves via `reports_to` (OrgHierarchyService).
2. Each level approves via user trigger; whole chain completes → `approved`.
3. Override/alternate approvers via optional `approver_id`.
4. Chain is a transition pre-action via resolver `hrm.approval_chain.nextStep`.
5. `allowed_transitions` exposed on DTO for Approve/Reject button gating.

---

## BR-LV-003 — Balance guard

**Status:** IMPLEMENTED

Entitlement guard at submit and approve time:

1. `days_requested <= balance_remaining` for that leave type × year at submit time.
2. Approve guard re-checks still-current balance at approval moment (parallel request may have consumed it).
3. `rejected_exhausted` auto-transition fires when parallel request consumed balance; message names blocker request.

---

## BR-LV-004 — Notifications (BR-COM-03)

**Status:** IMPLEMENTED

- `submitted` → next approver in chain
- `approved`/`rejected` → requester + delegating admin
- Published as `notify` rows with deep link to leave request page

---

## BR-LV-005 — Day consumption

**Status:** IMPLEMENTED

On `approved → scheduled`, the `consume_days` post-action:
- Writes each day of the request as a consumed day in `0_hrm_leave_request_days`
- Decrements `used_days` on `0_hrm_leave_balances`
- Calendar view renders approved requests per team

---

## BR-LV-006 — Access control

**Status:** IMPLEMENTED

- `SA_LEAVE_VIEW` — view own leave requests
- `SA_LEAVE_MANAGE` — create/edit own leave requests
- `SA_LEAVE_APPROVE` — approve/reject requests routed to user
- `SA_LEAVE_ADMIN` — administer all leave
- Access follows HRM Access Control model: Employee sees own, Manager sees team, HR Admin sees all

---

## BR-LV-007 — Configurable approval mode

**Status:** IMPLEMENTED

Approval mode is configurable via `leave_approval_mode`:
- `report_to` (default): only direct manager in chain approves
- `report_to_and_project_managers`: all project managers whose project the employee is assigned to must also approve

---

## BR-LV-008 — RelatedTo integration

**Status:** IMPLEMENTED

- `leave_request` type registered in RelatedTo registry
- `RELATED_TO_FIELDS` answers with request field map
- `RELATED_TO` resolver returns resolved LeaveRequestDTO instances

