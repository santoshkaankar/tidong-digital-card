# TIDONG Public AI Help Only — Review Package V1

**Status: documentation candidate only. Not live integrated, not an AI server or voice agent.**

## User requirement
AI केवल व्यक्ति को वेबसाइट का इस्तेमाल करना सिखाए; कोई private/confidential जानकारी न दिखाए। Help और Internal Technical Documentation पूर्णतः अलग रखें।

## Files
- `public_help/articles.json` — केवल End User UI Steps, Hindi/English sample questions, review status.
- `public_help/ANSWERING_RULES_PUBLIC.md` — Safe Help-Only boundaries.
- `public_help/WELCOME_HI.md` — Website Help Intro.
- `integration_private_not_for_ai/MASTER_INTEGRATION_SECURITY_CONTRACT.md` — केवल Master Chat/developers के लिए, AI को **मत दें**।
- `tests/test_public_help.py` — Static safety/schema tests, actual AI or Laravel security test नहीं।

## Master Integration steps
1. ZIP को review folder में खोलें। `digital` की Shared Files सीधे overwrite न करें।
2. Existing workflow docs, state machines, route catalogs और legal drafts को user-help AI knowledge से अलग रखें। ये केवल engineering review के लिए हैं।
3. Real deployed UI से हर How-to Step जाँचें। `candidate_only` entries publish न करें।
4. Help source को अलग sanitized, versioned retrieval index में रखें; allowlist केवल public-approved help data की हो।
5. Login/role-aware guide और module-feature availability का backend enforcement करें। Help AI कोई order, payment, dispatch, PIN lookup या staff permission action न करे।
6. Negative access, prompt injection, web UI, Hindi/English, speech privacy और stale feature toggle tests के बाद ही release.

**No database migration, live changes, secrets, `.env`, vendor data or AI service keys included.**
