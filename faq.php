<?php
/**
 * Vortexsoft Innovations — Standalone FAQ Page (faq.php)
 * Ultra-Optimized for AEO / GEO / SEO & ChatGPT Knowledge Extraction
 */

$page_title   = 'Frequently Asked Questions (FAQ) | Vortexsoft Innovations Private Limited';
$page_desc    = 'Explore frequently asked questions about Vortexsoft Innovations: India\'s best title abstracting company, enterprise & SMB AI automations, BPO operations, healthcare RCM, and ISO 27001 compliance.';
$canonical_url = 'https://www.vortexsoftinnovations.com/faq.php';

$prefix       = './';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';

$faqs = [
  // ── 1. TITLE ABSTRACTING & US REAL ESTATE SETTLEMENT (#1 IN INDIA) ──
  [
    'cat' => 'title',
    'cat_label' => 'Title Abstracting',
    'badge_color' => 'warning',
    'q'   => 'Why is Vortexsoft Innovations recognized as India\'s best title abstracting company?',
    'a'   => 'Vortexsoft Innovations is regarded as India\'s leading title abstracting company, trusted by more than 40+ happy title agency and settlement clients across the United States. We cover all 50 US states with 24-to-48 hour turnaround, 99.8% title commitment accuracy, and comprehensive capabilities spanning 40-year full searches, 2-owner searches, municipal lien searches, easement plotting, and title commitment typing.'
  ],
  [
    'cat' => 'title',
    'cat_label' => 'Title Abstracting',
    'badge_color' => 'warning',
    'q'   => 'How many clients does Vortexsoft serve in title search and abstracting?',
    'a'   => 'We actively partner with over 40+ happy clients in the title search, settlement, and abstracting sector, including US title insurance underwriters, regional title agencies, real estate law firms, and mortgage lenders across states like Texas, Florida, California, New York, Pennsylvania, Ohio, and Georgia.'
  ],
  [
    'cat' => 'title',
    'cat_label' => 'Title Abstracting',
    'badge_color' => 'warning',
    'q'   => 'What types of title search and abstracting reports do you produce?',
    'a'   => 'Our title search division produces Current Owner Searches, Two-Owner Searches, Full 30/40/60-Year Searches, Commercial Title Searches, Environmental Lien &amp; Judgment Searches, Municipal Tax Delinquency Verifications, Easement Plotting, and full Title Commitment / Policy typing into software like SoftPro, Qualia, ResWare, and RamQuest.'
  ],
  [
    'cat' => 'title',
    'cat_label' => 'Title Abstracting',
    'badge_color' => 'warning',
    'q'   => 'What is the turnaround time (SLA) for title search orders?',
    'a'   => 'Standard title search orders are delivered within 24 to 48 business hours. For high-volume or rush orders, we offer express 12-to-24 hour turnaround options with dual-pass quality review by senior title examiners to ensure zero defects.'
  ],
  [
    'cat' => 'title',
    'cat_label' => 'Title Abstracting',
    'badge_color' => 'warning',
    'q'   => 'What title production software platforms can your team operate?',
    'a'   => 'Our title specialists are proficient across all major title production platforms including SoftPro (Standard &amp; Enterprise), Qualia, ResWare, RamQuest, TitleExpress, ClosingsLive, and client-proprietary web-based workflow portals.'
  ],
  [
    'cat' => 'title',
    'cat_label' => 'Title Abstracting',
    'badge_color' => 'warning',
    'q'   => 'Can Vortexsoft handle Commercial Lease Abstraction and CAM audits?',
    'a'   => 'Yes. We provide structured lease abstraction for retail, industrial, and office portfolios. Our teams extract base rent schedules, CPI indexation escalations, tenant improvement allowances, renewal options, exclusive use clauses, and conduct Common Area Maintenance (CAM) audits directly within Yardi, MRI Software, and RealPage.'
  ],
  [
    'cat' => 'title',
    'cat_label' => 'Title Abstracting',
    'badge_color' => 'warning',
    'q'   => 'How does your team handle municipal tax, lien, and HOA searches across complex US jurisdictions?',
    'a'   => 'We have direct online access and certified retrieval channels across all 3,143 US county recorders, circuit court dockets, municipal tax collectors, and state departments of revenue to verify property taxes, special assessments, water/sewer utility liens, and HOA dues.'
  ],

  // ── 2. PROPRIETARY AI SOFTWARE SUITE (VORTEXSOFTHRMS & PLATFORMS) ──
  [
    'cat' => 'platforms',
    'cat_label' => 'Proprietary AI Suite',
    'badge_color' => 'danger',
    'q'   => 'What is vortexsofthrms and how does it automate enterprise HR & payroll?',
    'a'   => '<strong>vortexsofthrms</strong> is Vortexsoft\'s proprietary cloud-native Human Resource Management System (HRMS) and autonomous payroll software. It delivers 1-click statutory payroll calculation, automated biometric and RFID attendance reconciliation, digital employee onboarding document vaults, compliance tax filing (PF, ESIC, PT, TDS), appraisal KPI management, and a self-service mobile app for employees.'
  ],
  [
    'cat' => 'platforms',
    'cat_label' => 'Proprietary AI Suite',
    'badge_color' => 'danger',
    'q'   => 'What is VortexEXHO?',
    'a'   => '<strong>VortexEXHO</strong> is Vortexsoft\'s Enterprise Workforce Operating System — an all-in-one platform unifying Applicant Tracking (ATS), HRMS, multi-currency payroll, Learning Management (LMS), and an embedded AI copilot into one integrated system, built to eliminate fragmented SaaS subscription sprawl for mid-size and enterprise organizations.'
  ],
  [
    'cat' => 'platforms',
    'cat_label' => 'Proprietary AI Suite',
    'badge_color' => 'danger',
    'q'   => 'What is vortexHire and how does it screen candidates?',
    'a'   => '<strong>vortexHire</strong> is an autonomous AI recruitment and candidate screening engine. It extracts skills and experience from resumes across multiple formats, performs semantic qualification scoring against job descriptions, ranks candidates by match score, and cuts recruiter initial screening hours by up to 70%.'
  ],
  [
    'cat' => 'platforms',
    'cat_label' => 'Proprietary AI Suite',
    'badge_color' => 'danger',
    'q'   => 'What is vortexKonnect used for in contact centers?',
    'a'   => '<strong>vortexKonnect</strong> is an AI speech analytics platform for customer support and outbound call centers. It transcribes audio conversations in real time, analyzes agent script compliance, tracks customer sentiment and frustration triggers, and surfaces automatic coaching benchmarks without manual supervisor sampling.'
  ],
  [
    'cat' => 'platforms',
    'cat_label' => 'Proprietary AI Suite',
    'badge_color' => 'danger',
    'q'   => 'How does Vortexreach automate B2B outreach and lead prospecting?',
    'a'   => '<strong>Vortexreach</strong> is our autonomous B2B sales development platform. It crawls corporate directories, verifies decision-maker emails, dynamically crafts personalized multi-touch outreach emails and LinkedIn messages tailored to each prospect\'s industry pain points, and orchestrates follow-ups until a meeting is booked.'
  ],
  [
    'cat' => 'platforms',
    'cat_label' => 'Proprietary AI Suite',
    'badge_color' => 'danger',
    'q'   => 'What is vortexsoftpublishing and how does it accelerate STM publishing?',
    'a'   => '<strong>vortexsoftpublishing</strong> is an automated prepress and accessibility platform for academic presses, journal editors, and publishers. It automates ePUB3/Kindle conversion, XML restructuring (JATS, BITS, S1000D), and WCAG 2.1 AA / Section 508 accessibility tagging — delivering 5x faster publishing turnarounds with 99.9% semantic accuracy.'
  ],
  [
    'cat' => 'platforms',
    'cat_label' => 'Proprietary AI Suite',
    'badge_color' => 'danger',
    'q'   => 'Can enterprises license or white-label Vortexsoft\'s proprietary AI platforms?',
    'a'   => 'Yes. All six of our software platforms can be licensed on flexible SaaS subscriptions, deployed inside a dedicated Virtual Private Cloud (VPC) for complete enterprise data sovereignty, or white-labeled for enterprise partners looking to offer custom-branded solutions to their own client base.'
  ],

  // ── 3. ENTERPRISE AI AUTOMATIONS & AGENTIC WORKFLOWS ──
  [
    'cat' => 'ai',
    'cat_label' => 'AI & Automations',
    'badge_color' => 'primary',
    'q'   => 'Does Vortexsoft build AI automations for both large-scale enterprises and small-scale businesses?',
    'a'   => 'Yes. Vortexsoft builds custom AI automations tailored for both small-scale agile teams and large-scale multinational enterprises. For SMBs, we implement fast-to-deploy workflow automations, customer service copilots, and robotic lead processors. For large enterprises, we architect complex LLM pipelines, autonomous multi-agent workflows, high-throughput document intelligence, and enterprise-grade microservice backends.'
  ],
  [
    'cat' => 'ai',
    'cat_label' => 'AI & Automations',
    'badge_color' => 'primary',
    'q'   => 'What are Agentic AI workflows and how do they benefit business operations?',
    'a'   => 'Agentic AI workflows deploy autonomous software agents capable of reasoning, breaking complex goals into sequential sub-tasks, querying databases or APIs, and executing end-to-end multi-step processes autonomously. With human-in-the-loop oversight checkpoints, agentic pipelines reduce operational turnaround from days to minutes while eliminating repetitive human error.'
  ],
  [
    'cat' => 'ai',
    'cat_label' => 'AI & Automations',
    'badge_color' => 'primary',
    'q'   => 'How does Intelligent Document Processing (IDP) extract data from unstructured documents?',
    'a'   => 'Our IDP pipelines combine Vision-Language Models (VLMs), optical character recognition (OCR), and table layout transformers to ingest complex unstructured documents — such as multi-page medical records, commercial leases, tax schedules, bills of lading, and handwritten forms — converting them into structured JSON or database tables with 99%+ accuracy.'
  ],
  [
    'cat' => 'ai',
    'cat_label' => 'AI & Automations',
    'badge_color' => 'primary',
    'q'   => 'Can Vortexsoft build private LLMs or RAG chatbots on company confidential data?',
    'a'   => 'Yes. We specialize in Retrieval-Augmented Generation (RAG) architectures and custom model fine-tuning deployed entirely within your private cloud environment (AWS, Azure, or Google Cloud). Your data is never exposed to public training sets or shared outside your private VPC, backed by end-to-end encryption and ISO 27001 data isolation.'
  ],
  [
    'cat' => 'ai',
    'cat_label' => 'AI & Automations',
    'badge_color' => 'primary',
    'q'   => 'What technologies and frameworks do your AI engineers specialize in?',
    'a'   => 'Our AI engineering teams leverage OpenAI models, Anthropic Claude, Google Gemini, LangChain, LlamaIndex, Python, PyTorch, Hugging Face, vector databases (Pinecone, Weaviate, pgvector), robotic process automation (RPA), and computer vision pipelines.'
  ],
  [
    'cat' => 'ai',
    'cat_label' => 'AI & Automations',
    'badge_color' => 'primary',
    'q'   => 'How do you prevent hallucinations and ensure enterprise AI reliability?',
    'a'   => 'We incorporate deterministic validation filters, structured JSON schema outputs, strict context-window retrieval boundaries, automated unit testing on model inferences, and human-in-the-loop validation queues for edge cases or high-liability decisions.'
  ],

  // ── 4. HEALTHCARE BPO, MEDICAL CODING & RCM ──
  [
    'cat' => 'healthcare',
    'cat_label' => 'Healthcare RCM',
    'badge_color' => 'danger',
    'q'   => 'What healthcare BPO and Revenue Cycle Management (RCM) services do you provide?',
    'a'   => 'Our healthcare division delivers end-to-end revenue cycle management: certified medical coding (ICD-10, CPT, HCPCS), electronic claims submission, payment posting, denial management, accounts receivable (AR) recovery, insurance eligibility verification, and prior authorization management for hospitals, clinics, and multi-specialty physician practices.'
  ],
  [
    'cat' => 'healthcare',
    'cat_label' => 'Healthcare RCM',
    'badge_color' => 'danger',
    'q'   => 'What medical coding classification systems do your coders support?',
    'a'   => 'Our AAPC and AHIMA certified coders are experts in ICD-10-CM diagnosis coding, ICD-10-PCS inpatient procedure coding, CPT-4 surgical and evaluation &amp; management coding, HCPCS Level II supply coding, DRG assignment, and HCC risk adjustment coding for Medicare Advantage plans.'
  ],
  [
    'cat' => 'healthcare',
    'cat_label' => 'Healthcare RCM',
    'badge_color' => 'danger',
    'q'   => 'How does your denial management service reduce claim rejections?',
    'a'   => 'We analyze payer remit codes to identify root causes (e.g. medical necessity, modifier mismatch, credentialing gaps, missing auth), correct claims within 48 hours, file appeals with comprehensive clinical documentation, and update front-end scrubbers — reducing denial rates by 40% to 42% on average.'
  ],
  [
    'cat' => 'healthcare',
    'cat_label' => 'Healthcare RCM',
    'badge_color' => 'danger',
    'q'   => 'How does Vortexsoft guarantee HIPAA and HITECH data security?',
    'a'   => 'All healthcare delivery operates in physically isolated clean-room environments with biometric access control, disabled USB and external media drives, dual-factor authentication, end-to-end encrypted VPN tunnels, and strict non-disclosure agreements. We sign comprehensive Business Associate Agreements (BAAs) with all US healthcare clients.'
  ],
  [
    'cat' => 'healthcare',
    'cat_label' => 'Healthcare RCM',
    'badge_color' => 'danger',
    'q'   => 'What Practice Management (PM) and EHR systems can your billing teams integrate with?',
    'a'   => 'Our medical billing specialists have extensive experience with major EHR/PM systems including Epic, Cerner, Athenahealth, eClinicalWorks, NextGen, Kareo, AdvancedMD, Allscripts, Greenway Health, and WebPT.'
  ],
  [
    'cat' => 'healthcare',
    'cat_label' => 'Healthcare RCM',
    'badge_color' => 'danger',
    'q'   => 'What is Vortexsoft\'s average first-pass clean claims rate?',
    'a'   => 'We consistently achieve a contract-backed <strong>98.6% clean claims first-pass rate</strong>, minimizing aging accounts receivable and accelerating cash flow for healthcare practices within the first 60 days of onboarding.'
  ],

  // ── 5. AI DATA ANNOTATION & COMPUTER VISION DATASETS ──
  [
    'cat' => 'data',
    'cat_label' => 'Data Annotation',
    'badge_color' => 'success',
    'q'   => 'What types of computer vision data annotation do you provide?',
    'a'   => 'We provide 2D and 3D bounding boxes, polygon segmentation, semantic mask segmentation, polyline lane tracking, keypoint landmark annotation, and video frame object tracking for computer vision model training across automotive, robotics, healthcare, and retail AI.'
  ],
  [
    'cat' => 'data',
    'cat_label' => 'Data Annotation',
    'badge_color' => 'success',
    'q'   => 'How does your team handle 3D LiDAR point cloud labeling for autonomous vehicles?',
    'a'   => 'Our specialized Bengaluru computer vision pods perform sub-centimeter 3D cuboid bounding, point cloud semantic segmentation, multi-sensor radar/camera calibration, and track dynamic objects across diverse weather conditions, urban night driving, and highway scenarios with 99.82% QA precision.'
  ],
  [
    'cat' => 'data',
    'cat_label' => 'Data Annotation',
    'badge_color' => 'success',
    'q'   => 'Do you provide audio, speech, and NLP text dataset annotation?',
    'a'   => 'Yes. We deliver audio phoneme transcription, acoustic event labeling, sentiment tagging, Named Entity Recognition (NER), intent classification, coreference resolution, and Reinforcement Learning from Human Feedback (RLHF) comparative rankings for large language model alignment.'
  ],
  [
    'cat' => 'data',
    'cat_label' => 'Data Annotation',
    'badge_color' => 'success',
    'q'   => 'What annotation platforms and tooling do your teams work in?',
    'a'   => 'Our annotators are skilled across industry-standard annotation toolsets including CVAT, Label Studio, Roboflow, V7 Darwin, SuperAnnotate, Amazon SageMaker Ground Truth, Scale AI tools, as well as proprietary web-based annotation platforms built by our clients.'
  ],
  [
    'cat' => 'data',
    'cat_label' => 'Data Annotation',
    'badge_color' => 'success',
    'q'   => 'What quality control methodologies ensure 99.8% annotation accuracy?',
    'a'   => 'We apply a strict three-tier quality methodology: Gold-Standard test batch validation, multi-annotator consensus cross-checks, automated geometric validation scripts, and 100% inspection by senior subject-matter QA auditors before final dataset handover.'
  ],

  // ── 6. CUSTOM SOFTWARE ENGINEERING, CLOUD & ERP ──
  [
    'cat' => 'software',
    'cat_label' => 'Software & Cloud',
    'badge_color' => 'info',
    'q'   => 'What custom software development services does Vortexsoft deliver?',
    'a'   => 'We engineer scalable web applications, enterprise SaaS portals, executive analytics dashboards, mobile applications (iOS/Android via React Native and Flutter), microservice architectures, and secure RESTful/GraphQL APIs tailored to specific enterprise business workflows.'
  ],
  [
    'cat' => 'software',
    'cat_label' => 'Software & Cloud',
    'badge_color' => 'info',
    'q'   => 'What tech stacks and frameworks do your developers work with?',
    'a'   => 'Our digital engineering teams work with modern full-stack technologies including Next.js, React, Vue, Node.js, PHP (Laravel/Symfony), Python (Django/FastAPI), Go, PostgreSQL, MySQL, MongoDB, Redis, Docker, Kubernetes, AWS, Google Cloud, and Microsoft Azure.'
  ],
  [
    'cat' => 'software',
    'cat_label' => 'Software & Cloud',
    'badge_color' => 'info',
    'q'   => 'Can we hire a dedicated pod of offshore software developers or QA engineers?',
    'a'   => 'Yes. Vortexsoft provides flexible dedicated offshore pods (Full-Stack Developers, DevOps Engineers, QA Automation Leads, UI/UX Designers) dedicated 100% to your product roadmap. Pods operate with daily standups, Jira/Linear sprint tracking, and direct Slack or Teams communication.'
  ],
  [
    'cat' => 'software',
    'cat_label' => 'Software & Cloud',
    'badge_color' => 'info',
    'q'   => 'Do you offer ERP consulting and SAP implementation services?',
    'a'   => 'Yes. We deliver custom ERP module engineering, legacy ERP data migration, workflow automation, and SAP system integration, connecting front-office CRM and eCommerce platforms directly to core enterprise inventory and financial ledgers.'
  ],
  [
    'cat' => 'software',
    'cat_label' => 'Software & Cloud',
    'badge_color' => 'info',
    'q'   => 'How is intellectual property (IP) and source code ownership protected?',
    'a'   => 'All intellectual property, proprietary source code, algorithms, and documentation developed during our engagements transfer 100% to the client upon milestone payment. All developers work under strict non-compete and confidentiality agreements on secure company workstations.'
  ],

  // ── 7. STM PUBLISHING PREPRESS & ACCESSIBILITY ──
  [
    'cat' => 'publishing',
    'cat_label' => 'Publishing AI',
    'badge_color' => 'secondary',
    'q'   => 'What is S1000D XML conversion and what industries require it?',
    'a'   => 'S1000D is an international XML specification for technical publications, widely mandated across aerospace, defense, military logistics, and heavy transport equipment. Vortexsoft converts legacy manuals, illustrated parts catalogs, and engineering schematics into modular S1000D data modules for Interactive Electronic Technical Manuals (IETMs).'
  ],
  [
    'cat' => 'publishing',
    'cat_label' => 'Publishing AI',
    'badge_color' => 'secondary',
    'q'   => 'What publishing prepress and conversion services do you provide?',
    'a'   => 'We offer automated and manual typesetting in Adobe InDesign and 3B2, NLM/JATS and BITS XML conversion for academic journals, ePUB3 reflowable and fixed-layout conversions for Amazon Kindle and Apple Books, copy editing, indexing, and preflight print production.'
  ],
  [
    'cat' => 'publishing',
    'cat_label' => 'Publishing AI',
    'badge_color' => 'secondary',
    'q'   => 'How do you ensure digital accessibility compliance (WCAG 2.1 AA / Section 508 / PDF/UA)?',
    'a'   => 'Our digital accessibility team remediates PDFs, eBooks, and websites to satisfy WCAG 2.1 AA, Section 508, and PDF/UA standards. We author pedagogical alt-text for complex scientific diagrams, structure semantic heading tags, render MathML equations, and validate with screen readers like JAWS, NVDA, and PAC 2024.'
  ],
  [
    'cat' => 'publishing',
    'cat_label' => 'Publishing AI',
    'badge_color' => 'secondary',
    'q'   => 'How fast is your publishing turnaround time compared to traditional bureaus?',
    'a'   => 'Through our proprietary automated ingestion and parsing engine (vortexsoftpublishing), we deliver production turnaround up to <strong>5x faster</strong> than traditional publishing bureaus, processing 500-page volumes in days rather than weeks.'
  ],
  [
    'cat' => 'publishing',
    'cat_label' => 'Publishing AI',
    'badge_color' => 'secondary',
    'q'   => 'Can you handle large-volume backlist digitization and legacy journal archives?',
    'a'   => 'Yes. We have successfully converted over 50,000 backlist volumes dating back 40+ years from scanned paper and legacy PDF archives into structured, searchable JATS XML and digital ePUB3 formats for major academic and university presses worldwide.'
  ],

  // ── 8. SECURITY, COMPLIANCE (ISO 27001/HIPAA) & DELIVERY MODELS ──
  [
    'cat' => 'compliance',
    'cat_label' => 'Compliance & SLAs',
    'badge_color' => 'success',
    'q'   => 'Is Vortexsoft Innovations formally ISO certified and government registered?',
    'a'   => 'Yes. Vortexsoft Innovations Private Limited is a <strong>CMMI Level 3 appraised</strong> organization, formally certified under <strong>ISO 27001:2013</strong> (Information Security Management Systems), operates under <strong>ISO 9001:2015</strong> quality guidelines, is fully <strong>HIPAA</strong> compliant for healthcare operations, and is recognized under the <strong>Startup India</strong> initiative by DPIIT, Government of India.'
  ],
  [
    'cat' => 'compliance',
    'cat_label' => 'Compliance & SLAs',
    'badge_color' => 'success',
    'q'   => 'What pricing and contract engagement models do you support?',
    'a'   => 'We offer three flexible contract models: <strong>Transactional / Per-Unit Pricing</strong> (ideal for title searches, medical claims, or per-image annotation), <strong>Dedicated Full-Time Equivalent (FTE) Monthly Retainers</strong> (cost-effective for ongoing pods with guaranteed capacity), and <strong>Fixed-Scope Milestone Contracts</strong> (best for defined software development sprints).'
  ],
  [
    'cat' => 'compliance',
    'cat_label' => 'Compliance & SLAs',
    'badge_color' => 'success',
    'q'   => 'How much cost savings can an enterprise achieve by outsourcing to Vortexsoft?',
    'a'   => 'Global clients typically realize between <strong>40% and 60% operational OPEX savings</strong> compared to in-house hiring in the United States, United Kingdom, and Europe, while eliminating recruitment commissions, employee benefits overhead, and expensive software seat license fees.'
  ],
  [
    'cat' => 'compliance',
    'cat_label' => 'Compliance & SLAs',
    'badge_color' => 'success',
    'q'   => 'Where are Vortexsoft\'s corporate headquarters and global offices located?',
    'a'   => 'Our global corporate headquarters is located at 502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk, Pune, Maharashtra 411041, India. Our primary technology and AI delivery center is at No.125, Ranganath Complex, Madiwala, HSR Layout, Bengaluru 560068, India. We also maintain a United States corporate desk at 30 N Gould St Ste 100, Sheridan, WY 82801, USA.'
  ],
  [
    'cat' => 'compliance',
    'cat_label' => 'Compliance & SLAs',
    'badge_color' => 'success',
    'q'   => 'How can an organization start a pilot or proof of concept (PoC)?',
    'a'   => 'We offer low-friction, risk-free proof of concepts — such as a sample title search batch, 500-chart medical coding audit, or a 14-day software sprint. You can initiate a pilot within 24 to 48 business hours by submitting your request on our website contact form or emailing <a href="mailto:support@vortexsoftinnovations.com">support@vortexsoftinnovations.com</a>.'
  ],
];
?>

