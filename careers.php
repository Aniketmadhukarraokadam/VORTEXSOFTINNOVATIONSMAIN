<?php
/**
 * Vortexsoft Innovations — Careers & Corporate Job Openings (careers.php)
 * Clean, Professional, Multi-Column Careers Portal
 * Active Openings: Java Developer, Python Developer, Data Engineer, HR Executive, 
 * Associate Process Analyst, Title Search Executive, BPO Executive, KPO Analyst, Medical Coder
 */

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$page_title    = 'Careers & Job Openings | Vortexsoft Innovations Private Limited';
$page_desc     = 'Explore corporate career opportunities at Vortexsoft Innovations. Join our engineering, operations, US title, healthcare, and analytics teams in Pune, Bengaluru & Remote.';
$canonical_url = SITE_URL . '/careers.php';
$prefix        = './';

// Fetch jobs with fast file-based cache for maximum page speed (0ms DB delay)
$jobs       = [];
$cache_file = __DIR__ . '/config/jobs_cache.json';
$cache_ttl  = 1800; // 30 minutes

if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_ttl)) {
    $cached = @json_decode(@file_get_contents($cache_file), true);
    if (is_array($cached) && !empty($cached)) {
        // Verify cache does not contain obsolete internship titles
        $has_old = false;
        foreach ($cached as $cj) {
            if (stripos($cj['title'] ?? '', 'intern') !== false || stripos($cj['type'] ?? '', 'intern') !== false) {
                $has_old = true;
                break;
            }
        }
        if (!$has_old) {
            $jobs = $cached;
        } else {
            @unlink($cache_file);
        }
    }
}

if (empty($jobs)) {
    try {
        $db = getDB();
        if ($db) {
            // Automatically purge obsolete internship postings from database
            try {
                $db->exec("DELETE FROM jobs WHERE title LIKE '%intern%' OR type LIKE '%intern%' OR description LIKE '%internship%'");
            } catch (Throwable $t) {}

            // If table is empty, auto-seed the 9 official corporate openings
            $existing_count = (int)$db->query("SELECT COUNT(*) FROM jobs WHERE is_active=1")->fetchColumn();
            if ($existing_count === 0) {
                $seed_stmt = $db->prepare("INSERT INTO jobs (title, department, type, location, experience_range, skills_json, description, is_urgent, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)");
                $seed_rows = [
                    ['Java Developer', 'IT & Software', 'Full Time', 'Bengaluru / Pune / Remote', '2–5 years', 'Java, Spring Boot, Microservices, Hibernate, REST APIs, SQL, Docker', 'Design, build, and maintain robust enterprise backend microservices and RESTful APIs using modern Java and Spring Boot. Optimize high-volume database queries and collaborate on cloud-native deployments.', 1, 1],
                    ['Python Developer', 'IT & Software', 'Full Time', 'Bengaluru / Remote', '2–5 years', 'Python, Django, FastAPI, PostgreSQL, REST APIs, Celery, Docker', 'Develop scalable backend applications, automated data pipelines, and high-performance REST APIs. Collaborate with product teams to build clean architectures with Python, FastAPI, and PostgreSQL.', 1, 2],
                    ['Data Engineer', 'IT & Software', 'Full Time', 'Bengaluru / Remote', '2–5 years', 'Python, SQL, Apache Spark, Airflow, ETL Pipelines, Snowflake, AWS', 'Design, construct, and operationalize resilient batch and real-time ETL pipelines. Manage data ingestion, transformation workflows, and cloud data warehouses ensuring high data reliability.', 1, 3],
                    ['HR Executive', 'Human Resources', 'Full Time', 'Pune / Bengaluru', '1–4 years', 'Talent Acquisition, IT Recruitment, Onboarding, Employee Engagement, HR Operations', 'Manage end-to-end talent acquisition for engineering, AI, and BPO divisions. Coordinate interviews, campus drives, new hire onboarding, HR policies, and employee engagement programs.', 0, 4],
                    ['Associate Process Analyst', 'Business Operations', 'Full Time', 'Pune / Bengaluru', '1–3 years', 'Process Mapping, Data Analysis, MIS Reporting, Workflow Optimization, Advanced Excel', 'Analyze business operations workflows, map process lifecycles, and identify operational bottlenecks. Create comprehensive MIS reporting dashboards and optimize client turnaround times.', 0, 5],
                    ['Title Search Executive', 'Real Estate BPO', 'Full Time', 'Pune HQ', '1–4 years', '40-Year Title Searches, Deed Chains, Mortgage Liens, Tax Searches, SoftPro, Qualia', 'Conduct detailed title searches and examinations on US county property records. Verify ownership deed chains, identify open liens and taxes, and prepare commitment packages for US title settlement clients.', 1, 6],
                    ['BPO Executive', 'Customer Operations', 'Full Time', 'Pune / Bengaluru', '0–3 years', 'Voice & Non-Voice Support, Customer Engagement, CRM Systems, Email Support, Active Listening', 'Provide first-line client communication and transactional support across inbound, outbound, and digital channels. Resolve customer inquiries with precision while maintaining stellar SLA metrics.', 0, 7],
                    ['KPO Analyst', 'Research & Analytics', 'Full Time', 'Bengaluru / Pune', '1–4 years', 'Market Research, Financial Analysis, Business Intelligence, Data Synthesis, Advanced Excel', 'Deliver high-value secondary research, industry benchmarking, and financial analysis for global clients. Synthesize complex data sets into actionable presentation decks and strategic intelligence reports.', 0, 8],
                    ['Medical Coder', 'Healthcare BPO', 'Full Time', 'Bengaluru / Remote', '1–4 years', 'CPC / COC Certified, ICD-10-CM, CPT-4, HCPCS, E&M Coding, HIPAA Compliance', 'Review clinical documentation and provider charts to assign accurate diagnostic and procedural codes according to US healthcare standards. Maintain >96% accuracy and ensure strict HIPAA adherence.', 1, 9],
                ];
                foreach ($seed_rows as $sr) {
                    $seed_stmt->execute($sr);
                }
            }

            $rows = $db->query("SELECT id, title, department, type, location, experience_range, skills_json, description, is_urgent FROM jobs WHERE is_active=1 ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $dept_lower  = strtolower($r['department'] ?? '');
                $title_lower = strtolower($r['title'] ?? '');

                if (str_contains($dept_lower, 'it') || str_contains($dept_lower, 'software') || str_contains($title_lower, 'developer') || str_contains($title_lower, 'engineer')) {
                    $cat = 'IT & Software';
                } elseif (str_contains($dept_lower, 'real estate') || str_contains($title_lower, 'title')) {
                    $cat = 'Real Estate BPO';
                } elseif (str_contains($dept_lower, 'health') || str_contains($title_lower, 'medical')) {
                    $cat = 'Healthcare BPO';
                } elseif (str_contains($dept_lower, 'customer') || str_contains($title_lower, 'bpo')) {
                    $cat = 'Customer & BPO';
                } elseif (str_contains($dept_lower, 'research') || str_contains($title_lower, 'kpo')) {
                    $cat = 'Research & KPO';
                } elseif (str_contains($dept_lower, 'business') || str_contains($dept_lower, 'operation') || str_contains($title_lower, 'process')) {
                    $cat = 'Business Operations';
                } elseif (str_contains($dept_lower, 'human') || str_contains($dept_lower, 'hr') || str_contains($title_lower, 'hr')) {
                    $cat = 'Corporate & HR';
                } else {
                    $cat = $r['department'] ?: 'General';
                }

                $jobs[] = [
                    'id'         => (int)$r['id'],
                    'title'      => $r['title'],
                    'department' => $r['department'],
                    'category'   => $cat,
                    'type'       => $r['type'],
                    'location'   => $r['location'],
                    'exp'        => $r['experience_range'] ?? '',
                    'skills'     => array_values(array_filter(array_map('trim', explode(',', $r['skills_json'] ?? '')))),
                    'desc'       => $r['description'],
                    'urgent'     => (bool)$r['is_urgent'],
                ];
            }
            if (!empty($jobs)) {
                @file_put_contents($cache_file, json_encode($jobs, JSON_UNESCAPED_UNICODE));
            }
        }
    } catch (Throwable $e) { 
        $jobs = []; 
    }
}

