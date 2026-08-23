<?php

return [
    /*
    |--------------------------------------------------------------------
    | Customizable system PDF templates
    |--------------------------------------------------------------------
    |
    | Same intro/outro-zone pattern as config/printbuka_email_templates.php,
    | reusing the App\Models\EmailTemplate table and block builder — a PDF
    | template's key just lives in a distinct 'pdf.*' namespace. 'view'
    | is the Blade view Pdf::loadView() renders. 'variables' documents the
    | {{token}} placeholders available to that template's blocks.
    |
    */

    'pdf.invoice_customer' => [
        'name' => 'Invoice PDF (Customer Copy)',
        'view' => 'invoices.pdf',
        'variables' => ['customer_name', 'invoice_number', 'total_amount'],
    ],

    'pdf.invoice_admin' => [
        'name' => 'Invoice PDF (Admin Copy)',
        'view' => 'admin.invoices.pdf',
        'variables' => ['customer_name', 'invoice_number', 'total_amount'],
    ],

    'pdf.receipt' => [
        'name' => 'Paid Receipt PDF',
        'view' => 'receipts.pdf',
        'variables' => ['customer_name', 'invoice_number', 'total_amount'],
    ],

    'pdf.payslip' => [
        'name' => 'Payslip PDF',
        'view' => 'admin.payroll.payslip-pdf',
        'variables' => ['staff_name', 'period_label', 'net_salary'],
    ],

    'pdf.payroll_run' => [
        'name' => 'Payroll Run PDF',
        'view' => 'admin.payroll.run-pdf',
        'variables' => ['period_label', 'total_net'],
    ],

    'pdf.finance_entry' => [
        'name' => 'Finance Record PDF',
        'view' => 'admin.finance.pdf',
        'variables' => ['category', 'amount'],
    ],

    'pdf.finance_report' => [
        'name' => 'Finance Report PDF',
        'view' => 'admin.finance.report-pdf',
        'variables' => ['period_label', 'net_total'],
    ],

    'pdf.job_log' => [
        'name' => 'Job Log PDF',
        'view' => 'admin.orders.job-log-pdf',
        'variables' => ['order_number', 'customer_name'],
    ],

    'pdf.expense_log' => [
        'name' => 'Expense Log PDF',
        'view' => 'admin.orders.expense-log-pdf',
        'variables' => ['order_number', 'customer_name'],
    ],

    'pdf.shop_receipt' => [
        'name' => 'Shop Order Receipt PDF',
        'view' => 'shop.receipt-pdf',
        'variables' => ['customer_name', 'order_number', 'total_amount'],
    ],
];
