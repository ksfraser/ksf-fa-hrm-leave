-- ksf_FA_HRM_Leave: leave request tables

CREATE TABLE IF NOT EXISTS `0_hrm_leave_requests` (
  `id`             INT(11) NOT NULL AUTO_INCREMENT,
  `person_id`      INT(11) NOT NULL COMMENT 'FK to crm_persons.id (employee)',
  `leave_type_id`  INT(11) NOT NULL COMMENT 'FK to leave type master',
  `from_date`      DATE NOT NULL,
  `to_date`        DATE NOT NULL,
  `days`           DECIMAL(5,2) NOT NULL DEFAULT 0,
  `reason`         VARCHAR(255) DEFAULT NULL,
  `state`          VARCHAR(24) NOT NULL DEFAULT 'draft' COMMENT 'draft|submitted|pending|approved|rejected|rejected_exhausted|scheduled',
  `current_approver_id` INT(11) DEFAULT NULL COMMENT 'Current approver in chain',
  `created_at`     DATETIME NOT NULL,
  `updated_at`     DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_person_state` (`person_id`, `state`),
  KEY `idx_from_to` (`from_date`, `to_date`),
  KEY `idx_current_approver` (`current_approver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `0_hrm_leave_request_days` (
  `day_id`     INT(11) NOT NULL AUTO_INCREMENT,
  `request_id` INT(11) NOT NULL,
  `date`       DATE NOT NULL,
  `consumed`   DECIMAL(5,2) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`day_id`),
  UNIQUE KEY `idx_req_date` (`request_id`, `date`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