// Clean fallback — 9 curated corporate positions with small, precise titles
if (empty($jobs)) {
    $jobs = [
      [
        'id'         => 1,
        'title'      => 'Java Developer',
        'department' => 'IT & Software',
        'category'   => 'IT & Software',
        'type'       => 'Full Time',
        'location'   => 'Bengaluru / Pune / Remote',
        'exp'        => '2–5 years',
        'skills'     => ['Java', 'Spring Boot', 'Microservices', 'Hibernate', 'REST APIs', 'SQL', 'Docker'],
        'desc'       => 'Design, build, and maintain robust enterprise backend microservices and RESTful APIs using modern Java and Spring Boot. Optimize high-volume database queries and collaborate on cloud-native deployments.',
        'urgent'     => true,
      ],
      [
        'id'         => 2,
        'title'      => 'Python Developer',
        'department' => 'IT & Software',
        'category'   => 'IT & Software',
        'type'       => 'Full Time',
        'location'   => 'Bengaluru / Remote',
        'exp'        => '2–5 years',
        'skills'     => ['Python', 'Django', 'FastAPI', 'PostgreSQL', 'REST APIs', 'Celery', 'Docker'],
        'desc'       => 'Develop scalable backend applications, automated data pipelines, and high-performance REST APIs. Collaborate with product teams to build clean architectures with Python, FastAPI, and PostgreSQL.',
        'urgent'     => true,
      ],
      [
        'id'         => 3,
        'title'      => 'Data Engineer',
        'department' => 'IT & Software',
        'category'   => 'IT & Software',
        'type'       => 'Full Time',
        'location'   => 'Bengaluru / Remote',
        'exp'        => '2–5 years',
        'skills'     => ['Python', 'SQL', 'Apache Spark', 'Airflow', 'ETL Pipelines', 'Snowflake', 'AWS'],
        'desc'       => 'Design, construct, and operationalize resilient batch and real-time ETL pipelines. Manage data ingestion, transformation workflows, and cloud data warehouses ensuring high data reliability.',
        'urgent'     => true,
      ],
      [
        'id'         => 4,
        'title'      => 'HR Executive',
        'department' => 'Human Resources',
        'category'   => 'Corporate & HR',
        'type'       => 'Full Time',
        'location'   => 'Pune / Bengaluru',
        'exp'        => '1–4 years',
        'skills'     => ['Talent Acquisition', 'IT Recruitment', 'Onboarding', 'Employee Engagement', 'HR Operations'],
        'desc'       => 'Manage end-to-end talent acquisition for engineering, AI, and BPO divisions. Coordinate interviews, campus drives, new hire onboarding, HR policies, and employee engagement programs.',
        'urgent'     => false,
      ],
      [
        'id'         => 5,
        'title'      => 'Associate Process Analyst',
        'department' => 'Business Operations',
        'category'   => 'Business Operations',
        'type'       => 'Full Time',
        'location'   => 'Pune / Bengaluru',
        'exp'        => '1–3 years',
        'skills'     => ['Process Mapping', 'Data Analysis', 'MIS Reporting', 'Workflow Optimization', 'Advanced Excel'],
        'desc'       => 'Analyze business operations workflows, map process lifecycles, and identify operational bottlenecks. Create comprehensive MIS reporting dashboards and optimize client turnaround times.',
        'urgent'     => false,
      ],
      [
        'id'         => 6,
        'title'      => 'Title Search Executive',
        'department' => 'Real Estate BPO',
        'category'   => 'Real Estate BPO',
        'type'       => 'Full Time',
        'location'   => 'Pune HQ',
        'exp'        => '1–4 years',
        'skills'     => ['40-Year Title Searches', 'Deed Chains', 'Mortgage Liens', 'Tax Searches', 'SoftPro', 'Qualia'],
        'desc'       => 'Conduct detailed title searches and examinations on US county property records. Verify ownership deed chains, identify open liens and taxes, and prepare commitment packages for US title settlement clients.',
        'urgent'     => true,
      ],
      [
        'id'         => 7,
        'title'      => 'BPO Executive',
        'department' => 'Customer Operations',
        'category'   => 'Customer & BPO',
        'type'       => 'Full Time',
        'location'   => 'Pune / Bengaluru',
        'exp'        => '0–3 years',
        'skills'     => ['Voice & Non-Voice Support', 'Customer Engagement', 'CRM Systems', 'Email Support', 'Active Listening'],
        'desc'       => 'Provide first-line client communication and transactional support across inbound, outbound, and digital channels. Resolve customer inquiries with precision while maintaining stellar SLA metrics.',
        'urgent'     => false,
      ],
      [
        'id'         => 8,
        'title'      => 'KPO Analyst',
        'department' => 'Research & Analytics',
        'category'   => 'Research & KPO',
        'type'       => 'Full Time',
        'location'   => 'Bengaluru / Pune',
        'exp'        => '1–4 years',
        'skills'     => ['Market Research', 'Financial Analysis', 'Business Intelligence', 'Data Synthesis', 'Advanced Excel'],
        'desc'       => 'Deliver high-value secondary research, industry benchmarking, and financial analysis for global clients. Synthesize complex data sets into actionable presentation decks and strategic intelligence reports.',
        'urgent'     => false,
      ],
      [
        'id'         => 9,
        'title'      => 'Medical Coder',
        'department' => 'Healthcare BPO',
        'category'   => 'Healthcare BPO',
        'type'       => 'Full Time',
        'location'   => 'Bengaluru / Remote',
        'exp'        => '1–4 years',
        'skills'     => ['CPC / COC Certified', 'ICD-10-CM', 'CPT-4', 'HCPCS', 'E&M Coding', 'HIPAA Compliance'],
        'desc'       => 'Review clinical documentation and provider charts to assign accurate diagnostic and procedural codes according to US healthcare standards. Maintain >96% accuracy and ensure strict HIPAA adherence.',
        'urgent'     => true,
      ],
    ];
}

// Build unique categories for filter tabs
$ordered_categories = ['All', 'IT & Software', 'Real Estate BPO', 'Healthcare BPO', 'Customer & BPO', 'Research & KPO', 'Business Operations', 'Corporate & HR'];
$available_categories = array_unique(array_column($jobs, 'category'));
$categories = [];
foreach ($ordered_categories as $c) {
    if ($c === 'All' || in_array($c, $available_categories, true)) {
        $categories[] = $c;
    }
}
foreach ($available_categories as $c) {
    if (!in_array($c, $categories, true)) {
        $categories[] = $c;
    }
}

// Check if a specific job was requested for deep-linking/sharing
$shared_job_id = isset($_GET['job']) ? (int)$_GET['job'] : 0;
$shared_job = null;
if ($shared_job_id > 0) {
    foreach ($jobs as $jb) {
        if ((int)$jb['id'] === $shared_job_id) {
            $shared_job = $jb;
            break;
        }
    }
}

if ($shared_job) {
    $page_title    = $shared_job['title'] . ' (' . $shared_job['location'] . ') | Vortexsoft Innovations Careers';
    $page_desc     = 'Apply for ' . $shared_job['title'] . ' at Vortexsoft Innovations Private Limited. Location: ' . $shared_job['location'] . '. ISO 27001 Certified Global IT & BPO Leader.';
    $canonical_url = SITE_URL . '/careers.php?job=' . $shared_job['id'];
}

// Build Google for Jobs JobPosting Schema.org objects
$job_postings_schema = [];
foreach ($jobs as $jb) {
    $loc_lower = strtolower($jb['location']);
    
    // Address mapping (Pune Corporate HQ vs Bengaluru Tech Delivery Center)
    if (str_contains($loc_lower, 'pune') && !str_contains($loc_lower, 'bengaluru')) {
        $address = [
            '@type' => 'PostalAddress',
            'streetAddress' => '502, 4th Floor, Dangat Patil Empire, Kudale Baug, Vadgaon Budruk',
            'addressLocality' => 'Pune',
            'addressRegion' => 'Maharashtra',
            'postalCode' => '411041',
            'addressCountry' => 'IN'
        ];
    } else {
        $address = [
            '@type' => 'PostalAddress',
            'streetAddress' => 'No.125, Ranganath Complex, Madiwala, HSR Layout 5th Sector',
            'addressLocality' => 'Bengaluru',
            'addressRegion' => 'Karnataka',
            'postalCode' => '560068',
            'addressCountry' => 'IN'
        ];
    }
    
    $job_url = 'https://www.vortexsoftinnovations.com/careers.php?job=' . $jb['id'];

    // Experience requirements parsed to months
    $exp_months = 24;
    if (preg_match('/(\d+)\s*[–-]\s*(\d+)/u', $jb['exp'], $exp_match)) {
        $exp_months = (int)$exp_match[1] * 12;
    } elseif (preg_match('/(\d+)/', $jb['exp'], $exp_match)) {
        $exp_months = (int)$exp_match[1] * 12;
    }

    $emp_type = 'FULL_TIME';
    
    // Clean, lightweight description for Google for Jobs (saves ~50KB page payload)
    $clean_desc = htmlspecialchars($jb['title']) . ' at Vortexsoft Innovations Private Limited. ' .
        htmlspecialchars($jb['desc']) . ' Engagement: ' . htmlspecialchars($jb['type']) . 
        '. Department: ' . htmlspecialchars($jb['department']) . 
        '. Experience: ' . htmlspecialchars($jb['exp'] ?: 'Freshers / Experienced') . 
        '. Location: ' . htmlspecialchars($jb['location']) . 
        '. Apply online: ' . $job_url;

    $posting = [
        '@type' => 'JobPosting',
        '@id' => $job_url . '#jobposting',
        'title' => $jb['title'],
        'description' => $clean_desc,
        'identifier' => [
            '@type' => 'PropertyValue',
            'name' => 'Vortexsoft Innovations Private Limited',
            'value' => 'VSI-JOB-' . str_pad((string)$jb['id'], 3, '0', STR_PAD_LEFT)
        ],
        'datePosted' => '2026-10-25T08:00:00+05:30',
        'validThrough' => '2027-12-31T23:59:59+05:30',
        'employmentType' => $emp_type,
        'industry' => $jb['department'],
        'hiringOrganization' => [
            '@type' => 'Organization',
            'name' => 'Vortexsoft Innovations Private Limited',
            'sameAs' => 'https://www.vortexsoftinnovations.com',
            'logo' => 'https://www.vortexsoftinnovations.com/logo-header.png'
        ],
        'jobLocation' => [
            '@type' => 'Place',
            'address' => $address
        ],
        'experienceRequirements' => [
            '@type' => 'OccupationalExperienceRequirements',
            'monthsOfExperience' => $exp_months
        ],
        'educationRequirements' => [
            '@type' => 'EducationalOccupationalCredential',
            'credentialCategory' => 'bachelor degree'
        ],
        'directApply' => true,
        'url' => $job_url
    ];

    if (str_contains($loc_lower, 'remote')) {
        $posting['jobLocationType'] = 'TELECOMMUTE';
        $posting['applicantLocationRequirements'] = [
            '@type' => 'Country',
            'name' => 'India'
        ];
    }

    $job_postings_schema[] = $posting;
}

