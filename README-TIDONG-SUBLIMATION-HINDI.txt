TIDONG SUBLIMATION STUDIO — MODULE 1
====================================
यह अलग Laravel प्रोजेक्ट नहीं है। यह आपके मौजूदा C:\xampp\htdocs\digital के लिए सुरक्षित ADD-ON है।

क्या शामिल है:
- Vendor Dashboard में 'Sublimation / Personalized Gifts' Business Type
- हर Sublimation Business के लिए अलग private Studio link
- Branch-wise Mug / T-Shirt / Cushion / Keychain / Bottle / Frame product catalogue
- Raw blank, ink/paper, pressing, packing cost, sale quote, gross difference
- Customer Custom Job, quantity, due date, printing instructions
- Manual customer design approval note, production status, print job cards
- Status and creation audit; कोई order delete नहीं
- Only approved Vendor account and owning business (Platform Staff excluded)

क्या अभी नहीं है:
- Customer-side order website, Registration, Customer approval portal, photo uploads,
  cloud storage, GST final invoice, B2B prices, wallet/payment posting, vendor employee delegation.
  ये सभी अलग से security/design के बाद होंगे।
- इस ZIP को लगाने से Sublimation Business अपने-आप Create/Approve नहीं होता।

सावधानी:
- यह ZIP snapshot (2026-10-19) के code पर बना है। अगर routes/vendor.php या
  vendor/dashboard.blade.php बाद में बदले हों, तो full file overwrite न करें;
  changes को merge करें।
- इसमें नई additive Migration है। कोई मौजूदा transaction delete/modify नहीं होता।
- पहले MAIN DB का SQL Backup और Source Backup रखें।
- पहले TEST database confirm करें; Test DB का नाम अलग दिखना चाहिए।
- फिर TEST DB में migration चलाकर functional tests और manually UI जाँचें।
- केवल successful validation के बाद Main DB migration और live deployment करें।
- Live में .env, SQL dump, secrets, logs या storage/data Git में push न करें।
- यह 'working source package' है, आपके XAMPP या Render में स्वतः install नहीं हुआ है।

फाइलें:
 app/Http/Controllers/Vendor/SublimationStudioController.php
 routes/sublimation.php
 routes/vendor.php (adds require of separate routes file)
 database/migrations/2026_10_19_090000_create_sublimation_studio_tables.php
 resources/views/vendor/sublimation/index.blade.php
 resources/views/vendor/dashboard.blade.php (business type + link)
 public/images/tidong-studio-logo.jpg (आपका दिया गया Tidong Logo)

Test DB verified हो तो only testing migration path:
 php artisan migrate --env=testing --path=database/migrations/2026_10_19_090000_create_sublimation_studio_tables.php

Browser path after migration and approved Vendor having Sublimation Business and Branch:
 http://localhost/digital/public/vendor/businesses/{business-id}/sublimation

Public Registration Lock और Render/Supabase Deployment इस ZIP का भाग नहीं हैं।

V1.1 — SAMPLE PRODUCT CATALOG
=============================
इस updated package में 12 sample products शामिल करने के लिए अलग Artisan Command है.
यह automatically किसी Branch में नहीं चलता. Target business और branch IDs देना जरूरी है.
Examples (business 12, branch 15 are EXAMPLES, don't run blindly):
  php artisan studio:seed-samples 12 15 --dry-run
  php artisan studio:seed-samples 12 15
सही business/branch IDs जानने के बाद ही run करें.
Only approved sublimation/custom_printing/printing business और उसकी ACTIVE Branch पर seed होता है.
Existing items unchanged, repeat seed duplicate नहीं करता. Sample items price ₹0 और inactive हैं.
Vendor खोलकर Edit Product Prices & Activate में real Cost / Sale Price भरें और Active करें.
No customer job created by seeder. All seed additions included in audit_logs.
Photo example provided by user is NOT auto-published or placed in a product gallery.
V1 से V1.1 में updated controller, view और routes include हैं; current custom changes हों तो MERGE करें.
Source review और actual Laravel tests स्थानीय XAMPP पर चलाना जरूरी है; PHP syntax checks only are not full tests.
