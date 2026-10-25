# Institutional Partner Ecosystem Setup

The partner portal extends the existing PHP/MySQL application and reuses its user accounts, courses, payment approvals, dashboard shell, and public layouts.

## Database migration

Back up the database, then apply these migrations in order through the same MySQL connection used by TCM One (phpMyAdmin's SQL import or the MySQL client):

1. `database/migrations/2026_10_25_partner_ecosystem.sql`
2. `database/migrations/2026_10_25_partner_existing_accounts.sql`

The migrations are additive. They add partner directory and membership records, agreement versions, partner batches and student attribution, independent courses, service requests, financial ledger, settlements, activity history, and partner membership roles. Existing user accounts and passwords are preserved when partner access is linked.

The repository does not include an automatic migration runner. Do not rerun the `ALTER TABLE users ... MODIFY role` statement against a modified role enum without reviewing the current schema first. `CREATE TABLE IF NOT EXISTS` statements may safely be rerun after that role update.

## Initial onboarding

1. Sign in to the existing `/admin` dashboard and open **Institutional Partners**.
2. Create a partner with the owner's email. TCM One records a pending agreement. If the email already has a TCM One account, it links that account without changing its password or role. For a new email, enter an initial password for immediate access or leave it blank to send a one-time, 72-hour setup invitation. While approval is pending, the owner can sign in and update the institution profile; operational actions unlock after activation.
3. Review the signed terms; set the agreement active and partner status to **Active** from the partner detail page.
4. Approve the public profile separately after reviewing its content. Only active, published institutions and approved partner courses appear at `/partners`.
5. Partners sign in at `/partner/login` using the shared TCM One authentication and password reset service.

## Financial reconciliation

Approved existing payment submissions tied to reviewed partner student attributions appear in Admin → Institutional Partners → Ledger & Settlements. A finance administrator confirms the partner, collection owner, tax, and gateway charges before creating a verified ledger entry. The applicable active agreement version is snapshotted on each entry. Settlement requests are approved separately; an actual transfer only counts after its NEFT/RTGS reference is recorded.

The ledger starts empty. No historical payment rows are automatically attributed, and pending or unreviewed student submissions do not count as enrollments or revenue.

## Operational boundaries

- The existing authorization model has one `admin` role. Partner operations and finance pages are admin-only; finance/operations subroles and finer-grained permissions require a later expansion of the central admin authorization model.
- Student, batch, independent course, profile, certificate, mentor, visit, and support workflows are integrated. File uploads for partner agreements, course imagery, evidence, and request attachments, CSV imports/exports, reconciliation PDFs, notification routing to partner emails, mentor allocation, certificate issuance, and student attendance/progress integration are not enabled by this first integrated release.
- Public directory enquiries are consent-gated, deduplicated within 24 hours, attributed to the partner/course, and also mirrored to the existing leads inbox.
- Partner payments currently reconcile from approved `payment_submissions`. PayU order payments without a corresponding verified partner attribution are not included automatically.