// Candidate & Recruitment FAQPage Schema
$careers_faq_schema = [
    '@type' => 'FAQPage',
    '@id' => 'https://www.vortexsoftinnovations.com/careers.php#faq',
    'mainEntity' => [
        [
            '@type' => 'Question',
            'name' => 'What career openings and technical positions are available at Vortexsoft Innovations?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Vortexsoft Innovations is actively hiring across multiple key domains including Java Developers, Python Developers, Data Engineers, US Title Search Executives, Healthcare Medical Coders, BPO Executives, KPO Analysts, HR Executives, and Associate Process Analysts.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'How do I apply for job openings at Vortexsoft Innovations?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'You can apply directly online through our Careers portal at https://www.vortexsoftinnovations.com/careers.php by clicking "Apply Now" on any vacancy. Alternatively, email your resume to careers@vortexsoftinnovations.com or connect with our recruitment team on WhatsApp at +91-8308906690.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'What is the recruitment and interview process at Vortexsoft Innovations?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Our transparent hiring process follows 4 clear steps: 1) Application screening within 48 business hours; 2) Practical skill assessment or coding evaluation; 3) Technical discussion and managerial interview; 4) Formal offer letter rollout and smooth digital onboarding.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'Does Vortexsoft Innovations offer Work-From-Home (WFH) or hybrid jobs?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Yes! Most software engineering, data engineering, and analytics roles support hybrid and 100% remote work arrangements across India. Core title abstracting and specialized operations operate from our high-security facilities in Pune and Bengaluru.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'What Title Search and Abstracting openings are available at Vortexsoft\'s Pune HQ?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Vortexsoft operates India\'s premier Title Abstracting delivery center at our Pune Corporate Headquarters (Vadgaon Budruk). We actively hire US Title Search & Settlement Executives to examine nationwide county property records, deeds, open mortgages, and prepare commitment packages for 40+ US title settlement clients.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'Where are the corporate offices and tech centers of Vortexsoft Innovations located?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Vortexsoft Innovations is headquartered in Pune (502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk, Pune 411041), maintains an engineering delivery center in Bengaluru (No.125, Ranganath Complex, Madiwala, HSR Layout, Bengaluru 560068), and holds a corporate entity in Sheridan, Wyoming, USA.'
            ]
        ]
    ]
];

// Combine Google for Jobs and FAQPage into $extra_head JSON-LD script tag
if ($shared_job) {
    $matched_posting = null;
    $other_postings = [];
    foreach ($job_postings_schema as $p) {
        if ($p['url'] === $canonical_url) {
            $matched_posting = $p;
        } else {
            $other_postings[] = $p;
        }
    }
    if ($matched_posting) {
        $schema_graph = array_merge([$matched_posting], $other_postings, [$careers_faq_schema]);
    } else {
        $schema_graph = array_merge($job_postings_schema, [$careers_faq_schema]);
    }
} else {
    $schema_graph = array_merge($job_postings_schema, [$careers_faq_schema]);
}

$extra_head = '<script type="application/ld+json">' . "\n" .
    json_encode(['@context' => 'https://schema.org', '@graph' => $schema_graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n" .
    '</script>' . "\n";

$og_image = 'https://www.vortexsoftinnovations.com/assets/images/vortexsoft-careers-share.png?v=20261027';
$page_keywords = 'Vortexsoft careers, Java developer jobs Bangalore, Python developer Pune, Data Engineer jobs India, US title search executive Pune, medical coding CPC jobs India, BPO executive jobs, KPO analyst, remote software engineer vacancies';

require_once __DIR__ . '/includes/header.php';
?>

<style>
/* ── Modern Careers Design System ────────────────────────────── */
:root {
  --vsi-navy-dark: #080b1c;
  --vsi-navy-primary: #1C2280;
  --vsi-navy-light: #2a33ad;
  --vsi-red: #CC2228;
  --vsi-emerald: #059669;
  --vsi-amber: #d97706;
  --vsi-purple: #7c3aed;
  --vsi-slate-900: #0f172a;
  --vsi-slate-700: #334155;
  --vsi-slate-500: #64748b;
  --vsi-slate-200: #e2e8f0;
  --vsi-slate-100: #f1f5f9;
  --vsi-slate-50: #f8fafc;
}

/* Hero Section */
.careers-hero {
  background: linear-gradient(135deg, #070919 0%, #111738 50%, #1b2152 100%);
  padding: 85px 0 75px;
  position: relative;
  overflow: hidden;
  color: #fff;
}
.careers-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: 
    radial-gradient(circle at 20% 30%, rgba(99, 102, 241, 0.15) 0%, transparent 45%),
    radial-gradient(circle at 80% 70%, rgba(204, 34, 40, 0.12) 0%, transparent 40%),
    linear-gradient(rgba(255,255,255,.02) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,.02) 1px, transparent 1px);
  background-size: 100% 100%, 100% 100%, 40px 40px, 40px 40px;
  pointer-events: none;
}
.hero-tag-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.18);
  backdrop-filter: blur(8px);
  color: #e2e8f0;
  padding: 6px 16px;
  border-radius: 100px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 20px;
}
.hero-headline {
  font-family: 'Poppins', sans-serif;
  font-weight: 800;
  font-size: clamp(2.2rem, 4.5vw, 3.4rem);
  line-height: 1.15;
  color: #ffffff;
  margin-bottom: 18px;
  letter-spacing: -0.5px;
}
.hero-headline .text-gradient {
  background: linear-gradient(135deg, #818cf8 0%, #c084fc 50%, #f472b6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.hero-subtext {
  font-size: 16px;
  line-height: 1.7;
  color: #cbd5e1;
  max-width: 680px;
  margin-bottom: 30px;
}

/* Hero Stats Bar */
.hero-stats-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-top: 36px;
  max-width: 860px;
}
.hero-stat-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 16px 20px;
  backdrop-filter: blur(10px);
}
.hero-stat-card .stat-num {
  font-family: 'Poppins', sans-serif;
  font-weight: 800;
  font-size: 24px;
  color: #ffffff;
  line-height: 1.2;
}
.hero-stat-card .stat-label {
  font-size: 12.5px;
  color: #94a3b8;
  font-weight: 500;
  margin-top: 4px;
}

