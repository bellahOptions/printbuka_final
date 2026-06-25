<?php

return [
    'services' => [
        'direct-image-printing' => [
            'name' => 'Direct Image Printing',
            'pricing_mode' => 'variable',
            'pricing_factors' => ['Paper Type', 'Paper Size', 'Paper Density'],
            'summary' => 'High-clarity direct image printing for flexible brand applications with sharp color output.',
            'hero_kicker' => 'Fast Brand Execution',
            'hero_title' => 'Direct Image Printing That Keeps Brand Quality Consistent',
            'hero_summary' => 'From one-off samples to campaign volume, we help you print with predictable color, clean finish, and dependable turnaround.',
            'proof_points' => [
                'Color-managed production workflow',
                'Pre-press file checks before output',
                'Delivery updates from confirmation to handoff',
            ],
            'features' => [
                'Full-color direct transfer output',
                'Suitable for small and bulk runs',
                'Fast turnaround for urgent jobs',
            ],
            'use_cases' => [
                'Product launches and activation kits',
                'Event collateral and campaign materials',
                'Retail and in-store branded pieces',
            ],
            'process_steps' => [
                'Share your quantity, paper options, and design direction.',
                'We review files, confirm scope, and lock production details.',
                'Production starts immediately after payment confirmation.',
            ],
            'trust_points' => [
                'Transparent pricing from super admin settings',
                'Secure order and payment tracking',
                'Dedicated support for revisions and clarifications',
            ],
        ],
        'uv-dtf' => [
            'name' => 'UV DTF',
            'pricing_mode' => 'variable',
            'pricing_factors' => ['Item Type (e.g. Pen, Notepad, Bottle)'],
            'summary' => 'Durable UV DTF transfers for branded hard-surface applications with premium finishing.',
            'hero_kicker' => 'Premium Surface Branding',
            'hero_title' => 'UV DTF Transfers Built for Professional, Long-Lasting Results',
            'hero_summary' => 'Deliver sharp branded finishes on hard surfaces with transfer quality designed to stay neat under regular handling.',
            'proof_points' => [
                'Durable transfer adhesion for daily use',
                'Fine-detail print clarity for logos and text',
                'Quality checks before dispatch',
            ],
            'features' => [
                'Scratch-resistant UV transfers',
                'Excellent adhesion on multiple surfaces',
                'Ideal for premium branding jobs',
            ],
            'use_cases' => [
                'Corporate gift and promo item branding',
                'Bottle, tumbler, and hard-surface customization',
                'Premium packaging enhancement',
            ],
            'process_steps' => [
                'Submit quantity and customization notes.',
                'We confirm print readiness and pricing.',
                'Your job is produced, inspected, and prepared for pickup or delivery.',
            ],
            'trust_points' => [
                'Consistent output across repeat runs',
                'Clear communication on timeline and status',
                'Support for both short and bulk production',
            ],
        ],
        'dtf' => [
            'name' => 'DTF',
            'pricing_mode' => 'variable',
            'pricing_factors' => ['Film Size (A6, A5, A4, A3, A2)'],
            'summary' => 'Direct-to-film transfer service for vibrant textile branding and apparel printing.',
            'hero_kicker' => 'Apparel Branding',
            'hero_title' => 'DTF Printing for Durable Fabric Branding at Scale',
            'hero_summary' => 'We help businesses and creators produce vibrant garment branding with reliable wash durability and clean finishing.',
            'proof_points' => [
                'Vibrant textile color output',
                'Production setup for repeatable quality',
                'Fast support for rush timelines',
            ],
            'features' => [
                'Vibrant color reproduction on fabric',
                'Reliable wash durability',
                'Best for uniforms and branded merch',
            ],
            'use_cases' => [
                'Team uniforms and staff wear',
                'Brand merchandise and resale apparel',
                'Campaign shirts for events and outreach',
            ],
            'process_steps' => [
                'Provide quantity, garment notes, and artwork details.',
                'We confirm file readiness and production scope.',
                'After payment, we execute printing and final quality checks.',
            ],
            'trust_points' => [
                'Predictable quality for recurring orders',
                'Secure checkout and invoice tracking',
                'Support team available through each stage',
            ],
        ],
        'dtf-borderless' => [
            'name'             => 'DTF Borderless Printing',
            'pricing_mode'     => 'variable',
            'pricing_factors'  => ['Film Size (A4, A3, A2, A1)', 'Quantity'],
            'default_price'    => 0,
            'summary'          => 'Full-surface borderless DTF transfers with no outline or frame — ideal for all-over designs, sportswear, and vibrant branded apparel.',
            'hero_kicker'      => 'All-Over Print Specialist',
            'hero_title'       => 'DTF Borderless Printing for Full-Surface Brand Expressions',
            'hero_summary'     => 'Go edge-to-edge with your designs. Our borderless DTF transfers deliver vivid, wash-fast prints across the entire film area — no white border, no compromise.',
            'proof_points'     => [
                'True edge-to-edge print coverage',
                'Wash-fast colors for lasting brand impact',
                'Consistent output across bulk runs',
            ],
            'features'         => [
                'No border or outline on transfer',
                'Full-bleed print on light and dark fabrics',
                'Vibrant CMYK color reproduction',
                'Durable wash-fast adhesion',
            ],
            'use_cases'        => [
                'All-over print apparel and fashion pieces',
                'Sportswear, activewear and team kits',
                'Colorful event merchandise and campaign shirts',
            ],
            'process_steps'    => [
                'Submit film size, quantity and your artwork file (PDF, AI, or high-res PNG).',
                'We check files, confirm scope, and lock the price before payment.',
                'Production begins immediately after payment — QC checked before dispatch.',
            ],
            'trust_points'     => [
                'Pre-press file check before every run',
                'Predictable output across repeat orders',
                'Clear timeline and status updates throughout',
            ],
        ],
        'laser-engraving' => [
            'name' => 'Laser Engraving',
            'pricing_mode' => 'variable',
            'pricing_factors' => ['Item Type (e.g. Pen, Notepad, Bottle)'],
            'summary' => 'Precision laser engraving for corporate gifts, plaques, and permanent custom markings.',
            'hero_kicker' => 'Precision Finishing',
            'hero_title' => 'Laser Engraving for Permanent, Premium Brand Impressions',
            'hero_summary' => 'Create memorable branded gifts and professional keepsakes with clean, permanent engraving on quality surfaces.',
            'proof_points' => [
                'High-precision engraving control',
                'Material-aware setup for clean finishing',
                'Detailed preview and production confirmation',
            ],
            'features' => [
                'High-precision permanent engraving',
                'Works on acrylic, wood, metal, and more',
                'Excellent for gifting and commemorative items',
            ],
            'use_cases' => [
                'Awards, plaques, and recognition pieces',
                'Corporate gift personalization',
                'Premium product branding and packaging accents',
            ],
            'process_steps' => [
                'Share material type, quantity, and engraving details.',
                'We validate specs and align on output expectations.',
                'Engraving is completed and quality-checked before dispatch.',
            ],
            'trust_points' => [
                'Skilled handling across supported materials',
                'Clear production updates and predictable delivery',
                'Business-grade finishing standards',
            ],
        ],
    ],
];
