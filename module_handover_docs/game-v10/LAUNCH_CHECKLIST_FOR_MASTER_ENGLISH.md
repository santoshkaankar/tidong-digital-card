# TIDONG Virtual Life — First Launch Gate Checklist (not launch approval)

## Scope of first public-facing candidate
- Clearly labelled standalone, single-player, browser-based game with Home, Market, Garden and fictional item collection.
- No real currency, cashout, affiliate credits, customer financial data, or production Wallet connection.
- No claims that generated cinematic concept videos show the actual game's current graphics.
- No unrestricted chats, multiplayer, real-person messaging, or child friend discovery at first launch.

## Blocking checks before a release decision
1. Master Chat source review, version control and reproducible integration plan; ensure existing Laravel routes/modules remain unchanged until authorized.
2. Full manual touch playtest on low- and mid-range Android phones, iOS Safari and a desktop browser; measure frame rate, memory, battery, load time and offline save behavior.
3. App security review: content security policy, safe asset delivery, no sensitive data in localStorage, error monitoring without collecting user data by default.
4. Accessibility and localization readiness; controls and on-screen guidance must be usable on small screens.
5. Legal/branding review of game images, sounds and product references; clarify when an item is a virtual representation versus a real purchasable product.
6. Hosting staging preview, rollback and recovery plan; validate file storage, HTTPS and rate-limits when server features appear.
7. Usability review with a small consented test audience before a broader release; do not use demo balances as a real wallet or account entitlement.

## Not in V6
Production login, server-side progression, multiplayer safety architecture, realistic high-fidelity graphics, photorealistic motion, 3D on real GPU hardware, financial integration or public deployment.