/* Control Hub: Search & Filters */
.careers-control-hub {
  background: #ffffff;
  border-radius: 20px;
  padding: 24px 28px;
  margin-top: -35px;
  position: relative;
  z-index: 10;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
  border: 1px solid #e2e8f0;
}
.search-input-wrap {
  position: relative;
  flex: 1 1 320px;
}
.search-input-wrap i {
  position: absolute;
  left: 18px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 16px;
}
.search-input-wrap input {
  width: 100%;
  padding: 13px 18px 13px 48px;
  border-radius: 12px;
  border: 1.5px solid #cbd5e1;
  font-size: 14.5px;
  background: #f8fafc;
  color: #0f172a;
  transition: all 0.2s ease;
  outline: none;
}
.search-input-wrap input:focus {
  border-color: #4f46e5;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}

/* Category Filter Tabs */
.filter-tabs-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 18px;
  padding-top: 16px;
  border-top: 1px solid #f1f5f9;
}
.cat-pill-btn {
  background: #f8fafc;
  border: 1.5px solid #e2e8f0;
  color: #475569;
  font-family: 'Poppins', sans-serif;
  font-size: 13px;
  font-weight: 600;
  padding: 8px 18px;
  border-radius: 100px;
  cursor: pointer;
  transition: all 0.2s ease;
  outline: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.cat-pill-btn:hover {
  background: #f1f5f9;
  color: #1C2280;
  border-color: #cbd5e1;
}
.cat-pill-btn.active {
  background: #1C2280;
  color: #ffffff;
  border-color: #1C2280;
  box-shadow: 0 4px 12px rgba(28, 34, 128, 0.25);
}
.cat-count {
  font-size: 11px;
  padding: 1px 7px;
  border-radius: 100px;
  background: rgba(0, 0, 0, 0.06);
}
.cat-pill-btn.active .cat-count {
  background: rgba(255, 255, 255, 0.22);
  color: #fff;
}

/* ── Job Openings Multi-Column Layout ───────────────────────── */
.jobs-stack-section {
  padding: 50px 0 75px;
  background: #f8fafc;
}
.job-cards-container {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}
@media (max-width: 1199px) {
  .job-cards-container {
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
}
@media (max-width: 767px) {
  .job-cards-container {
    grid-template-columns: 1fr;
    gap: 16px;
  }
}

.job-card-wrapper {
  height: 100%;
  display: flex;
  flex-direction: column;
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.job-card-wrapper.d-none {
  display: none !important;
}

.job-modern-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1.5px solid #e2e8f0;
  padding: 24px;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  box-shadow: 0 3px 12px rgba(15, 23, 42, 0.04);
  display: flex;
  flex-direction: column;
  height: 100%;
}
.job-modern-card:hover {
  border-color: #c7d2fe;
  transform: translateY(-4px);
  box-shadow: 0 16px 32px rgba(28, 34, 128, 0.09);
}
.job-modern-card.card-regular {
  border-top: 4px solid #1C2280;
}
.job-modern-card.card-urgent {
  border-top: 4px solid #CC2228;
}

/* Card Header Badges */
.card-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-bottom: 12px;
}
.job-role-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 19px;
  color: #0f172a;
  margin: 0 0 10px;
  line-height: 1.3;
}
.job-role-title a {
  color: inherit;
  text-decoration: none;
  transition: color 0.15s;
}
.job-role-title a:hover {
  color: #1C2280;
}

/* Tags & Badges */
.card-badges-wrap {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 6px;
}
.job-chip {
  font-size: 11.5px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.chip-fulltime {
  background: #e0e7ff;
  color: #3730a3;
  border: 1px solid #c7d2fe;
}
.chip-dept {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
}
.chip-urgent {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fca5a5;
  animation: pulse-urgent 2s infinite;
}
@keyframes pulse-urgent {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.75; }
}

/* Location & Experience Meta */
.card-meta-row {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 13px;
  color: #64748b;
  margin-bottom: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid #f1f5f9;
}
.card-meta-row span {
  display: inline-flex;
  align-items: center;
  gap: 7px;
}
.card-meta-row i {
  font-size: 13px;
}

/* Description & Skills */
.card-desc-text {
  font-size: 13.5px;
  color: #475569;
  line-height: 1.6;
  margin-bottom: 14px;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.card-skills-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 5px;
  margin-bottom: 18px;
}
.skill-pill {
  background: #f8fafc;
  color: #334155;
  border: 1px solid #e2e8f0;
  font-size: 11.5px;
  font-weight: 500;
  padding: 3px 8px;
  border-radius: 6px;
}

