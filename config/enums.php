<?php
return [
    'user_status' => [
        'Inactive' => '0',
        'Active' => '1',
        'Deleted' => '2',
    ],

    'ticket_status' => [
        'Disabled' => '0',
        'Opened' => '1',
        'Closed' => '2',
    ],

    'stream_status' => [
        'Disabled' => '0',
        'PMO' => '1',
        'Tester' => '2',
        'Analyst' => '3',
        'QA' => '4',
        'HOD' => '5',
        'iOS_Tester' => '6',
        'Android_Tester' => '7',
        'Remediation_PMO' => '8',
        'Remediation_Officer' => '9',
        'Signed_Off' => '10',
        'Closed' => '11',
    ],

    'severity_status' => [
        null => '0',
        'Informational' => '1',
        'Low' => '2',
        'Medium' => '3',
        'High' => '4',
        'Critical' => '5',
    ],

    'active_status' => [
        'Inactive' => '0',
        'Active' => '1',
    ],

    'mdb_stream_status' => [
        'Open' => '0',
        'Close' => '1',
        'Revalidation' => '2',
        'Exception' => '3',
        'FP' => '4',
    ],

    'default_avatar_img' => [
        'img_filename' => 'avatar.jpg',
    ],

    'opco_switch' => [
        'AG' => '1',
        'CoE' => '2',
        'ADS' => '3',
        'Axiata' => '4',
        'ECo' => '5',
        'XL' => '6',
        'NCELL' => '7',
        'SMART' => '8',
        'Dialog' => '9',
        'Robi' => '10',
        'Celcom' => '11',
    ],
    'vuln_category' => [
        'Broken Access Control' => '1',
        'Broken Authentication' => '2',
        'Injection' => '3',
        'Patch Management' => '4',
        'Security Misconfiguration' => '5',
        'Sensitive Data Exposure' => '6',
        'System Development and Maintenance' => '7',
        'Unprotected Services' => '8',
        'Weak Cryptography' => '9',
    ],
    'Category' => [
        'IDP' => '1',
        'External' => '2',
    ],
    'opco_status' => [
        'Disabled' => '0',
        'Primary' => '1',
        'Secondary' => '2',
    ],
];

//how to use?
//adding this line of code "Config::get('enums.stream_statues.2')"
//the "2" means getting "Tester" from stream_statues
