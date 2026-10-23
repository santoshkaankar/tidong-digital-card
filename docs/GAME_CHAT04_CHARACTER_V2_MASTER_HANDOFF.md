# Character & Real Life V2 — Master Chat 02 handoff (2026-10-23)

**Branch:** `feature/game-character-life-chat04-v2-engine-20261023`.
**Scope:** isolated new character files only. `main`, current live `/play/`, database and payment systems must remain unchanged until Master integration review.

## What this provides
- Tested six-stat character life state machine; bounded needs, walk/run pace, rest/sleep/wash, food/milk receipt consumption adapter, cosmetic routine, V1 save migration.
- Static scene obstacle-aware walking, pathfinding, destination activities in Three-dimensional WebGL Studio (new independent `studio3d-character.*` files; does NOT replace or edit existing `studio3d.*`).
- 33 offline unit/integration tests from the prior V2 patch with V11 rule fixtures.

## Required before enabling
1. Merge/verify authoritative V11 game logic and access gating first. Current GitHub `main` uses V2 demo logic, **not** V11. V2 bridge reads `tidong.virtual-life.demo.v3` and requires the V11 `TidongLogic.canPlay()` and `TidongLogic.consume()` APIs. Without these, Character Life must remain disabled.
2. V11 prerequisite scripts: `logic.js`, `access-gate.js`, `studio-logic.js`, `world-logic.js`. The new HTML loads those, then `character-life.js`, `character-navigation.js`, `character-life-bridge.js` and finally `studio3d-character.js`.
3. Compare with `feature/game-character-life-chat02-v13-guided-play` BEFORE merging. It modifies the existing `studio3d.html`/`index.html`; this branch avoids those paths intentionally. Chat02's guide currently reads legacy `demo.v2` and needs V11 state migration before combined release.
4. Master decides where/how to mount game assets. `resources/tidong/game/v10` is source; live public game currently serves `public/play/virtual-life` separately. **No direct live or `/play/` modifications in this PR.**
5. Keep the game isolated from main platform auth/billing, and do not turn public offline demo receipts into production money or server-authorized game state.

## QA
`node --test tests/game-character-v2/*.test.js`
Browser preview for V2 was previously checked with software-mode Chromium; this branch has no verified production WebGL device run. Manual login, live signup/dashboard, live server DB, multiplayer and physical phones are unverified.

**No actual live deploy is claimed.** Master Chat 02 owns approved integration, release gating, Render deployment and smoke verification.