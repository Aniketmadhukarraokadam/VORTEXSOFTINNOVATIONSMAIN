# 08 — AI Coding Rules & Change Safety Protocol
**Vortexsoft Innovations Pvt. Ltd.**
*To be followed by all AI coding agents — August 2026*

---

## Core Principle

> **PRESERVE WHAT WORKS. UNDERSTAND BEFORE CHANGING. DOCUMENT BEFORE REBUILDING. MAKE SMALL, TESTED, REVERSIBLE CHANGES.**

---

## 🚫 Absolute Prohibitions (NEVER Do Without Explicit Written Approval)

1. **Never rebuild from scratch** — Do not rewrite existing pages because you'd implement them differently.
2. **Never delete existing files** — Even if a file appears unused, document it first and get approval to delete.
3. **Never rename files, routes, folders** — Renames break links, sitemaps, and server configs.
4. **Never change database column names** — Column renames require schema migrations and break all existing queries.
5. **Never change API endpoint URLs** — `/api/contact.php`, `/api/apply.php`, `/api/newsletter.php` are public-facing. Changing paths breaks forms.
6. **Never delete existing CSS classes** — Classes are used across 75+ pages. Unknown if a class is truly unused without full grep.
7. **Never push credentials, `.env` files, or secrets to GitHub** — Always verify `.gitignore` before committing.
8. **Never commit directly to `main` branch** without user approval — Discuss changes, then implement.
9. **Never simplify or rewrite** the brute-force protection in `admin/login.php` — It is a security-critical implementation.
10. **Never change `config/constants.php`** without understanding every constant that depends on it.

---

## ✅ Safe Operations (Can Do Without Special Approval)

- Adding new files that don't interfere with existing ones
- Adding new PHP functions to `includes/functions.php` at the bottom
- Adding comments and documentation
- Fixing typos in content (not in code logic)
- Adding new database TABLE (not modifying existing ones)
- Adding new routes/pages that don't conflict with existing ones
- Updating `docs/` files
- Improving CSS for a specific, isolated component (with grep verification)

---

## Verification Checklist Before Making Any Code Change

Before editing any file, answer ALL of these:

- [ ] **Which file** am I editing? Is it in the Critical Files list?
- [ ] **Which function/class** am I modifying? Are there callers in other files?
- [ ] **What does this file currently do?** (Read the file before editing)
- [ ] **What other files include/require this file?** (grep for `require_once` and `include`)
- [ ] **Will this change break any existing route, query, or link?**
- [ ] **Is this a reversible change?** (Can I undo it easily?)
- [ ] **Does this change affect mobile behavior?** (Check responsive CSS)
- [ ] **Does this change affect SEO?** (Check title, meta, canonical, hreflang)
- [ ] **Does this change affect admin sessions?** (Check `ADMIN_SESSION` constant usage)
- [ ] **Is the file I'm editing committed to Git?** (Check git status before and after)

---

## Critical Files — Extra Care Required

| File | Why Critical | Allowed Changes |
|---|---|---|
| `config/constants.php` | All app URLs, emails, paths, credentials resolved here | Only add new constants; never change existing constant names |
| `config/database.php` | Singleton PDO connection for the entire app | Only add new DB options; never change `getDB()` signature |
| `includes/functions.php` | CSRF, session, auth, rate-limit — security-critical | Add new functions at bottom; don't refactor existing ones |
| `includes/header.php` | SEO meta, canonical, hreflang, nav — affects ALL pages | Small targeted edits only; test canonical output after |
| `includes/footer.php` | Scripts, service worker registration, trust marks | Small targeted edits only |
| `admin/login.php` | Brute-force protection, session security | Do not simplify; do not remove security checks |
| `.htaccess` | Server-wide security, redirects, compression | Test thoroughly; wrong rules can take down the site |
| `database/setup.sql` | Defines all table schemas and seed data | Only add new tables; never modify existing CREATE TABLE |

---

## Code Style Conventions (Confirmed from existing code)

| Aspect | Convention |
|---|---|
| PHP opening tag | `<?php` (no short tags) |
| Indentation | 4 spaces |
| PHP functions | `snake_case` |
| PHP constants | `UPPER_CASE` |
| PHP classes | None currently used |
| CSS classes | `kebab-case` |
| JS functions | `camelCase` |
| HTML id attributes | `camelCase` or `kebab-case` |
| Error handling | `try/catch` with `error_log()` — never echo raw errors |
| SQL queries | PDO prepared statements — ALWAYS |
| Escaping output | `htmlspecialchars()` — ALWAYS |
| Session usage | Always call `session_start()` if session status is not active |

---

## How to Add a New Admin Page

1. Copy the structure from an existing admin page (e.g., `contacts.php`)
2. Start with `session_start()` and all 3 `require_once` includes
3. Call `admin_check()` immediately
4. Use `admin_require_role()` for any role-restricted operations
5. Add `noindex, nofollow` in the robots meta tag
6. Add a sidebar link in ALL other admin pages (or create a shared admin navigation include)
7. Test that navigating to the page without being logged in redirects to login

---

## How to Add a New Public Page

1. Copy `privacy.php` or `about.php` as a template
2. Set `$page_title`, `$meta_description`, and `$canonical_url` at the top
3. `require_once 'includes/header.php'` and `require_once 'includes/footer.php'`
4. Add the page to `sitemap.xml` with proper `<lastmod>` and `<priority>`
5. Add a link to `robots.txt` if needed (usually not needed)
6. Add to the `sw.js` CRITICAL cache list if the page should be offline-capable
7. Test that the canonical URL is correct for both `.com` and `.in`

---

## Database Change Protocol

1. **Adding a NEW table**: Add `CREATE TABLE IF NOT EXISTS` to `setup.sql`; test locally first
2. **Adding a NEW column**: Use `ALTER TABLE ... ADD COLUMN IF NOT EXISTS` syntax; never use `MODIFY` without documenting the old definition
3. **Never drop columns** — mark as deprecated; coordinate with production DB backup
4. **Always use transactions** for multi-step writes
5. **Always use `utf8mb4`** for any new table or column storing user text

---

## Git & Deployment Protocol

1. **Never force-push to `main`**
2. **Commit message format**: `[TYPE]: Short description` — e.g., `[FIX]: Remove hardcoded deploy token`, `[FEAT]: Add blog post detail page`, `[DOCS]: Add technical architecture doc`
3. **Review before commit**: Run `git diff` and check for secrets, test data, or debug code
4. **After any push to `main`**: The webhook auto-deploy may trigger. Verify the production site after any push.
5. **Before editing `.htaccess`**: Backup the current file; test on staging if possible