/* Bottom Action Row (Pinned to bottom of card) */
.card-actions-row {
  margin-top: auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding-top: 14px;
  border-top: 1px solid #f1f5f9;
}
.btn-vsi-apply {
  background: linear-gradient(135deg, #1C2280 0%, #2932aa 100%);
  color: #ffffff;
  border: none;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 13px;
  padding: 8px 18px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  text-decoration: none;
}
.btn-vsi-apply:hover {
  background: linear-gradient(135deg, #13175c 0%, #1C2280 100%);
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(28, 34, 128, 0.25);
}
.btn-vsi-share {
  background: #ffffff;
  color: #475569;
  border: 1.5px solid #cbd5e1;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 12.5px;
  padding: 7px 14px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
.btn-vsi-share:hover {
  border-color: #1C2280;
  color: #1C2280;
  background: #f8fafc;
}

/* Empty State */
.empty-jobs-box {
  background: #ffffff;
  border-radius: 16px;
  border: 1.5px dashed #cbd5e1;
  padding: 48px 24px;
  text-align: center;
  margin-top: 20px;
}

/* Why Join Us Grid */
.why-join-section {
  padding: 70px 0;
  background: #ffffff;
  border-top: 1px solid #e2e8f0;
}
.section-badge-center {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #1e40af;
  font-size: 12.5px;
  font-weight: 700;
  padding: 4px 14px;
  border-radius: 100px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 12px;
}
.section-main-heading {
  font-family: 'Poppins', sans-serif;
  font-weight: 800;
  font-size: clamp(1.8rem, 3.5vw, 2.5rem);
  color: #0f172a;
  margin-bottom: 14px;
}
.section-sub-heading {
  font-size: 15.5px;
  color: #64748b;
  max-width: 650px;
  margin: 0 auto 40px;
  line-height: 1.6;
}
.perk-card {
  background: #f8fafc;
  border: 1.5px solid #e2e8f0;
  border-radius: 16px;
  padding: 28px 24px;
  height: 100%;
  transition: all 0.25s ease;
}
.perk-card:hover {
  background: #ffffff;
  border-color: #c7d2fe;
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(28, 34, 128, 0.06);
}
.perk-icon-wrap {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  margin-bottom: 18px;
}
.perk-title {
  font-family: 'Poppins', sans-serif;
  font-size: 17.5px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 8px;
}
.perk-desc {
  font-size: 13.5px;
  color: #64748b;
  line-height: 1.6;
  margin: 0;
}

/* Why Work With Us Section (Positioned at Bottom of Page) */
.why-work-section {
  padding: 80px 0;
  background: #ffffff;
  border-top: 1px solid #e2e8f0;
}
.ready-to-apply-box {
  background: linear-gradient(135deg, #090d24 0%, #151a44 50%, #1C2280 100%);
  border-radius: 20px;
  padding: 44px 38px;
  border: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow: 0 15px 35px rgba(28, 34, 128, 0.14);
  position: relative;
  overflow: hidden;
  margin-top: 40px;
}
.ready-to-apply-box::before {
  content: '';
  position: absolute;
  top: -60px;
  right: -60px;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.22) 0%, transparent 70%);
  pointer-events: none;
}
.apply-contact-card {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: 12px;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  gap: 14px;
  text-decoration: none !important;
  transition: all 0.2s ease;
  backdrop-filter: blur(8px);
}
.apply-contact-card:hover {
  background: rgba(255, 255, 255, 0.16);
  border-color: rgba(255, 255, 255, 0.35);
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
}
.apply-icon-wrap {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 19px;
  flex-shrink: 0;
}

/* Hiring Process Timeline */
.process-section {
  padding: 70px 0;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}
.process-step-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1.5px solid #e2e8f0;
  padding: 26px 22px;
  height: 100%;
  position: relative;
}
.process-step-num {
  font-family: 'Poppins', sans-serif;
  font-weight: 800;
  font-size: 13px;
  color: #1C2280;
  background: #e0e7ff;
  width: 32px;
  height: 32px;
  border-radius: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 14px;
}

/* Candidate FAQ Section */
.faq-accordion-section {
  padding: 70px 0;
  background: #ffffff;
  border-top: 1px solid #e2e8f0;
}
.accordion-custom-btn {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 15.5px;
  color: #0f172a !important;
  background: #f8fafc !important;
  padding: 18px 24px;
  border-radius: 12px !important;
  border: 1px solid #e2e8f0 !important;
}
.accordion-custom-btn:not(.collapsed) {
  background: #eef2ff !important;
  color: #1C2280 !important;
  border-color: #c7d2fe !important;
}
.accordion-custom-body {
  padding: 20px 24px;
  font-size: 14.5px;
  line-height: 1.75;
  color: #475569;
}

/* Bottom CTA / WhatsApp Box */
.cta-banner-box {
  background: linear-gradient(135deg, #1C2280 0%, #0d1245 100%);
  border-radius: 20px;
  padding: 40px 36px;
  color: #ffffff;
  margin-top: 40px;
}

@media (max-width: 991.98px) {
  .filter-tabs-row {
    display: flex !important;
    flex-wrap: nowrap !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
    scrollbar-width: none !important;
    padding-bottom: 8px !important;
  }
  .filter-tabs-row::-webkit-scrollbar { display: none; }
  .cat-pill-btn { white-space: nowrap !important; flex-shrink: 0 !important; }
  .job-modern-card { padding: 20px 22px; }
}

@media (max-width: 767.98px) {
  .careers-hero { padding: 50px 0 42px; }
  .careers-control-hub { padding: 18px 16px; margin-top: -24px; border-radius: 16px; }
  .card-top-row { flex-direction: column; }
  .card-actions-row { justify-content: stretch; flex-direction: column-reverse; gap: 8px; }
  .btn-vsi-apply, .btn-vsi-share { width: 100%; justify-content: center; padding: 11px 16px; }
  .ready-to-apply-box { padding: 28px 20px; border-radius: 16px; }
  .apply-contact-card { width: 100%; }
}

@media (max-width: 575.98px) {
  .careers-hero { padding: 42px 0 36px !important; }
  .hero-headline { font-size: 1.8rem !important; line-height: 1.22 !important; }
  .hero-subtext { font-size: 13.5px !important; line-height: 1.6 !important; }
  .hero-tag-badge { font-size: 11px !important; padding: 5px 11px !important; }
  .search-input-wrap { width: 100% !important; flex: 1 1 100% !important; }
  .search-input-wrap input { font-size: 16px !important; padding: 11px 14px 11px 40px !important; }
  #locationSelect { width: 100% !important; min-width: 100% !important; font-size: 16px !important; height: 44px !important; }
  .job-modern-card { padding: 16px 14px !important; border-radius: 14px !important; }
  .job-role-title { font-size: 16px !important; }
  .card-meta-row { gap: 10px !important; font-size: 12.5px !important; }
  .card-desc-text { font-size: 13px !important; line-height: 1.6 !important; }
  .accordion-custom-btn { font-size: 14px !important; padding: 13px 15px !important; }
  .accordion-custom-body { font-size: 13px !important; padding: 13px 15px 16px !important; }
}

@media (min-width: 768px) and (max-width: 1024px) {
  .careers-hero { padding: 65px 0 55px; }
  .careers-control-hub { padding: 22px 24px; }
  .job-modern-card { padding: 22px 24px; }
  .perk-card { padding: 24px 20px; }
  .process-step-card { padding: 22px 18px; }
}
</style>

<!-- ── HERO SECTION ────────────────────────────────────────────── -->
<section class="careers-hero">
  <div class="container position-relative">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <div class="hero-tag-badge">
          <i class="fas fa-briefcase text-warning"></i>
          <span>We're Hiring: Professional Careers &amp; Global Opportunities</span>
        </div>
        <h1 class="hero-headline">
          Build Your Career at <span class="text-gradient">Vortexsoft Innovations</span>
        </h1>
        <p class="hero-subtext">
          Join 200+ technology leaders and domain specialists across our Pune HQ, Bengaluru tech hub, and remote teams. Discover high-impact roles in Software Engineering, Title Search, Healthcare BPO, Customer Operations, and Analytics.
        </p>
        <div class="d-flex align-items-center flex-wrap gap-3">
          <a href="#openings" class="btn btn-primary" style="background:#4f46e5;border-color:#4f46e5;font-weight:600;padding:12px 26px;border-radius:10px;">
            <i class="fas fa-arrow-down me-2"></i> View Openings (<?= count($jobs) ?>)
          </a>
          <a href="https://wa.me/918308906690?text=Hi%20Vortexsoft%20HR,%20I%20am%20applying%20for%20an%20open%20position" target="_blank" class="btn btn-outline-light" style="font-weight:600;padding:12px 24px;border-radius:10px;border-color:rgba(255,255,255,.3);">
            <i class="fab fa-whatsapp me-2 text-success"></i> WhatsApp HR Desk
          </a>
        </div>
      </div>
      <div class="col-lg-4 d-none d-lg-block">
        <div style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.12);border-radius:20px;padding:28px;backdrop-filter:blur(12px);">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div style="width:44px;height:44px;background:#4f46e5;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;">
              <i class="fas fa-award"></i>
            </div>
            <div>
              <div style="font-size:12px;color:#94a3b8;font-weight:600;text-transform:uppercase;">Certified Standards</div>
              <div style="font-weight:700;color:#fff;">ISO 27001 &amp; HIPAA</div>
            </div>
          </div>
          <p style="font-size:13.5px;color:#cbd5e1;line-height:1.6;margin-bottom:16px;">
            Work in an elite information security and data-privacy compliant environment with global exposure to US, UK, and European clients.
          </p>
          <div style="font-size:12.5px;color:#94a3b8;">
            <i class="fas fa-check-circle text-success me-1"></i> 48-Hour HR Application Review SLA
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Ribbon -->
    <div class="hero-stats-row">
      <div class="hero-stat-card">
        <div class="stat-num"><?= count($jobs) ?></div>
        <div class="stat-label">Active Career Vacancies</div>
      </div>
      <div class="hero-stat-card">
        <div class="stat-num text-warning">150+</div>
        <div class="stat-label">Global Enterprise Clients</div>
      </div>
      <div class="hero-stat-card">
        <div class="stat-num">Pune &amp; BLR</div>
        <div class="stat-label">Dual Delivery Hubs + Remote</div>
      </div>
      <div class="hero-stat-card">
        <div class="stat-num text-success">48 Hrs</div>
        <div class="stat-label">Initial Screening Response</div>
      </div>
    </div>
  </div>
</section>

<!-- ── SEARCH & FILTER CONTROL HUB ────────────────────────────── -->
<div class="container" id="openings">
  <div class="careers-control-hub">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <!-- Search Input -->
      <div class="search-input-wrap">
        <i class="fas fa-search"></i>
        <input type="text" id="jobSearchInput" placeholder="Search by role, keyword, or skill (e.g., Java, Python, Title Search, Medical Coding)..." autocomplete="off">
      </div>
      <!-- Quick Location Selector -->
      <div class="d-flex align-items-center gap-2">
        <span class="d-none d-md-inline" style="font-size:13px;font-weight:600;color:#64748b;">Location:</span>
        <select id="locationSelect" class="form-select form-select-sm" style="border-radius:10px;font-size:13.5px;font-weight:600;color:#334155;border-color:#cbd5e1;min-width:170px;">
          <option value="all">All Locations</option>
          <option value="pune">Pune HQ</option>
          <option value="bengaluru">Bengaluru Hub</option>
          <option value="remote">Remote / WFH</option>
        </select>
      </div>
    </div>

    <!-- Category Tabs -->
    <div class="filter-tabs-row" id="categoryFilterRow">
      <?php foreach ($categories as $cat): ?>
      <?php 
        $cat_count = 0;
        foreach ($jobs as $j) {
            if ($cat === 'All' || $j['category'] === $cat) $cat_count++;
        }
      ?>
      <button 
        type="button" 
        class="cat-pill-btn <?= $cat === 'All' ? 'active' : '' ?>" 
        data-category="<?= htmlspecialchars($cat) ?>"
        onclick="filterByCategory('<?= htmlspecialchars($cat, ENT_QUOTES) ?>', this)"
      >
        <span><?= htmlspecialchars($cat) ?></span>
        <span class="cat-count"><?= $cat_count ?></span>
      </button>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ── JOB OPENINGS MULTI-COLUMN VIEW ──────────────────────────── -->
<section class="jobs-stack-section">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h3 style="font-family:'Poppins',sans-serif;font-weight:800;font-size:22px;color:#0f172a;margin:0;">
          Showing <span id="visibleJobsCount"><?= count($jobs) ?></span> Available Positions
        </h3>
        <p style="font-size:13.5px;color:#64748b;margin:3px 0 0;">
          Filter by category above or search directly by technology/skillset.
        </p>
      </div>
      <div>
        <button onclick="resetFilters()" class="btn btn-sm btn-link text-decoration-none" style="font-size:13px;color:#4f46e5;font-weight:600;">
          <i class="fas fa-undo me-1"></i> Reset Filters
        </button>
      </div>
    </div>

    <div class="job-cards-container" id="jobsContainer">
      <?php foreach ($jobs as $idx => $job): ?>
      <?php 
        $loc_lower = strtolower($job['location']);
      ?>
      <div 
        class="job-card-wrapper" 
        id="job-<?= $job['id'] ?>"
        data-id="<?= $job['id'] ?>"
        data-title="<?= htmlspecialchars(strtolower($job['title'])) ?>"
        data-category="<?= htmlspecialchars($job['category']) ?>"
        data-location="<?= htmlspecialchars($loc_lower) ?>"
        data-skills="<?= htmlspecialchars(strtolower(implode(' ', $job['skills']))) ?>"
        data-desc="<?= htmlspecialchars(strtolower($job['desc'])) ?>"
      >
        <div class="job-modern-card <?= $job['urgent'] ? 'card-urgent' : 'card-regular' ?>">
          <!-- Top Row Badges -->
          <div class="card-top-row">
            <span class="job-chip chip-dept"><i class="fas fa-layer-group"></i> <?= htmlspecialchars($job['category']) ?></span>
            <div class="d-flex align-items-center gap-1">
              <?php if ($job['urgent']): ?>
              <span class="job-chip chip-urgent"><i class="fas fa-fire"></i> Urgent</span>
              <?php endif; ?>
              <span class="job-chip chip-fulltime"><i class="fas fa-briefcase"></i> <?= htmlspecialchars($job['type']) ?></span>
            </div>
          </div>

          <!-- Job Title (Crisp, Small) -->
          <h4 class="job-role-title">
            <a href="careers.php?job=<?= $job['id'] ?>">
              <?= htmlspecialchars($job['title']) ?>
            </a>
          </h4>

          <!-- Meta Row (Location, Experience) -->
          <div class="card-meta-row">
            <span>
              <i class="fas fa-map-marker-alt text-danger"></i>
              <strong><?= htmlspecialchars($job['location']) ?></strong>
            </span>
            <span>
              <i class="fas fa-user-clock text-primary"></i>
              <?= htmlspecialchars($job['exp']) ?>
            </span>
          </div>

          <!-- Description -->
          <p class="card-desc-text">
            <?= htmlspecialchars($job['desc']) ?>
          </p>

          <!-- Skills Row -->
          <div class="card-skills-row">
            <?php foreach ($job['skills'] as $sk): ?>
            <span class="skill-pill"><?= htmlspecialchars($sk) ?></span>
            <?php endforeach; ?>
          </div>

          <!-- Actions Row (Pinned to bottom) -->
          <div class="card-actions-row">
            <button 
              type="button" 
              class="btn-vsi-share" 
              onclick="openJobShare(<?= htmlspecialchars(json_encode([
                'id'         => (string)$job['id'],
                'title'      => $job['title'],
                'department' => $job['department'],
                'location'   => $job['location'],
                'exp'        => $job['exp'],
                'type'       => $job['type']
              ]), ENT_QUOTES, 'UTF-8') ?>)" 
              title="Share this job opening"
            >
              <i class="fas fa-share-nodes"></i> Share
            </button>
            <button 
              type="button" 
              class="btn-vsi-apply" 
              onclick="openApplyModal('<?= htmlspecialchars($job['title'], ENT_QUOTES) ?>','<?= htmlspecialchars($job['department'], ENT_QUOTES) ?>','<?= $job['id'] ?>')"
            >
              <i class="fas fa-paper-plane"></i> Apply Now
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Empty State Box -->
    <div class="empty-jobs-box d-none" id="emptyJobsBox">
      <div style="font-size:36px;margin-bottom:12px;">🔍</div>
      <h5 style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;margin-bottom:8px;">No Openings Match Your Filter</h5>
      <p style="color:#64748b;font-size:14px;max-width:520px;margin:0 auto 20px;">
        Try clearing your search terms or selecting another category. You can also submit a general application, and our recruiters will review your profile for upcoming batches.
      </p>
      <button onclick="resetFilters()" class="btn btn-outline-primary" style="font-weight:600;padding:8px 22px;border-radius:8px;">
        <i class="fas fa-undo me-1"></i> Show All Positions
      </button>
    </div>
  </div>