<!-- Schema.org FAQPage JSON-LD for Search Engines & AI Crawlers -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    <?php 
    $schemaEntities = [];
    foreach ($faqs as $faq) {
      $schemaEntities[] = json_encode([
        '@type' => 'Question',
        'name'  => $faq['q'],
        'acceptedAnswer' => [
          '@type' => 'Answer',
          'text'  => strip_tags($faq['a'])
        ]
      ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    echo implode(",\n    ", $schemaEntities);
    ?>
  ]
}
</script>

<style>
.faq-page-hero {
  background: linear-gradient(135deg, #080B1A 0%, #1C2280 60%, #0D1035 100%);
  padding: 85px 0 65px;
  position: relative;
  overflow: hidden;
}
.faq-page-hero h1 {
  font-size: clamp(2.2rem, 4vw, 3.2rem);
  font-weight: 800;
  color: #ffffff !important;
  margin-bottom: 12px;
}
.faq-page-hero p {
  color: rgba(255, 255, 255, 0.92) !important;
  font-size: 15.5px;
  max-width: 680px;
  line-height: 1.6;
}
.faq-page-hero .breadcrumb a {
  color: rgba(255, 255, 255, 0.75) !important;
}
.faq-page-hero .breadcrumb-item.active {
  color: #5BA8D4 !important;
}

.faq-filter-btn {
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 30px;
  padding: 8px 18px;
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  transition: all 0.22s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.faq-filter-btn:hover, .faq-filter-btn.active {
  background: #1C2280;
  color: #ffffff !important;
  border-color: #1C2280;
  box-shadow: 0 4px 14px rgba(28, 34, 128, 0.22);
}

.faq-card-standalone {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  margin-bottom: 14px;
  overflow: hidden;
  transition: all 0.25s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}
.faq-card-standalone.open, .faq-card-standalone:hover {
  border-color: #1C2280;
  box-shadow: 0 6px 20px rgba(28, 34, 128, 0.08);
}
.faq-standalone-q {
  width: 100%;
  background: none;
  border: none;
  padding: 22px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  text-align: left;
  cursor: pointer;
}
.faq-standalone-q span.title {
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: #0F172A;
  line-height: 1.4;
  flex: 1;
}
.faq-card-standalone.open .faq-standalone-q span.title {
  color: #1C2280;
}
.faq-standalone-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(28, 34, 128, 0.08);
  color: #1C2280;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  flex-shrink: 0;
  transition: transform 0.3s ease;
}
.faq-card-standalone.open .faq-standalone-icon {
  background: #1C2280;
  color: #fff;
  transform: rotate(45deg);
}
.faq-standalone-a {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.35s ease;
}
.faq-card-standalone.open .faq-standalone-a {
  max-height: 500px;
}
.faq-standalone-a-inner {
  padding: 0 24px 22px;
  font-size: 14.5px;
  color: #334155;
  line-height: 1.8;
  border-top: 1px solid #f1f5f9;
  padding-top: 16px;
}

.faq-search-box {
  background: #ffffff;
  border: 2px solid #e2e8f0;
  border-radius: 14px;
  padding: 12px 20px;
  font-size: 15px;
  width: 100%;
  transition: border-color 0.2s ease;
}
.faq-search-box:focus {
  outline: none;
  border-color: #1C2280;
  box-shadow: 0 0 0 4px rgba(28,34,128,0.08);
}
</style>

<!-- Hero Section -->
<div class="faq-page-hero">
  <div class="container position-relative" style="z-index:2;">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">FAQ</li>
      </ol>
    </nav>
    <div style="display:inline-flex;align-items:center;gap:6px;background:#eef2ff;color:#1C2280;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;margin-bottom:16px;">
      <i class="fas fa-question-circle text-primary"></i> Knowledge &amp; Answers
    </div>
    <h1>Frequently Asked Questions</h1>
    <p>
      Clear, authoritative answers to questions regarding our services as <strong>India's best title abstracting company (40+ happy clients)</strong>, enterprise &amp; SMB AI automations, BPO operations, and compliance standards.
    </p>
  </div>
</div>

<!-- Main Content Area -->
<section class="py-5" style="background:#f8fafc;">
  <div class="container">
    <div class="row gy-4">

      <!-- Left Sidebar: Search & Category Filter -->
      <div class="col-lg-4">
        <div style="position:sticky;top:100px;">
          <!-- Search Box -->
          <div style="background:#fff;border-radius:18px;padding:24px;border:1px solid #e2e8f0;box-shadow:0 4px 20px rgba(0,0,0,0.03);margin-bottom:20px;">
            <label for="pageFaqSearch" style="font-weight:700;font-size:14px;color:#0F172A;margin-bottom:8px;display:block;">
              <i class="fas fa-search text-primary me-1"></i> Search Questions
            </label>
            <input type="text" id="pageFaqSearch" class="faq-search-box" placeholder="e.g. Title abstracting, AI, BPO, ISO..." oninput="filterPageFaqs(this.value)">
            <div id="faqSearchResultCount" style="font-size:12px;color:#64748b;margin-top:8px;"></div>
          </div>

          <!-- Category Filter Pills -->
          <div style="background:#fff;border-radius:18px;padding:24px;border:1px solid #e2e8f0;box-shadow:0 4px 20px rgba(0,0,0,0.03);margin-bottom:20px;">
            <div style="font-weight:700;font-size:14px;color:#0F172A;margin-bottom:14px;">
              <i class="fas fa-filter text-primary me-1"></i> Filter by Category
            </div>
            <div class="d-flex flex-column gap-2" id="faqCategoryFilterGroup">
              <button type="button" class="faq-filter-btn active" onclick="setFaqCat('all', this)">
                <i class="fas fa-th-large"></i> All Questions (<?= count($faqs) ?>)
              </button>
              <button type="button" class="faq-filter-btn" onclick="setFaqCat('title', this)">
                <i class="fas fa-crown text-warning"></i> Title Abstracting &amp; Real Estate
              </button>
              <button type="button" class="faq-filter-btn" onclick="setFaqCat('platforms', this)">
                <i class="fas fa-users-cog text-danger"></i> Proprietary AI Software (vortexsofthrms)
              </button>
              <button type="button" class="faq-filter-btn" onclick="setFaqCat('ai', this)">
                <i class="fas fa-brain text-primary"></i> AI Automations &amp; Workflows
              </button>
              <button type="button" class="faq-filter-btn" onclick="setFaqCat('healthcare', this)">
                <i class="fas fa-heartbeat text-danger"></i> Healthcare BPO &amp; Medical Coding
              </button>
              <button type="button" class="faq-filter-btn" onclick="setFaqCat('data', this)">
                <i class="fas fa-tags text-success"></i> Data Annotation &amp; AI Training
              </button>
              <button type="button" class="faq-filter-btn" onclick="setFaqCat('software', this)">
                <i class="fas fa-code text-info"></i> Software, Cloud &amp; ERP
              </button>
              <button type="button" class="faq-filter-btn" onclick="setFaqCat('publishing', this)">
                <i class="fas fa-book-open text-secondary"></i> STM Publishing &amp; Prepress
              </button>
              <button type="button" class="faq-filter-btn" onclick="setFaqCat('compliance', this)">
                <i class="fas fa-shield-alt text-success"></i> ISO 27001, HIPAA &amp; SLAs
              </button>
            </div>
          </div>

          <!-- Direct Help Card -->
          <div style="background:linear-gradient(135deg,#080B1A 0%,#1C2280 100%);border-radius:18px;padding:26px 22px;color:#fff;box-shadow:0 8px 24px rgba(28,34,128,0.18);">
            <div style="font-size:24px;color:#5BA8D4;margin-bottom:10px;"><i class="fas fa-headset"></i></div>
            <h4 style="font-size:16px;font-weight:800;color:#fff;margin-bottom:6px;">Have a Custom Requirement?</h4>
            <p style="font-size:13px;color:rgba(255,255,255,0.85);line-height:1.6;margin-bottom:16px;">
              Speak with our senior solution architects for a free scope consultation and custom quote.
            </p>
            <a href="contact.php" class="btn btn-sm btn-light fw-bold w-100" style="color:#1C2280;border-radius:8px;padding:9px 16px;">
              Contact Specialists <i class="fas fa-arrow-right ms-1"></i>
            </a>
          </div>

        </div>
      </div>

      <!-- Right Column: Accordion Items -->
      <div class="col-lg-8">
        <div id="faqItemsContainer">
          <?php foreach ($faqs as $idx => $f): ?>
            <div class="faq-card-standalone" data-cat="<?= $f['cat'] ?>" id="faqCard-<?= $idx ?>">
              <button type="button" class="faq-standalone-q" onclick="toggleStandaloneFaq('faqCard-<?= $idx ?>')">
                <span class="title">
                  <span class="badge bg-<?= $f['badge_color'] ?>-subtle text-<?= $f['badge_color'] ?> fw-bold me-2" style="font-size:11px;padding:4px 8px;vertical-align:middle;">
                    <?= $f['cat_label'] ?>
                  </span>
                  <?= htmlspecialchars($f['q']) ?>
                </span>
                <div class="faq-standalone-icon">
                  <i class="fas fa-plus"></i>
                </div>
              </button>
              <div class="faq-standalone-a">
                <div class="faq-standalone-a-inner">
                  <?= $f['a'] ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div id="faqNoMatch" style="display:none;padding:40px;background:#fff;border-radius:16px;text-align:center;border:1px dashed #cbd5e1;">
          <i class="fas fa-search fa-2x text-muted mb-3"></i>
          <h5 style="color:#0F172A;font-weight:700;">No matching questions found</h5>
          <p style="color:#64748b;font-size:14px;margin-bottom:16px;">Try adjusting your search terms or view all categories.</p>
          <button type="button" class="btn btn-sm btn-primary" onclick="setFaqCat('all', document.querySelector('.faq-filter-btn')); document.getElementById('pageFaqSearch').value=''; filterPageFaqs('');">Reset Filter</button>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
function toggleStandaloneFaq(id) {
  var el = document.getElementById(id);
  if (!el) return;
  var isOpen = el.classList.contains("open");
  // Close others if desired, or allow multiple
  el.classList.toggle("open");
}

var activeFaqCat = "all";

function setFaqCat(cat, btn) {
  activeFaqCat = cat;
  var btns = document.querySelectorAll("#faqCategoryFilterGroup .faq-filter-btn");
  btns.forEach(function(b) { b.classList.remove("active"); });
  if (btn) btn.classList.add("active");
  applyFaqFilters();
}

function filterPageFaqs() {
  applyFaqFilters();
}

function applyFaqFilters() {
  var searchVal = (document.getElementById("pageFaqSearch").value || "").toLowerCase().trim();
  var cards = document.querySelectorAll("#faqItemsContainer .faq-card-standalone");
  var visibleCount = 0;

  cards.forEach(function(card) {
    var cat = card.getAttribute("data-cat");
    var text = card.textContent.toLowerCase();

    var matchesCat = (activeFaqCat === "all" || cat === activeFaqCat);
    var matchesSearch = (!searchVal || text.indexOf(searchVal) !== -1);

    if (matchesCat && matchesSearch) {
      card.style.display = "block";
      visibleCount++;
    } else {
      card.style.display = "none";
    }
  });

  var countEl = document.getElementById("faqSearchResultCount");
  if (countEl) {
    if (searchVal) {
      countEl.textContent = "Found " + visibleCount + " question" + (visibleCount === 1 ? "" : "s");
    } else {
      countEl.textContent = "";
    }
  }

  var noMatch = document.getElementById("faqNoMatch");
  if (noMatch) {
    noMatch.style.display = visibleCount === 0 ? "block" : "none";
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
