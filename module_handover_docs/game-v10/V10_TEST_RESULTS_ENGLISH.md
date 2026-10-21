# V10 — Actual test results (2026-10-21)

- JS syntax checked: `town.js`, `story.js`, and `story-logic.js` — pass.
- **57/57** Node logic tests passed, including **11 new V10 assertions** of story/NPC proximity, duplicate conversation, sanitization, milestones, original game ownership, and unchanged demo coin balance.
- Desktop (1365×820) + Mobile simulated (390×844) Chromium, using in-memory HTML/CSS/JS injection due to browser restrictions on direct local file/navigation:
  - Town visible, four NPCs visible, near-Mira talk works, story namespace saved, dialogue displayed, completed character shown.
  - Story Journal opens with six chapters; only actual Plaza + Mira milestone marked done (2/6). Chapter links preserved. No observed page JavaScript exceptions.
  - Original Home Game entry works in both layouts; demo subscription state persisted; duplicate shortcut links cleaned; no observed page JavaScript exceptions.
- All V1–V9 source and tests retained in archive. Original V9 test suite remained green.
- Archive CRC/file extraction integrity checked after packaging.

**Not tested / approved:** native Android and iPhone, direct origin browsing of unzipped HTML from this restricted browser environment, WebGL GPU matrix, real-world performance, user accessibility audit, public multiplayer, server-authoritative saves, financial legality, production Laravel integration, live hosting or public release. Master Review and device UAT mandatory.
