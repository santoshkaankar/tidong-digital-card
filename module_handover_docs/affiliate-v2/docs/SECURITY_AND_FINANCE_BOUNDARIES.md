# TIDONG Affiliate - Security and Financial Controls

- Store actual referral sponsor separately from binary placement parent/leg. Never credit system-inferred sponsors or use member count as a payout trigger.
- Enroll inside a DB transaction. Lock `affiliate_settings` row to serialize root chain changes. Maintain a database unique slot constraint. Do not remove unique constraints to fix races.
- Public role assignments are forbidden. Verify identity and customer-only account type server-side; never trust `user_id`, owner role or money amounts from a browser.
- The legacy 14 stages are reference data, not financial triggers. Display no guaranteed income or misleading eligibility.
- An estimate is NOT an accounts payable item; the current percentage is zero by default. Actual payments, wallet changes, fee collection, tax posting, withdrawal and financial liability recognition are absent.
- Payments and registration fees remain disabled regardless of UI visibility. Future release requires direct-selling counsel, CA/CS, tax, privacy and anti-abuse review.
- Cross-outlet sales must use trusted completed-order adapters, normalized eligible amount, verified customer ownership, stable source IDs, refunds/reversals, idempotency and net-of-tax policy; not implemented in this package.
- Unreferred placements require a manually verified First ID. Do not import an arbitrary root, overwrite old placements or reassign sponsors invisibly.
- Referred A/B slot overflow policy needs product-owner instructions; error rather than invent hierarchy.
- Feature flags and a reviewed live rollout are distinct. Keep public `/register` closed until customer-only signup security is tested.
