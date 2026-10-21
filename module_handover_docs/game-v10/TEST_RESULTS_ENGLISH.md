# Actual V6 Test Results — 21 October 2026

- Node.js `node --test tests/economy.test.js tests/studio_v4.test.js tests/world_v5.test.js tests/launch_v6.test.js` — **32 passed, 0 failed**.
- Node syntax `node --check` on `logic.js`, `world-logic.js`, `launch-tools.js`, `game.js`, `studio3d.js` — passed.
- `python tests/browser_v6_launch.py` — desktop 1440px and mobile 390px: guided onboarding, first item, furniture placement, JSON backup download, reset, restore, invalid backup rejection; passed, no JavaScript exceptions. Browser storage was **simulated** because this test environment blocks a real browser navigation origin. This is not real on-device storage verification.
- `python tests/browser_smoke.py` — desktop/mobile original game smoke: passed.
- `python tests/browser_v2_features.py` — desktop/mobile inventory, scene and shop flow: passed.
- `python tests/browser_v3_studio.py` — desktop/mobile 3D scene: passed with SOFTWARE 3D.
- `python tests/browser_v4_studio.py` — desktop/mobile owned furniture: passed with SOFTWARE 3D.
- `python tests/browser_v5_garden.py` — desktop/mobile garden and guided walk: passed with SOFTWARE 3D.
- Gameplay previews `V6_Journey_Desktop.png`, `V6_Journey_Mobile.png` were captured from actual Chromium browser tests (not AI concept images).

**Still untested:** Production browsers over HTTPS, GPU WebGL performance, first public users, real browser storage persistence, server security, multiplayer, real purchases. Do not treat this package as deployment ready.