</section>

<!-- ── TRANSPARENT 4-STAGE HIRING ROADMAP ───────────────────────── -->
<section class="process-section">
  <div class="container text-center">
    <div class="section-badge-center">Hiring Journey</div>
    <h2 class="section-main-heading">Our Transparent 4-Step Hiring Process</h2>
    <p class="section-sub-heading">
      From initial application to your first day on the squad, we maintain rapid turnaround SLAs and clear feedback at every step.
    </p>

    <div class="row g-4 text-start">
      <div class="col-md-6 col-lg-3">
        <div class="process-step-card">
          <div class="process-step-num">01</div>
          <h5 style="font-family:'Poppins',sans-serif;font-weight:700;font-size:16px;color:#0f172a;margin-bottom:8px;">Online Application</h5>
          <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">
            Submit your resume and contact details online. Our technical recruiters screen every submission within 48 business hours.
          </p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="process-step-card">
          <div class="process-step-num">02</div>
          <h5 style="font-family:'Poppins',sans-serif;font-weight:700;font-size:16px;color:#0f172a;margin-bottom:8px;">Skill Evaluation</h5>
          <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">
            Complete a practical coding challenge, test project, or domain assessment tailored to your chosen specialization.
          </p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="process-step-card">
          <div class="process-step-num">03</div>
          <h5 style="font-family:'Poppins',sans-serif;font-weight:700;font-size:16px;color:#0f172a;margin-bottom:8px;">Technical Discussion</h5>
          <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">
            Deep-dive conversation with an engineering lead or domain head discussing problem-solving approaches and project experiences.
          </p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="process-step-card">
          <div class="process-step-num">04</div>
          <h5 style="font-family:'Poppins',sans-serif;font-weight:700;font-size:16px;color:#0f172a;margin-bottom:8px;">Offer &amp; Onboarding</h5>
          <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">
            Formal offer letter rollout, digital documentation verification, workstation setup, and dedicated mentor pairing.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── WHY WORK WITH US (Bottom of Page Section) ───────────────── -->
<section class="why-work-section" id="why-work-with-us">
  <div class="container text-center">
    <div class="section-badge-center">Culture &amp; Growth</div>
    <h2 class="section-main-heading">Why Work With Us?</h2>
    <p class="section-sub-heading" style="max-width:760px;margin:0 auto 45px;">
      At Vortexsoft, we believe in fostering a culture of innovation, collaboration, and growth. We invest in our people with continuous training, international exposure, and career advancement opportunities.
    </p>

    <!-- 6 Benefits Grid -->
    <div class="row g-4 text-start mb-4">
      <!-- 1. Continuous Learning -->
      <div class="col-md-6 col-lg-4">
        <div class="perk-card">
          <div class="perk-icon-wrap" style="background:#eff6ff;color:#1d4ed8;">
            <i class="fas fa-book-open-reader"></i>
          </div>
          <h5 class="perk-title">Continuous Learning</h5>
          <p class="perk-desc">
            Certifications, workshops, and training programs designed to keep you at the cutting edge of modern tech.
          </p>
        </div>
      </div>

      <!-- 2. Global Exposure -->
      <div class="col-md-6 col-lg-4">
        <div class="perk-card">
          <div class="perk-icon-wrap" style="background:#f5f3ff;color:#6d28d9;">
            <i class="fas fa-globe-americas"></i>
          </div>
          <h5 class="perk-title">Global Exposure</h5>
          <p class="perk-desc">
            Work with 150+ international clients across the United States, Europe, and global delivery markets.
          </p>
        </div>
      </div>

      <!-- 3. Health Benefits -->
      <div class="col-md-6 col-lg-4">
        <div class="perk-card">
          <div class="perk-icon-wrap" style="background:#ecfdf5;color:#047857;">
            <i class="fas fa-heart-pulse"></i>
          </div>
          <h5 class="perk-title">Health Benefits</h5>
          <p class="perk-desc">
            Comprehensive health and wellness programs providing group medical coverage and family wellness support.
          </p>
        </div>
      </div>

      <!-- 4. Performance Rewards -->
      <div class="col-md-6 col-lg-4">
        <div class="perk-card">
          <div class="perk-icon-wrap" style="background:#fffbeb;color:#b45309;">
            <i class="fas fa-trophy"></i>
          </div>
          <h5 class="perk-title">Performance Rewards</h5>
          <p class="perk-desc">
            Quarterly bonuses and recognition awards celebrating standout project deliveries and innovation.
          </p>
        </div>
      </div>

      <!-- 5. Flexible Work -->
      <div class="col-md-6 col-lg-4">
        <div class="perk-card">
          <div class="perk-icon-wrap" style="background:#f0fdf4;color:#15803d;">
            <i class="fas fa-laptop-house"></i>
          </div>
          <h5 class="perk-title">Flexible Work</h5>
          <p class="perk-desc">
            Remote and hybrid work options available across software engineering, AI, and digital prepress teams.
          </p>
        </div>
      </div>

      <!-- 6. Career Growth -->
      <div class="col-md-6 col-lg-4">
        <div class="perk-card">
          <div class="perk-icon-wrap" style="background:#fdf2f8;color:#be185d;">
            <i class="fas fa-arrow-trend-up"></i>
          </div>
          <h5 class="perk-title">Career Growth</h5>
          <p class="perk-desc">
            Fast-track career advancement paths, mentorship from industry leaders, and cross-functional leadership opportunities.
          </p>
        </div>
      </div>
    </div>

    <!-- ── READY TO APPLY? (Bottom Contact Box) ───────────────────── -->
    <div class="ready-to-apply-box text-start">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <div class="d-inline-flex align-items-center gap-2 mb-2" style="font-size:12px;font-weight:700;color:#818cf8;text-transform:uppercase;letter-spacing:1px;">
            <i class="fas fa-paper-plane"></i> Direct Candidate Helpline
          </div>
          <h3 style="font-family:'Poppins',sans-serif;font-weight:800;font-size:clamp(1.6rem,3vw,2.2rem);color:#ffffff;margin-bottom:10px;">
            Ready to Apply?
          </h3>
          <p style="font-size:15px;color:#cbd5e1;line-height:1.7;margin:0;max-width:560px;">
            Browse our openings above, or send your resume directly to our HR team. We respond to all applications within 3–5 business days.
          </p>
        </div>
        <div class="col-lg-5">
          <div class="d-flex flex-column gap-3">
            <!-- HR Email Card -->
            <a href="mailto:careers@vortexsoftinnovations.com" class="apply-contact-card">
              <div class="apply-icon-wrap" style="background:rgba(99,102,241,0.22);color:#a5b4fc;">
                <i class="fas fa-envelope"></i>
              </div>
              <div class="flex-grow-1">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;color:#94a3b8;letter-spacing:0.5px;">HR Email</div>
                <div style="font-size:14px;font-weight:700;color:#ffffff;word-break:break-all;">careers@vortexsoftinnovations.com</div>
              </div>
              <i class="fas fa-arrow-right" style="color:#a5b4fc;font-size:13px;"></i>
            </a>

            <!-- WhatsApp HR Card -->
            <a href="https://wa.me/918308906690?text=Hello%20Vortexsoft%20Innovations%20HR%20Team%2C%20I%20am%20a%20job%20candidate%20inquiring%20about%20career%20opportunities%20and%20openings.%20I%20would%20like%20to%20share%20my%20profile%20for%20review." target="_blank" class="apply-contact-card">
              <div class="apply-icon-wrap" style="background:rgba(37,211,102,0.22);color:#4ade80;">
                <i class="fab fa-whatsapp"></i>
              </div>
              <div class="flex-grow-1">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;color:#94a3b8;letter-spacing:0.5px;">WhatsApp HR</div>
                <div style="font-size:14.5px;font-weight:700;color:#ffffff;">+91-8308906690</div>
              </div>
              <i class="fas fa-arrow-right" style="color:#4ade80;font-size:13px;"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── CANDIDATE FAQ ACCORDION ──────────────────────────────────── -->
