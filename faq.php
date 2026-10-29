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
  // ── TITLE ABSTRACTING & REAL ESTATE ──
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
    'a'   => 'We actively partner with over 40+ happy clients in the title search, settlement, and abstracting sector, including US title insurance underwriters, regional title agencies, real estate law firms, and mortgage lenders across states like Texas, Florida, California, New York, Pennsylvania, and Georgia.'
  ],
  [
    'cat' => 'title',
    'cat_label' => 'Title Abstracting',
    'badge_color' => 'warning',
    'q'   => 'What types of title search and abstracting reports do you produce?',
    'a'   => 'Our title search division produces Current Owner Searches, Two-Owner Searches, Full 30/40/60-Year Searches, Commercial Title Searches, Environmental Lien & Judgment Searches, Municipal Tax Delinquency Verifications, Easement Plotting, and full Title Commitment / Policy typing into software like SoftPro, Qualia, ResWare, and RamQuest.'
  ],
  [
    'cat' => 'title',
    'cat_label' => 'Title Abstracting',
    'badge_color' => 'warning',
    'q'   => 'What is the turnaround time (SLA) for title search orders?',
    'a'   => 'Standard title search orders are delivered within 24 to 48 business hours. For high-volume or rush orders, we offer express 12-to-24 hour turnaround options with dual-pass quality review by senior title examiners.'
  ],

  // ── AI AUTOMATIONS & IT SERVICES ──
  [
    'cat' => 'ai',
    'cat_label' => 'AI Automations',
    'badge_color' => 'primary',
    'q'   => 'Does Vortexsoft build AI automations for both large-scale enterprises and small-scale businesses?',
    'a'   => 'Yes. Vortexsoft builds custom AI automations tailored for both small-scale agile teams and large-scale multinational enterprises. For SMBs, we implement fast-to-deploy workflow automations, customer service copilots, and robotic lead processors. For large enterprises, we architect complex LLM pipelines, autonomous multi-agent workflows, high-throughput document intelligence, and enterprise-grade microservice backends.'
  ],
  [
    'cat' => 'ai',
    'cat_label' => 'AI Automations',
    'badge_color' => 'primary',
    'q'   => 'What technologies and frameworks do you use for AI automation?',
    'a'   => 'Our AI engineering teams leverage OpenAI models, Anthropic Claude, Google Gemini, LangChain, LlamaIndex, Python, PyTorch, Hugging Face, vector databases (Pinecone, Weaviate, pgvector), robotic process automation (RPA), and computer vision pipelines.'
  ],
  [
    'cat' => 'ai',
    'cat_label' => 'AI Automations',
    'badge_color' => 'primary',
    'q'   => 'What are Vortexsoft\'s proprietary AI platforms?',
    'a'   => 'Vortexsoft has engineered six proprietary enterprise AI platforms: <strong>VortexEXHO</strong> (Workforce Operating System), <strong>vortexHire</strong> (AI resume parsing and talent screening), <strong>vortexKonnect</strong> (call center speech analytics and sentiment monitoring), <strong>Vortexreach</strong> (autonomous B2B sales outreach), <strong>vortexsoftpublishing</strong> (academic XML and accessibility automation), and <strong>vortexsofthrms</strong> (AI HRMS and autonomous payroll).'
  ],

  // ── BPO AUTOMATIONS & HEALTHCARE RCM ──
  [
    'cat' => 'bpo',
    'cat_label' => 'BPO Automations',
    'badge_color' => 'danger',
    'q'   => 'What are BPO automations and how does Vortexsoft implement them?',
    'a'   => 'BPO automations combine human process expertise with artificial intelligence and robotic workflows. Vortexsoft embeds AI in call analytics (vortexKonnect), automated invoice & claim processing, OCR document extraction, and ticket triage, reducing operational cycle times by up to 70% while improving back-office accuracy to 99.8%.'
  ],
  [
    'cat' => 'bpo',
    'cat_label' => 'BPO Automations',
    'badge_color' => 'danger',
    'q'   => 'How does Vortexsoft handle Healthcare BPO and Revenue Cycle Management (RCM)?',
    'a'   => 'Our healthcare division delivers end-to-end medical coding (ICD-10, CPT, HCPCS), insurance billing, claims submission, denial management, and accounts receivable (AR) recovery. All processes operate in dedicated clean-room environments under strict HIPAA compliance, achieving a 98%+ first-pass claim acceptance rate.'
  ],
  [
    'cat' => 'bpo',
    'cat_label' => 'BPO Automations',
    'badge_color' => 'danger',
    'q'   => 'What data annotation and AI training services do you provide?',
    'a'   => 'We provide computer vision and NLP training data annotation: polygon bounding boxes, semantic segmentation, 3D LiDAR point clouds, video tracking, audio transcription, and RLHF (Reinforcement Learning from Human Feedback) text labeling for autonomous vehicles, medical imaging, and eCommerce.'
  ],

  // ── IT & SOFTWARE DEVELOPMENT ──
  [
    'cat' => 'software',
    'cat_label' => 'Software & IT',
    'badge_color' => 'info',
    'q'   => 'What custom software development services does Vortexsoft offer?',
    'a'   => 'We build scalable web and cloud applications using modern full-stack architectures: React, Next.js, Node.js, PHP/Laravel, Python, and cloud infrastructure on AWS, Azure, and Google Cloud. We offer dedicated development pods, fixed-cost MVP builds, and enterprise modernization.'
  ],
  [
    'cat' => 'software',
    'cat_label' => 'Software & IT',
    'badge_color' => 'info',
    'q'   => 'Can we hire a dedicated pod of developers or BPO specialists?',
    'a'   => 'Yes. Vortexsoft provides flexible dedicated offshore pods consisting of developers, QA engineers, project managers, or BPO specialists dedicated 100% to your account, operating seamlessly in your time zone with daily standups and weekly sprint deliverables.'
  ],

  // ── COMPLIANCE, SECURITY & LOCATIONS ──
  [
    'cat' => 'compliance',
    'cat_label' => 'Compliance & Info',
    'badge_color' => 'success',
    'q'   => 'Is Vortexsoft Innovations ISO certified and HIPAA compliant?',
    'a'   => 'Yes. Vortexsoft Innovations Private Limited is formally certified under <strong>ISO 27001:2013</strong> (Information Security Management) and operates under strict <strong>HIPAA</strong> security and privacy standards. We are also registered under the <strong>Startup India</strong> initiative by the Government of India.'
  ],
  [
    'cat' => 'compliance',
    'cat_label' => 'Compliance & Info',
    'badge_color' => 'success',
    'q'   => 'Where are Vortexsoft\'s offices located?',
    'a'   => 'Our corporate headquarters is located at 502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk, Pune, Maharashtra 411041. Our advanced technology delivery center is located at No.125, Ranganath Complex, Madiwala, HSR Layout, Bengaluru 560068. We also operate a United States corporate presence at 30 N Gould St Ste 100, Sheridan, WY 82801, USA.'
  ],
  [
    'cat' => 'compliance',
    'cat_label' => 'Compliance & Info',
    'badge_color' => 'success',
    'q'   => 'How can I get started with a pilot or proof of concept?',
    'a'   => 'You can contact our solutions team via our website contact form, email support@vortexsoftinnovations.com, or call +91-8308906690. We typically offer low-friction pilot engagements (e.g., sample title searches, 500-image annotation test, or discovery sprint) within 48 business hours.'
  ]
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
              <button type="button" class="faq-filter-btn" onclick="setFaqCat('ai', this)">
                <i class="fas fa-brain text-primary"></i> AI Automations (SMB &amp; Enterprise)
              </button>
              <button type="button" class="faq-filter-btn" onclick="setFaqCat('bpo', this)">
                <i class="fas fa-robot text-danger"></i> BPO Automations &amp; Healthcare
              </button>
              <button type="button" class="faq-filter-btn" onclick="setFaqCat('software', this)">
                <i class="fas fa-code text-info"></i> Software &amp; IT Engineering
              </button>
              <button type="button" class="faq-filter-btn" onclick="setFaqCat('compliance', this)">
                <i class="fas fa-shield-alt text-success"></i> ISO 27001 &amp; Compliance
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
