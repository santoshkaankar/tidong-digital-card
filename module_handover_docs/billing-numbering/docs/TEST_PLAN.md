# Required integration tests before installation

1. Outlet A sale, receipt, sale => global serial 1,2,3 and SL serial 1,2.
2. Outlet B has own series starting at 1; different businesses isolated.
3. Reject branch_id whose business_id differs; reject unauthorized staff at integration boundary.
4. Same UUID retry same actor/type/year returns same allocation; changed actor/type/year rejected.
5. A transaction rollback must also roll back counter update and voucher allocation.
6. 50 leaves: #50 book 01, #51 book 02 with serial 0051.
7. Vendor changes book_size from 50 to 100 after 25 issued leaves; confirm deterministic book boundary.
8. FY reset ON and OFF across year rollover; reject backdated FY.
9. Two concurrent requests to same outlet global/typed counters: no duplicate, no deadlock at realistic peak load.
10. Simultaneous retry with identical UUID: handle uniqueness race via caller-level safe retry; do not create second voucher.
11. Prevent modifying issued number and confirm cancellation/reversal does not recycle it.
12. Verify MySQL and Supabase/PostgreSQL compatibility, missing migrations and permission integration.

WARNING: No existing ledger, routes or migrations changed in the master project. No Laravel end-to-end tests have run.
