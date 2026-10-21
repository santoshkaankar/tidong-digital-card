# TIDONG Affiliate V2 - Master Review Only

**Do not unzip into the active project or run migrations against `digital_db`.**

This is a review candidate built against the provided selected-file Master snapshot, not the verified current checkout. It adds a root First ID with a concurrency-safe, deterministic **Leg A -> Leg A -> Leg A** chain for customers without a real referral. Existing affiliate V1 members are never automatically reparented. A Master-authorized Super Admin must initialize the genuine first verified customer before enrollment can start.

**Approved finance hold:** registration fee remains INR 0; old 14 stages are reference-only; actual wallet credits, payouts, and withdrawals remain disabled. Estimated commissions do not constitute balances or payable money. Source order integration is NOT connected.

**Release blockers:** The customer public signup endpoint in the supplied Master is still closed. Master must integrate this service into verified customer-only registration atomically and review bot protections before opening public registration. Sponsored branch spillover after A/B direct slots fill also requires the owner's rule; until then that referred slot is rejected rather than silently rerouted. Root reconciliation, MySQL tests, privacy/compliance review, production approval and deployment remain pending.

See `docs/MASTER_INTEGRATION_GUIDE.md` for integration steps. All module UI/code comments use English.
