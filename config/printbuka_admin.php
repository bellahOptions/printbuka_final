<?php

return [
    'roles' => [
        'super_admin'        => ['*'],
        'managing_director'  => ['*'],
        'hr'                 => ['admin.view', 'orders.view', 'staff.view', 'staff.kyc', 'staff.queries', 'staff.evaluations', 'training.manage', 'announcements.view', 'blog.view', 'evaluations.view', 'customers.manage', 'payroll.manage', 'payroll.view'],
        'operations_manager' => ['admin.view', 'orders.view', 'orders.verify', 'orders.phase_comment', 'workflow.approve', 'sop.verify', 'invoices.manage', 'production.update', 'qc.update', 'delivery.update', 'payroll.view', 'shop-products.manage', 'shop-orders.view'],
        'customer_service'   => ['admin.view', 'orders.view', 'orders.create', 'orders.intake', 'invoices.manage', 'delivery.update', 'client_review.update', 'sop.update', 'newsletters.manage', 'customers.manage', 'finance.view', 'finance.create', 'finance.update', 'finance.delete', 'finance.view_amounts', 'shop-orders.view'],
        'personal_assistant' => ['admin.view', 'orders.view', 'orders.intake', 'design.update', 'design.upload', 'production.update', 'packaging.update', 'qc.update', 'delivery.update'],
        'office_assistant'   => ['admin.view', 'orders.view'],
        'machine_operator'   => ['admin.view', 'orders.view', 'production.update', 'packaging.update'],
        'staff_pending'      => [],
    ],

    'role_priority' => [
        'super_admin'        => 100,
        'managing_director'  => 95,
        'hr'                 => 80,
        'operations_manager' => 85,
        'customer_service'   => 70,
        'personal_assistant' => 60,
        'office_assistant'   => 30,
        'machine_operator'   => 25,
        'staff_pending'      => 5,
        'customer'           => 0,
    ],

    'role_labels' => [
        'super_admin'        => 'Super Admin',
        'managing_director'  => 'MD / CEO',
        'hr'                 => 'HR',
        'operations_manager' => 'Operations Manager',
        'customer_service'   => 'Customer Service',
        'personal_assistant' => 'Personal Assistant to the CEO',
        'office_assistant'   => 'Office Assistant / Apprentice',
        'machine_operator'   => 'Machine Operator',
        'staff_pending'      => 'Pending Staff',
    ],

    'todo_statuses' => [
        'pending' => 'Pending',
        'working_on_it' => 'Working on it',
        'completed' => 'Completed',
        'reviewed' => 'Reviewed',
        'review_requested' => 'Completed (Legacy)',
        'approved' => 'Reviewed (Legacy)',
        'rejected' => 'Reviewed (Legacy)',
    ],

    'todo_review_roles' => [
        'super_admin',
        'operations_manager',
        'managing_director',
    ],

    'staff_signup_roles' => [
        'operations_manager' => 'Operations Manager',
        'customer_service'   => 'Customer Service',
        'personal_assistant' => 'Personal Assistant to the CEO',
        'office_assistant'   => 'Office Assistant / Apprentice',
        'machine_operator'   => 'Machine Operator',
        'hr'                 => 'HR',
        'other'              => 'Other',
    ],

    'staff_dashboard_menus' => [
        'super_admin'        => ['All Data', 'Staff Approvals', 'Jobs', 'Invoices', 'Finance', 'Settings'],
        'managing_director'  => ['Jobs', 'Finance', 'Payroll View', "Today's Tasks"],
        'hr'                 => ['Staff Directory', 'Bio-Data KYC', 'Staff Queries', 'Monthly Evaluations', 'Payroll', 'Training Applications', 'Announcements'],
        'operations_manager' => ['Job Process', 'Phase Approvals', 'Invoices', 'Production & QC', 'Payroll View', "Today's Tasks"],
        'customer_service'   => ['Customer Data', 'Job Information', 'Finance', 'Invoices', 'Job Cards', "Today's Tasks"],
        'personal_assistant' => ['Job Briefs', 'Design', 'Production', "Today's Tasks"],
        'office_assistant'   => ['Job Information', "Today's Tasks"],
        'machine_operator'   => ['Production Jobs', 'Packaging', "Today's Tasks"],
    ],

    'module_permissions' => [
        'products.manage' => 'Product Management',
        'product_categories.manage' => 'Product Category Management',
        'blog.manage' => 'Blog Management',
        'invoices.manage' => 'Invoice Management',
        'newsletters.manage' => 'Newsletter Campaigns',
        'finance.view' => 'Finance',
        'site_settings.manage' => 'Site Settings',
        'customers.manage' => 'Customer Management',
        'training.manage' => 'Training Applications',
    ],

    'job_statuses' => [
        'Quote Requested',
        'Analyzing Job Brief',
        'Design / Artwork Preparation',
        'In Production',
        'Quality Check & Packaging',
        'Delivery In Progress',
        'Delivered',
        'Client Review — Satisfactory',
        'After-Sales: Revision Requested',
        'After-Sales: Reprint Required',
        'On Hold',
        'Cancelled',
    ],

    'job_types' => [
        'System Job',
        'Multiple Order',
        'Custom  Order',
    ],

    'sizes' => [
        'A3',
        'A4',
        'A5',
        'A6',
        'DL',
        '85x55mm (Business Card)',
        'Custom — see notes',
    ],

    'materials' => [
        'Art Paper 90gsm',
        'Art Paper 115gsm',
        'Art Paper 150gsm',
        'Art Paper 200gsm',
        'Art Card 250gsm',
        'Art Card 300gsm',
        'Art Card 350gsm',
        'Conqueror Paper',
        'Royal Executive Paper',
        'Tshirt',
        'Glass',
        'Vinyl',
        'PVC',
        'Fabric',
        'Cotton',
        'Aluminium',
        'Wood',
        'Acrylic',
        'Flex banner',
        'SAV',
        'Other',
    ],

    'finishes' => [
        'Gloss Lamination',
        'Mattes Lamination',
        'Spot UV',
        'No Finish',
        'Embossing',
        'Foil Stamping',
        'Perforation',
        'Spiral Binding',
        'Saddle Stitch',
        'Perfect Bind',
    ],

    'payment_statuses' => [
        'Awaiting Invoice',
        'Invoice Issued',
        'Pending Payment',
        'Part Payment',
        'Invoice Settled (70%)',
        'Invoice Settled (100%)',
        'Credit Terms',
    ],

    'payment_methods' => [
        'Bank Transfer',
        'Cash',
        'POS',
        'Cheque',
        'Online (Paystack)',
        'Other',
    ],

    'payment_terms' => [
        'standard' => 'Standard (70% before production)',
        'credit'   => 'Credit Terms (deliver before payment)',
    ],

    'priorities' => [
        '🔴 Urgent',
        '🟡 Normal',
        '🟢 Low',
    ],

    'job_channels' => [
        'Online',
        'Manual',
        'Walk-in Client',
    ],

    'delivery_methods' => [
        'Client Pickup',
        'Dispatch Rider',
    ],

    'review_statuses' => [
        'Pending Client Feedback',
        'Client Satisfied ✅',
        'Revision Requested 🔄',
        'Reprint Required 🔁',
        'Escalated ⚠️',
    ],

    'workflow_phases' => [
        [
            'phase' => '1 — Intake',
            'status' => 'Analyzing Job Brief',
            'responsible' => 'Customer Service',
            'permission' => 'orders.intake',
            'fields' => ['job_type', 'size_format', 'priority', 'job_image_assets', 'internal_notes'],
            'gates' => [
                'Receive and acknowledge client enquiry within 2 hours.',
                'Collect complete brief: type, size, quantity, deadline, reference files.',
                'Assign Job Order # using PB-YYYY-XXXX and log the job before work begins.',
                'Issue quote, create invoice, and collect 70% deposit before production.',
            ],
        ],
        [
            'phase' => '2 — Design',
            'status' => 'Design / Artwork Preparation',
            'responsible' => 'Designer / Customer Service',
            'permission' => 'design.update',
            'fields' => ['status', 'design_started_at', 'design_approved_by_client', 'design_approved_at', 'final_design_path', 'internal_notes'],
            'gates' => [
                'Designer confirms brief receipt and logs Design Start Date.',
                'Prepare print-ready artwork with 3mm bleed, CMYK colour, and 300dpi+ resolution.',
                'Send proof to client and save documented approval before production.',
            ],
        ],
        [
            'phase' => '3 — Production',
            'status' => 'In Production',
            'responsible' => 'Operations Manager',
            'permission' => 'production.update',
            'fields' => ['status', 'production_officer_id', 'production_started_at', 'material_substrate', 'finish_lamination', 'internal_notes'],
            'gates' => [
                'Receive approved artwork from Design and confirm print readiness.',
                'Confirm material/substrate is available or raise a purchase order.',
                'Confirm 70% payment received (or credit terms agreed) before starting production.',
                'Run a test print, complete production, and verify output quantity.',
            ],
        ],
        [
            'phase' => '4 — QC & Packaging',
            'status' => 'Quality Check & Packaging',
            'responsible' => 'Operations Manager',
            'permission' => 'qc.update',
            'fields' => ['status', 'qc_checked_by_id', 'qc_checked_at', 'qc_result', 'internal_notes'],
            'gates' => [
                'Inspect all units against the approved proof for colour, trim, finish, and quantity.',
                'If QC fails, log the issue and initiate reprint.',
                'Package approved jobs and label with client name, job number, and delivery date.',
            ],
        ],
        [
            'phase' => '5 — Delivery',
            'status' => 'Delivery In Progress',
            'responsible' => 'Customer Service',
            'permission' => 'delivery.update',
            'fields' => ['status', 'estimated_delivery_at', 'actual_delivery_at', 'delivery_method', 'dispatched_by_id', 'internal_notes'],
            'gates' => [
                'Confirm 100% payment received; for credit-terms clients, confirm balance is invoiced and due.',
                'Notify client that the job is ready or on its way.',
                'Log delivery method, dispatcher, actual delivery date, and delivered status.',
            ],
        ],
        [
            'phase' => '6 — Client Review',
            'status' => 'Client Review — Satisfactory',
            'responsible' => 'Customer Service',
            'permission' => 'client_review.update',
            'fields' => ['status', 'client_review_status', 'after_sales_action', 'after_sales_resolved_at', 'internal_notes'],
            'gates' => [
                'Follow up with client 24 hours after delivery.',
                'Close satisfied jobs, or log revision/reprint issue and owner.',
                'Resolve after-sales issue and confirm client satisfaction.',
            ],
        ],
    ],
];
