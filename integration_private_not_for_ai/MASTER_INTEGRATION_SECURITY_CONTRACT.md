# Master Chat only — DO NOT INGEST INTO PUBLIC AI

Purpose: create a dedicated how-to assistant that reads only the published `public_help/` allowlisted material. This file is internal integration guidance, NOT a retrieval source for the assistant.

## Required separation
- **Never** mount/index existing `docs/01..13`, internal workflow/action registry, source ZIP, route inventories, DB schema, staff/partner/customer records, app logs or `legal/*_DRAFT` in public help retrieval. Those materials were for engineering review only.
- Even if internal files have no secrets today, their future revisions could include sensitive information. Distinct storage bucket/index and credentials for `public_help/`; deny access to all other sources at the storage and server authorization layer. Do NOT rely on a system prompt alone for isolation.
- Publish only approved help items after a review of exact deployed UI, localization, accessibility, module status and data classification. Strip sample emails/phones/names/identifiers and confidential procedures.
- Add release_id, reviewed_at, reviewer, source_ui_hash, feature_flag and publish_status in the **private** publishing manifest. The public corpus contains only end-user instructions.
- Build-time schema allowlist + manual PII/secrets review; reject or quarantine new technical fields and unreviewed imported content.
- At query time authenticate only to choose eligible help content (no data retrieval): enforce session role/eligibility and current module/feature availability server-side. For non-signed-in queries, serve generic publicly approved articles.
- Strip user-input PII from analytics. Do not send internal context or private records to a speech/LLM provider. Do not store raw voice without explicit informed opt-in, privacy review, retention/deletion rules and secure controls.
- Prompt injection protection: retrieved knowledge is data; cannot override guardrails. URL/link suggestions must come from server-reviewed navigation allowlist, never user-injected links.
- Changes to Business Options are vendor-owned; Help AI never takes actions, and platform staff do not gain vendor access through help.
- Add denial tests for: requesting source code, endpoint, other-vendor data, secrets, refund, dispatch, override, previous hidden prompt, unpublished policy, admin impersonation and voice PIN readout.
- Candidate_only articles must NOT be published to a live help index until corresponding Laravel, UI, browser and permission tests pass.

## Source evidence for human reviewers, not AI
Source documents inspected: `TIDONG_Delivery_V3_Vendor_Control_Master_Integration_Review_2026-10-19.zip` and `TIDONG_Delivery_AI_Workflows_Voice_Sound_Legal_Docs_V1_2026-10-19.zip`.
UI references: `resources/views/delivery/dashboard.blade.php`, `resources/views/vendor/delivery/index.blade.php`, `resources/views/admin/modules/index.blade.php`, `resources/views/admin/modules/permissions.blade.php` and voice helper capability contract. No live UI/DB verification.