<section class="faq-accordion-section">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-badge-center">Common Queries</div>
      <h2 class="section-main-heading">Frequently Asked Questions</h2>
      <p class="section-sub-heading">
        Everything you need to know about career openings, work models, recruitment cycles, and employee benefits at Vortexsoft Innovations.
      </p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="accordion" id="careersAccordion">
          <!-- FAQ 1 -->
          <div class="accordion-item mb-3" style="border:none;">
            <h2 class="accordion-header" id="faq1">
              <button class="accordion-button accordion-custom-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true">
                <i class="fas fa-briefcase me-2 text-primary"></i> What career openings and technical positions are available?
              </button>
            </h2>
            <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#careersAccordion">
              <div class="accordion-body accordion-custom-body">
                Vortexsoft Innovations is actively recruiting across multiple key domains including Java Developers, Python Developers, Data Engineers, US Title Search Executives, Healthcare Medical Coders, BPO Executives, KPO Analysts, HR Executives, and Associate Process Analysts. We offer competitive salaries, dual delivery center facilities in Pune and Bengaluru, and hybrid/remote work flexibility.
              </div>
            </div>
          </div>

          <!-- FAQ 2 -->
          <div class="accordion-item mb-3" style="border:none;">
            <h2 class="accordion-header" id="faq2">
              <button class="accordion-button accordion-custom-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                <i class="fas fa-file-signature me-2 text-primary"></i> How do I apply for open positions?
              </button>
            </h2>
            <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#careersAccordion">
              <div class="accordion-body accordion-custom-body">
                You can apply directly online through this Careers portal by clicking <strong>"Apply Now"</strong> on any vacancy. Alternatively, email your resume to <a href="mailto:<?= EMAIL_HR ?>" class="fw-bold text-primary"><?= EMAIL_HR ?></a> or message our talent acquisition team on WhatsApp at <a href="https://wa.me/918308906690" target="_blank" class="fw-bold text-success">+91-8308906690</a>.
              </div>
            </div>
          </div>

          <!-- FAQ 3 -->
          <div class="accordion-item mb-3" style="border:none;">
            <h2 class="accordion-header" id="faq3">
              <button class="accordion-button accordion-custom-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                <i class="fas fa-laptop-house me-2 text-primary"></i> Does Vortexsoft Innovations support Work-From-Home (WFH) or hybrid models?
              </button>
            </h2>
            <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#careersAccordion">
              <div class="accordion-body accordion-custom-body">
                Yes. Most software engineering, data engineering, and analytics roles support hybrid or 100% remote working arrangements across India. Core title abstracting and specialized operations operate on-site from our high-security facilities in Pune and Bengaluru.
              </div>
            </div>
          </div>

          <!-- FAQ 4 -->
          <div class="accordion-item mb-3" style="border:none;">
            <h2 class="accordion-header" id="faq4">
              <button class="accordion-button accordion-custom-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                <i class="fas fa-building me-2 text-primary"></i> What Title Search and Real Estate BPO openings are available at Pune HQ?
              </button>
            </h2>
            <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#careersAccordion">
              <div class="accordion-body accordion-custom-body">
                Vortexsoft operates India's premier Title Abstracting center at our Pune Corporate Headquarters (Vadgaon Budruk). We actively hire US Title Search &amp; Settlement Executives to examine county property records, deeds, open mortgages, and prepare commitment packages for 40+ US title settlement clients across all 50 US States.
              </div>
            </div>
          </div>

          <!-- FAQ 5 -->
          <div class="accordion-item mb-3" style="border:none;">
            <h2 class="accordion-header" id="faq5">
              <button class="accordion-button accordion-custom-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5">
                <i class="fas fa-award me-2 text-primary"></i> What employee benefits and compensation structure does Vortexsoft offer?
              </button>
            </h2>
            <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#careersAccordion">
              <div class="accordion-body accordion-custom-body">
                Compensation packages are competitive and commensurate with candidate experience, technical interview evaluation, and domain expertise. Employee benefits include comprehensive group medical insurance, US shift allowances, quarterly performance bonuses, flexible work models, and sponsored professional certifications.
              </div>
            </div>
          </div>

          <!-- FAQ 6 -->
          <div class="accordion-item mb-3" style="border:none;">
            <h2 class="accordion-header" id="faq6">
              <button class="accordion-button accordion-custom-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6">
                <i class="fas fa-map-pin me-2 text-primary"></i> Where are the physical offices and delivery centers located?
              </button>
            </h2>
            <div id="collapse6" class="accordion-collapse collapse" data-bs-parent="#careersAccordion">
              <div class="accordion-body accordion-custom-body">
                Vortexsoft Innovations is headquartered in <strong>Pune</strong> (502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk, Pune 411041), maintains an engineering delivery center in <strong>Bengaluru</strong> (No.125, Ranganath Complex, Madiwala, HSR Layout, Bengaluru 560068), and holds an international executive office in <strong>Sheridan, Wyoming, USA</strong>.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── APPLICATION MODAL ───────────────────────────────────────── -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:18px;border:none;box-shadow:0 25px 60px rgba(0,0,0,0.25);">
      <div class="modal-header" style="background:#0f172a;color:#fff;border-bottom:1px solid rgba(255,255,255,.1);padding:20px 28px;">
        <h5 class="modal-title" id="applyModalLabel" style="font-family:'Poppins',sans-serif;font-weight:700;font-size:18px;">
          <i class="fas fa-briefcase text-primary me-2"></i> Apply for <span id="modal-job-title" class="text-warning">Position</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4" style="background:#f8fafc;">
        <div id="apply-feedback" class="d-none mb-3" role="alert"></div>
        <form id="applyForm" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="job_title" id="form-job-title">
          <input type="hidden" name="department" id="form-department">
          <input type="hidden" name="job_id" id="form-job-id">
          <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold" style="font-size:13px;color:#334155;">Full Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="applicant_name" placeholder="Your full legal name" style="border-radius:10px;font-size:14px;" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold" style="font-size:13px;color:#334155;">Email Address <span class="text-danger">*</span></label>
              <input type="email" class="form-control" name="email" placeholder="you@email.com" style="border-radius:10px;font-size:14px;" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold" style="font-size:13px;color:#334155;">Phone / WhatsApp <span class="text-danger">*</span></label>
              <input type="tel" class="form-control" name="phone" placeholder="+91 XXXXX XXXXX" style="border-radius:10px;font-size:14px;" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold" style="font-size:13px;color:#334155;">Current City &amp; State</label>
              <input type="text" class="form-control" name="current_location" placeholder="e.g. Pune, Maharashtra" style="border-radius:10px;font-size:14px;">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold" style="font-size:13px;color:#334155;">Years of Experience</label>
              <select class="form-select" name="experience_years" style="border-radius:10px;font-size:14px;">
                <option value="0">Fresher / Entry Level (0–1 years)</option>
                <option value="1">1 Year</option>
                <option value="2">2 Years</option>
                <option value="3">3 Years</option>
                <option value="4">4 Years</option>
                <option value="5">5+ Years</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold" style="font-size:13px;color:#334155;">Notice Period / Availability</label>
              <select class="form-select" name="notice_period" style="border-radius:10px;font-size:14px;">
                <option value="Immediate">Immediate / Within 1 Week</option>
                <option value="15 Days">15 Days</option>
                <option value="30 Days">30 Days</option>
                <option value="60+ Days">60+ Days</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold" style="font-size:13px;color:#334155;">Upload Resume (.pdf, .docx, .doc — max 5MB) <span class="text-danger">*</span></label>
              <input type="file" class="form-control" name="resume" accept=".pdf,.doc,.docx" style="border-radius:10px;font-size:14px;" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold" style="font-size:13px;color:#334155;">Brief Note / GitHub / Portfolio Link</label>
              <textarea class="form-control" name="cover_letter" rows="3" placeholder="Tell us about your background, key projects, or paste your GitHub/LinkedIn profile link..." style="border-radius:10px;font-size:14px;"></textarea>
            </div>
          </div>
          <div class="mt-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span style="font-size:12px;color:#64748b;">
              <i class="fas fa-lock me-1"></i> Data handled securely under ISO 27001 standards.
            </span>
            <button type="submit" id="applySubmitBtn" class="btn btn-primary" style="background:#1C2280;border-color:#1C2280;font-weight:700;padding:10px 28px;border-radius:10px;">
              <i class="fas fa-paper-plane me-2"></i> Submit Application
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ── SHARE MODAL ─────────────────────────────────────────────── -->
<div class="modal fade" id="jobShareModal" tabindex="-1" aria-labelledby="jobShareModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:18px;border:none;box-shadow:0 25px 60px rgba(0,0,0,0.25);">
      <div class="modal-header" style="background:#0f172a;color:#fff;border-bottom:1px solid rgba(255,255,255,.1);padding:18px 24px;">
        <h5 class="modal-title" id="jobShareModalLabel" style="font-family:'Poppins',sans-serif;font-weight:700;font-size:17px;">
          <i class="fas fa-share-nodes text-primary me-2"></i> Share Opportunity
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4" style="background:#ffffff;">
        <h6 id="shareModalJobTitle" style="font-family:'Poppins',sans-serif;font-weight:700;color:#0f172a;margin-bottom:6px;"></h6>
        <p id="shareModalJobMeta" style="font-size:13px;color:#64748b;margin-bottom:20px;"></p>

        <label style="font-size:12.5px;font-weight:600;color:#334155;margin-bottom:6px;display:block;">Direct Share Link</label>
        <div class="input-group mb-4">
          <input type="text" id="shareLinkInput" class="form-control" style="font-size:13px;background:#f8fafc;border-radius:8px 0 0 8px;" readonly>
          <button class="btn btn-outline-primary" type="button" id="copyShareBtn" style="font-weight:600;font-size:13px;border-radius:0 8px 8px 0;">
            <i class="fas fa-copy me-1"></i> Copy Link
          </button>
        </div>

        <div class="d-flex align-items-center justify-content-center gap-3">
          <a href="#" id="shareWaBtn" target="_blank" class="btn" style="background:#25D366;color:#fff;border-radius:10px;font-weight:600;font-size:13px;padding:9px 18px;">
            <i class="fab fa-whatsapp me-1"></i> WhatsApp
          </a>
          <a href="#" id="shareLiBtn" target="_blank" class="btn" style="background:#0A66C2;color:#fff;border-radius:10px;font-weight:600;font-size:13px;padding:9px 18px;">
            <i class="fab fa-linkedin me-1"></i> LinkedIn
          </a>
          <a href="#" id="shareMailBtn" class="btn" style="background:#ea4335;color:#fff;border-radius:10px;font-weight:600;font-size:13px;padding:9px 18px;">
            <i class="fas fa-envelope me-1"></i> Email
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- ── INTERACTIVE CLIENT-SIDE LOGIC ───────────────────────────── -->
<script>
(function() {
  const searchInput    = document.getElementById('jobSearchInput');
  const locationSelect = document.getElementById('locationSelect');
  const catButtons     = document.querySelectorAll('.cat-pill-btn');
  const jobCards       = document.querySelectorAll('.job-card-wrapper');
  const visibleCountEl = document.getElementById('visibleJobsCount');
  const emptyBox       = document.getElementById('emptyJobsBox');

  let activeCategory = 'All';
  let activeLocation = 'all';
  let searchKeyword  = '';

  function applyFilters() {
    let count = 0;
    jobCards.forEach(card => {
      const cardTitle    = card.getAttribute('data-title') || '';
      const cardCategory = card.getAttribute('data-category') || '';
      const cardLocation = card.getAttribute('data-location') || '';
      const cardSkills   = card.getAttribute('data-skills') || '';
      const cardDesc     = card.getAttribute('data-desc') || '';

      // 1. Category check
      const matchCat = (activeCategory === 'All') || (cardCategory === activeCategory);

      // 2. Location check
      let matchLoc = true;
      if (activeLocation !== 'all') {
        matchLoc = cardLocation.includes(activeLocation);
      }

      // 3. Search check
      let matchSearch = true;
      if (searchKeyword.length > 0) {
        matchSearch = cardTitle.includes(searchKeyword) || 
                      cardSkills.includes(searchKeyword) || 
                      cardDesc.includes(searchKeyword) ||
                      cardLocation.includes(searchKeyword) ||
                      cardCategory.toLowerCase().includes(searchKeyword);
      }

      if (matchCat && matchLoc && matchSearch) {
        card.classList.remove('d-none');
        count++;
      } else {
        card.classList.add('d-none');
      }
    });

    if (visibleCountEl) visibleCountEl.textContent = count;
    if (emptyBox) {
      if (count === 0) {
        emptyBox.classList.remove('d-none');
      } else {
        emptyBox.classList.add('d-none');
      }
    }
  }

  window.filterByCategory = function(cat, btn) {
    activeCategory = cat;
    catButtons.forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    applyFilters();
  };

  if (searchInput) {
    searchInput.addEventListener('input', function() {
      searchKeyword = this.value.trim().toLowerCase();
      applyFilters();
    });
  }

  if (locationSelect) {
    locationSelect.addEventListener('change', function() {
      activeLocation = this.value;
      applyFilters();
    });
  }

  window.resetFilters = function() {
    activeCategory = 'All';
    activeLocation = 'all';
    searchKeyword = '';
    if (searchInput) searchInput.value = '';
    if (locationSelect) locationSelect.value = 'all';
    catButtons.forEach(b => {
      if (b.getAttribute('data-category') === 'All') {
        b.classList.add('active');
      } else {
        b.classList.remove('active');
      }
    });
    applyFilters();
  };

  // Deep-linking: scroll to shared job if requested
  const urlParams = new URLSearchParams(window.location.search);
  const targetJobId = urlParams.get('job');
  if (targetJobId) {
    const targetEl = document.getElementById('job-' + targetJobId);
    if (targetEl) {
      setTimeout(() => {
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        const cardInner = targetEl.querySelector('.job-modern-card');
        if (cardInner) {
          cardInner.style.boxShadow = '0 0 0 4px #818cf8';
          setTimeout(() => { cardInner.style.boxShadow = ''; }, 3000);
        }
      }, 400);
    }
  }
})();

