# TIDONG AI — website-wide role-limited assistant / first KDS workflow

The owner requested TIDONG AI to coordinate the *whole website*, not a keyword-only restaurant bot.
Voice dialogue should be natural and role-aware. An AI model may propose an action but only authenticated
TIDONG services may perform it, with explicit confirmation for orders, financial activity, permission changes
and any other sensitive operations. Never let a model execute arbitrary PHP/SQL or access another outlet.

## Restaurant vocabulary
- **Total ordered:** original quantity plus later additions in the SAME order.
- **Added:** only quantities newly added to the ongoing order.
- **Ready:** kitchen actually confirmed.
- **Served:** waiter actually confirmed at table.
- **Remaining for kitchen:** ordered minus ready.
- **Ready not yet served:** ready minus served.
- A customer asking for two more chapatis adds 2 to existing order, same number, after explicit confirmation.
- No duplicate order or duplicate charges; server computes the revised monetary total.

## Current isolated draft
This branch adds item-quantity progress, audit events and same-order increments on existing items.
The existing KDS owner-only routes are preserved. The read-only per-table summary is scoped
to the owner of that outlet and draws from actual order items, not AI guesses.
The 'add' action currently supports only items already in the order; a new menu item requires
a separate reviewed same-order line-item endpoint.
A direct waiter/chef role workflow, speech transcription, live push notifications and conversational
tool orchestration are NOT finished here. Do not enable or merge until integration tests pass.

## Critical guardrails
- Existing status button synchronizes all item counts when marking a whole order ready/served.
- Staff role authorization requires a dedicated per-outlet permission scheme; never assign vendor-owner access to staff.
- Historic ready/served order rows need a safe data migration/backfill strategy before production.
- Cancel/revise paid orders only through refund/reconciliation workflow.
- Customer spoken order should first be displayed for correction, then confirmed before write.
