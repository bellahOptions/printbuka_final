<?php

return [
    /*
    |--------------------------------------------------------------------
    | Customizable system email templates
    |--------------------------------------------------------------------
    |
    | Each entry registers one email as customizable via the admin email
    | builder. 'key' must match the templateKey() the Mailable declares
    | via App\Mail\Concerns\HasEditableTemplate. 'variables' documents
    | the {{token}} placeholders available to that template's blocks —
    | shown to the super admin while editing, purely informational.
    |
    */

    'staff.kyc_reminder' => [
        'name' => 'Staff KYC Reminder',
        'mail_class' => \App\Mail\StaffKycReminderMail::class,
        'variables' => ['staff_name', 'company_name'],
    ],

    'invoice.created' => [
        'name' => 'Invoice Created',
        'mail_class' => \App\Mail\InvoiceMail::class,
        'variables' => ['customer_name', 'invoice_number', 'total_amount', 'order_number'],
    ],

    'training.application_confirmation' => [
        'name' => 'Training Application Confirmation',
        'mail_class' => \App\Mail\TrainingApplicationConfirmationMail::class,
        'variables' => ['applicant_name', 'track'],
    ],

    'staff.daily_activity_summary' => [
        'name' => 'Daily Staff Activity Summary',
        'mail_class' => \App\Mail\DailyStaffActivitySummaryMail::class,
        'variables' => ['recipient_name', 'report_date', 'total_actions'],
    ],

    'staff.employment_status' => [
        'name' => 'Staff Employment Status Notice',
        'mail_class' => \App\Mail\StaffEmploymentStatusMail::class,
        'variables' => ['staff_name', 'status', 'reason'],
    ],

    'staff.kyc_review' => [
        'name' => 'Staff KYC Review Outcome',
        'mail_class' => \App\Mail\StaffKycReviewMail::class,
        'variables' => ['staff_name', 'status', 'reviewer_name', 'notes'],
    ],

    'staff.query_issued' => [
        'name' => 'Staff Query Issued',
        'mail_class' => \App\Mail\StaffQueryIssuedMail::class,
        'variables' => ['staff_name', 'query_number', 'query_subject', 'query_type'],
    ],

    'staff.signup_alert' => [
        'name' => 'Staff Signup Alert',
        'mail_class' => \App\Mail\StaffSignupAlertMail::class,
        'variables' => ['recipient_name', 'staff_name', 'staff_email'],
    ],

    'staff.task_assigned' => [
        'name' => 'Task Assigned',
        'mail_class' => \App\Mail\TaskAssignedMail::class,
        'variables' => ['recipient_name', 'assigner_name', 'task'],
    ],

    'staff.task_review_outcome' => [
        'name' => 'Task Review Outcome',
        'mail_class' => \App\Mail\TaskReviewOutcomeMail::class,
        'variables' => ['recipient_name', 'task', 'rating'],
    ],

    'finance.report' => [
        'name' => 'Finance Report',
        'mail_class' => \App\Mail\FinanceReportMail::class,
        'variables' => ['period_label', 'income_total', 'expense_total', 'net_total', 'generated_by_name'],
    ],

    'invoice.paid_receipt' => [
        'name' => 'Invoice Paid Receipt',
        'mail_class' => \App\Mail\InvoicePaidReceiptMail::class,
        'variables' => ['customer_name', 'invoice_number', 'total_amount', 'order_number'],
    ],

    'payroll.run' => [
        'name' => 'Payroll Run Summary',
        'mail_class' => \App\Mail\PayrollRunMail::class,
        'variables' => ['period_label', 'sent_by_name'],
    ],

    'payroll.payslip' => [
        'name' => 'Staff Payslip',
        'mail_class' => \App\Mail\PayslipMail::class,
        'variables' => ['staff_name', 'period_label', 'net_salary'],
    ],

    'invoice.unpaid_reminder' => [
        'name' => 'Unpaid Invoice Reminder',
        'mail_class' => \App\Mail\UnpaidInvoiceReminderMail::class,
        'variables' => ['customer_name', 'invoice_number', 'total_amount'],
    ],

    'shop.order_confirmation' => [
        'name' => 'Shop Order Confirmation',
        'mail_class' => \App\Mail\ShopOrderConfirmationMail::class,
        'variables' => ['customer_name', 'order_reference', 'total_amount'],
    ],

    'shop.order_failed' => [
        'name' => 'Shop Order Payment Failed',
        'mail_class' => \App\Mail\ShopOrderFailedMail::class,
        'variables' => ['customer_name', 'order_reference', 'total_amount'],
    ],

    'shop.order_status_update' => [
        'name' => 'Shop Order Status Update',
        'mail_class' => \App\Mail\ShopOrderStatusUpdateMail::class,
        'variables' => ['customer_name', 'order_reference', 'status_label'],
    ],

    'job.assigned_designer' => [
        'name' => 'Job Assigned to Designer',
        'mail_class' => \App\Mail\JobAssignedDesignerMail::class,
        'variables' => ['designer_name', 'order_number', 'customer_name', 'product_name'],
    ],

    'job.completed_appreciation' => [
        'name' => 'Job Completed Appreciation',
        'mail_class' => \App\Mail\JobCompletedAppreciationMail::class,
        'variables' => ['customer_name', 'order_number'],
    ],

    'job.conclusion_summary' => [
        'name' => 'Job Conclusion Summary',
        'mail_class' => \App\Mail\JobConclusionSummaryMail::class,
        'variables' => ['recipient_name', 'order_number', 'customer_name', 'concluded_by'],
    ],

    'job.finance_summary' => [
        'name' => 'Job Finance Summary',
        'mail_class' => \App\Mail\JobFinanceSummaryMail::class,
        'variables' => ['customer_name', 'order_number'],
    ],

    'job.phase_role_alert' => [
        'name' => 'Job Phase Role Alert',
        'mail_class' => \App\Mail\JobPhaseRoleAlertMail::class,
        'variables' => ['recipient_name', 'order_number', 'customer_name', 'phase_name', 'old_status', 'new_status'],
    ],

    'job.status_advanced_customer' => [
        'name' => 'Job Status Advanced (Customer)',
        'mail_class' => \App\Mail\JobStatusAdvancedCustomerMail::class,
        'variables' => ['customer_name', 'order_number', 'old_status', 'new_status'],
    ],

    'order.alert' => [
        'name' => 'Order Alert (Shop Order / Quote Request)',
        'mail_class' => \App\Mail\OrderAlertMail::class,
        'variables' => ['recipient_name', 'customer_name', 'order_number', 'alert_type'],
    ],
];
