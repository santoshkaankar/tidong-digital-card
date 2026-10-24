# Game Chat 05 — SOCIAL V2 · Master Chat 02 handoff

**Status:** Integration candidate ONLY; no production multiplayer is operational. Based on the CURRENT live `/play/virtual-life` location at commit `57b79bcebafac44a5b2916ffb95126e85e23a40c` (23 Oct 2026). Render Docker app auto-deploys main; DO NOT push this branch to main without Master verification.

## Exact changes

- All files inside `public/play/virtual-life` in this patch have NEW names; NO existing 3D/finance/economy files are overwritten.
- `social-preview.html` runs a responsive 3D scene; when the localhost Node server is absent, users see 3 clearly fictional samples with disabled interactions.
- `social-studio3d.js` is a copy of V11 `studio3d.js` with an isolated social scene adapter, renamed to avoid overwriting the live studio. It must be compared with the CURRENT public studio on Master integration; do NOT silently replace the official game.
- `modules/game-social/local-server.js` binds 127.0.0.1 ONLY, not production. It provides real two-window WebSocket/social invitation tests and WebRTC signaling for local browser microphone testing.
- V2 hardening: accepted-friends-only voice request, response-only-to-pending invites, block clears pending invites, lip level broadcast only to consenting peers. It is still not Internet-safe because players are unauthenticated demo IDs.

## Master route and deployment boundaries

1. Public playtest is already under `/play/virtual-life/`, not `digital/resources/tidong/game/v10`. Do not duplicate old `studio3d.js` on live.
2. `routes/game.php` currently uses Laravel `auth` + `ModuleRegistry::isEnabled('game')` for the separate protected game. This module branch **does not** modify those routes.
3. `public/play/virtual-life` is a static no-login, offline demo: adding these new assets to `main` could expose a public DEMO only, not multiplayer. Keep marketing labelled `demo`; no real voice/online user claims.
4. Actual online social launch REQUIRES Master integration: authenticated users, eligible game access via owner-approved subscription/economy entitlements, Admin default-OFF social feature toggle, persistent friends/requests/invites/blocks/privacy, realtime identity verification and position rules, secure WSS, TURN credentials + voice permission gating, and abuse reporting/moderation.
5. The existing free Render Docker PHP/Apache service is **not** running this Node local socket; merging static assets to main will NOT produce real Internet multiplayer. A separate reviewed signaling service or vetted hosted signaling solution is needed before claiming live voice.
6. Do not publish the standalone local server endpoint or expose the localhost server to network. No databases, accounts, or paid flows are changed here.
7. Final merge and live deployment authority belongs to **Master Chat 02**, after security tests, backups when authorized, and approval. Game Chat 01 coordinates gameplay scope.

## Tests

- `node --test tests/game_social_local.test.js` — localhost WebSocket system and isolation smoke test.
- `node --check public/play/virtual-life/social-preview.js` and `node --check modules/game-social/local-server.js`.
- On a Windows working tree with Node 20+: `node modules/game-social/local-server.js` then two browser windows at `http://127.0.0.1:8743/`.
- No wallet or registration required for **demo**; not proof of service eligibility.

## Conflict check

All `public/play/virtual-life/social-*` files are NEW relative to live commit (expected). No `public/play/virtual-life/index.html`, main game, `routes/web.php`, `routes/game.php`, Dockerfile, finance, or migration changes.

## Outstanding QA

Interactive WebGL renders and hardware mic/WebRTC network traversal must be tested on actual desktop and mobile browsers. TURN setup, scalable rooms, real accounts, server-side auth/privileges, abuse controls and subscription gating are NOT done.