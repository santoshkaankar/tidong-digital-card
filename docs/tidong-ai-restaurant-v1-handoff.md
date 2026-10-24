# TIDONG AI — Restaurant Voice Assistant V1 (feature-gated review)

## User experience
- Login as an approved Customer; open **TIDONG AI** only after Admin enables both module and feature.
- Customer must explicitly allow GPS for nearby-restaurant lookup. The website does not send raw GPS or customer address to OpenAI.
- Restaurant and menu facts come directly from active, approved outlets and active branch items. Read-only request; no order/payment or transaction tools available.
- Microphone button uses the browser SpeechRecognition API in supported browsers; browser speech synthesis reads replies. **This is not OpenAI full-duplex realtime voice.** Unsupported browsers use the normal text form.
- AI provider key is server-only via `OPENAI_API_KEY`; never expose to frontend, log key or place it in Git.
- Responses API uses `store=false`; hard daily user/platform limits, HTTP timeout and fallback; API charges remain separate from ChatGPT subscription.

## Requirements and safety blockers before public enablement
- Admin must explicitly enable module `ai_restaurant_assistant` and feature `ai.restaurant_assistant`; both default OFF.
- Set server environment OPENAI_API_KEY; e.g. do not commit the actual key. Model setting TIDONG_AI_RESTAURANT_MODEL defaults to `gpt-4.1-mini`.
- Test authenticated Customer role (no operator/vendor access), GPS consent behavior, active/closed/blocked branch responses, item prices, browser mic support, latency/rate limits and provider failure.
- Existing customer registration may still be disabled. Do not open it automatically.
- **No order placement yet.** Voice/order creation requires a separate secure checkout quote, availability check, address verification, final amount and explicit customer confirmation, idempotency, payment/refund rules and regression tests.
- Query is within 12 km by GPS, not routing travel distance. Only businesses with saved branch coordinates are discoverable.
- This code has mocked-provider test cases but they have **not been run against the deployed database** in this review environment.
- Existing /play/ Virtual Life game, payment integrations and public registration are unchanged.
