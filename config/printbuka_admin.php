<?php

return [
    'roles' => [
        'super_admin' => ['*'],
        'management' => ['*'],
        'supervisor' => ['admin.view', 'orders.view', 'orders.verify', 'staff.view'],
        'customer_service' => ['admin.view', 'orders.view', 'orders.intake', 'invoices.manage', 'delivery.update', 'client_review.update'],
        'designer' => ['admin.view', 'orders.view', 'design.update'],
        'production' => ['admin.view', 'orders.view', 'production.update', 'packaging.update'],
        'qc' => ['admin.view', 'orders.view', 'qc.update'],
        'logistics' => ['admin.view', 'orders.view', 'delivery.update'],
    ],

    'departments' => [
        'management' => 'Management',
        'customer_service' => 'Customer Service',
        'design' => 'Design',
        'production' => 'Production',
        'quality_control' => 'Quality Control',
        'logistics' => 'Logistics',
    ],

    'job_statuses' => [
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

    'payment_statuses' => [
        'Awaiting Invoice',
        'Invoice Issued',
        'Pending Payment',
        'Invoice Settled (70%)',
        'Invoice Settled (100%)',
    ],

    'priorities' => [
        '🔴 Urgent',
        '🟡 Normal',
        '🟢 Low',
    ],

    'delivery_methods' => [
        'Client Pickup',
        'Dispatch Rider',
        'In-House Delivery',
    ],

    'review_statuses' => [
        'Pending Client Feedback',
        'Client Satisfied ✅',
        'Revision Requested 🔄',
        'Reprint Required 🔁',
        'Escalated ⚠️',
    ],
];
