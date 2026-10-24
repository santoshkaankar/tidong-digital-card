# TIDONG AI — Greeting and identity integration

This is an isolated, additive patch. It does NOT change routes, controllers, existing AI answers, live deployment or payments.

1. Persist `greeting_sent` per unique conversation/session, default false.
2. On creation of a NEW chat, render `TidongAiConversation::welcomeIfNew(false)` as the assistant's first message, then atomically set greeting_sent true. Never repeat on reload or later messages.
3. Play the welcome through `speakTidong` after an allowed user gesture (opening chat counts on supported browsers). If autoplay is blocked, keep a working speaker/retry button. Never claim audio succeeded until `onstart` fires.
4. For subsequent messages, check `TidongAiConversation::reply($text)` first for identity/simple social phrases; when null, continue through the existing context-aware AI/business lookup. Do not replace existing business availability checks.
5. The name is TIDONG AI, not a human identity. The first response starts with नमस्ते, even if the user's first input is a question.
6. Keep greeting and TTS independent: written responses must work when audio is unsupported.

Run `php artisan test --filter=TidongAiConversationTest` after integration. Browser/mobile audio requires manual testing.

Status: isolated component committed to a feature branch; NOT wired into the current UI and NOT deployed.