// Application Modal Functions
function openApplyModal(title, dept, jobId) {
  document.getElementById('modal-job-title').textContent = title || 'Open Position';
  document.getElementById('form-job-title').value = title || 'General Application';
  document.getElementById('form-department').value = dept || 'General';
  document.getElementById('form-job-id').value = jobId || '';
  const fb = document.getElementById('apply-feedback');
  if (fb) {
    fb.className = 'd-none mb-3';
    fb.innerHTML = '';
  }
  const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('applyModal'));
  modal.show();
}

// Share Modal Functions
function openJobShare(job) {
  const shareUrl = window.location.origin + window.location.pathname + '?job=' + job.id;
  document.getElementById('shareModalJobTitle').textContent = job.title;
  document.getElementById('shareModalJobMeta').textContent = job.department + ' • ' + job.location + ' • ' + job.type;
  document.getElementById('shareLinkInput').value = shareUrl;

  const shareText = encodeURIComponent('Explore opening for ' + job.title + ' at Vortexsoft Innovations: ' + shareUrl);
  document.getElementById('shareWaBtn').href = 'https://api.whatsapp.com/send?text=' + shareText;
  document.getElementById('shareLiBtn').href = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(shareUrl);
  document.getElementById('shareMailBtn').href = 'mailto:?subject=' + encodeURIComponent('Job Opening: ' + job.title) + '&body=' + shareText;

  const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('jobShareModal'));
  modal.show();
}

document.getElementById('copyShareBtn')?.addEventListener('click', function() {
  const input = document.getElementById('shareLinkInput');
  input.select();
  navigator.clipboard.writeText(input.value).then(() => {
    this.innerHTML = '<i class="fas fa-check text-success me-1"></i> Copied!';
    setTimeout(() => {
      this.innerHTML = '<i class="fas fa-copy me-1"></i> Copy Link';
    }, 2000);
  });
});

// Form Submission via Fetch
document.getElementById('applyForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const form = this;
  const submitBtn = document.getElementById('applySubmitBtn');
  const fb = document.getElementById('apply-feedback');

  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Submitting Profile...';

  const formData = new FormData(form);

  fetch('api/apply.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      fb.className = 'alert alert-success mb-3';
      fb.innerHTML = '<i class="fas fa-check-circle me-2"></i> ' + (data.message || 'Application submitted successfully! Our HR team will contact you within 48 business hours.');
      form.reset();
      submitBtn.innerHTML = '<i class="fas fa-check me-2"></i> Application Sent!';
      setTimeout(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('applyModal'));
        if (modal) modal.hide();
      }, 3000);
    } else {
      fb.className = 'alert alert-danger mb-3';
      fb.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> ' + (data.error || 'Submission error. Please verify required fields and file format.');
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Submit Application';
    }
  })
  .catch(err => {
    fb.className = 'alert alert-danger mb-3';
    fb.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Network error. Please try again or email your resume to hr@vortexsoftinnovations.com.';
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Submit Application';
  });
});
</script>
