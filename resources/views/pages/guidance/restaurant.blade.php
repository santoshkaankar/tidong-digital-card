<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>रेस्टोरेंट एवं फूड हब - यूजर गाइड | Tidong® Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.8; }
        .guide-header { background: #0f172a; color: #fff; padding: 50px 0; border-bottom: 4px solid #2563eb; }
        .guide-content { background: #ffffff; border-radius: 16px; border: 1px solid #cbd5e1; padding: 40px; margin-bottom: 40px; }
        .section-title { color: #0f172a; border-left: 4px solid #2563eb; padding-left: 12px; margin-top: 30px; font-weight: 700; margin-bottom: 15px; }
        .step-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="guide-header">
        <div class="container">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="fas fa-arrow-left me-1"></i> मुख्य प्लेटफार्म पर लौटें</a>
            <h1 class="fw-bold mb-2">tidong डिजिटल: रेस्टोरेंट एवं फूड हब - यूजर गाइड</h1>
            <p class="text-light mb-0 small">रेस्टोरेंट, टिफिन सर्विस और स्ट्रीट फूड वेंडर्स के लिए डिजिटल पहचान, मेनू प्रबंधन, पीओएस बिलिंग और लाइव किचन की पूरी मार्गदर्शिका</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="guide-content shadow-sm">

            <!-- Step 1 -->
            <h4 class="section-title">स्टेप 1: वेबसाइट पर रजिस्ट्रेशन करना (Sign Up)</h4>
            <div class="step-box">
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>वेबसाइट विजिट करें:</strong> सबसे पहले अपने ब्राउज़र में <code>tidong.in</code> खोलें, जहाँ आपको tidong डिजिटल का मुख्य पृष्ठ दिखाई देगा।</li>
                    <li><strong>रजिस्टर बटन दबाएं:</strong> ऊपर दाएं कोने पर दिए गए <strong>Register</strong> बटन पर क्लिक करें।</li>
                    <li><strong>विवरण भरें:</strong> रजिस्ट्रेशन फॉर्म में अपना नाम या बिजनेस का नाम, ईमेल, मोबाइल नंबर और पासवर्ड दर्ज करें।</li>
                    <li><strong>अकाउंट प्रकार चुनें:</strong> 
                        <ul class="mt-1">
                            <li>ड्रॉपडाउन से <strong>Business / Service Partner</strong> चुनें।</li>
                            <li>उसके नीचे <strong>Select Your Business Service</strong> में अपने काम के अनुसार <strong>Restaurant (Dine-in / KDS / POS / Tiffin / Street Food)</strong> चुनकर अपना खाता बना लें।</li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Step 2 -->
            <h4 class="section-title">स्टेप 2: अकाउंट लॉगिन करना</h4>
            <div class="step-box">
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>लॉगिन पेज:</strong> यदि आपका खाता पहले से बना हुआ है, तो <code>tidong.in/login</code> पर जाएं।</li>
                    <li><strong>जानकारी दर्ज करें:</strong> अपना ईमेल, मोबाइल नंबर या यूजरनेम और पासवर्ड डालकर <strong>LOG IN</strong> बटन पर क्लिक करें। लॉगिन होते ही आप सीधे अपने <strong>Restaurant Partner Panel (Dashboard)</strong> पर पहुंच जाएंगे।</li>
                </ul>
            </div>

            <!-- Step 3 -->
            <h4 class="section-title">स्टेप 3: डैशबोर्ड को समझना</h4>
            <div class="step-box">
                <p class="text-muted small mb-2"><i class="fas fa-info-circle text-primary me-1"></i> लॉगिन करने के बाद आपको मुख्य डैशबोर्ड दिखाई देगा जहाँ से आप सभी गतिविधियों को नियंत्रित कर सकते हैं:</p>
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>Total Orders:</strong> आज के या अब तक के कुल ऑर्डर्स की संख्या देखें।</li>
                    <li><strong>Active / Running:</strong> जो ऑर्डर्स अभी किचन में या प्रक्रिया में हैं।</li>
                    <li><strong>Completed:</strong> जो ऑर्डर्स सफलतापूर्वक पूरे हो चुके हैं।</li>
                    <li><strong>Today's Revenue:</strong> आज की कुल बिक्री और कमाई ट्रैक करें।</li>
                </ul>
            </div>

            <!-- Step 4 -->
            <h4 class="section-title">स्टेप 4: फूड कैटेगरी और मेनू / थाली आइटम सेट करना</h4>
            <div class="step-box">
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>कैटेगरी जोड़ें:</strong> बाएं मेनू से <strong>Select Category</strong> पर क्लिक करके अपनी भोजन श्रेणियां (जैसे Main Course - Veg, Breads / Roti, Rice & Biryani, Starters, Desserts) प्रबंधित करें।</li>
                    <li><strong>कस्टम आइटम या थाली बनाएं:</strong> मेनू में <strong>Thali / Tiffin Items</strong> या <strong>Create Catalog / Menu Card</strong> पर जाएं।
                        <ul class="mt-1">
                            <li>आइटम या थाली का नाम लिखें (जैसे Special Thali या Paneer Tikka)।</li>
                            <li>कैटेगरी, प्रकार (Veg/Non-Veg), टैक्स रेट और कीमत सेट करें।</li>
                            <li>आइटम की फोटो अपलोड करें और विवरण में लिखें कि उसमें क्या-क्या शामिल है (जैसे 4 रोटी, दाल फ्राई, शाही पनीर, चावल)। इसके बाद <strong>Save Custom Item</strong> पर क्लिक कर दें।</li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Step 5 -->
            <h4 class="section-title">स्टेप 5: पीओएस / काउंटर बिलिंग (ऑर्डर लेना)</h4>
            <div class="step-box">
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>POS स्क्रीन:</strong> बाएं मेनू से <strong>POS / Counter Billing</strong> पर क्लिक करें।</li>
                    <li><strong>ऑर्डर का प्रकार चुनें:</strong> ऑर्डर का प्रकार चुनें (जैसे Dine In, Takeaway, या Tiffin delivery) और यदि Dine-in है तो टेबल नंबर चुनें।</li>
                    <li><strong>आइटम जोड़ें:</strong> मेनू आइटम पर दिए गए <strong>+ Add</strong> बटन पर क्लिक करके आइटम्स को आर्डर कार्ट में जोड़ें।</li>
                    <li><strong>KOT प्रिंट करें:</strong> ग्राहक की जानकारी (नाम/मोबाइल) भरकर <strong>Place Order & Print KOT</strong> पर क्लिक करें। ऑर्डर तुरंत किचन और इतिहास में अपडेट हो जाएगा।</li>
                </ul>
            </div>

            <!-- Step 6 -->
            <h4 class="section-title">स्टेप 6: लाइव किचन डिस्प्ले सिस्टम (KDS)</h4>
            <div class="step-box">
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>KDS स्क्रीन:</strong> यदि किचन में शेफ के पास स्क्रीन लगी है, तो बाएं मेनू से <strong>Live Kitchen (KDS)</strong> खोलें।</li>
                    <li><strong>रियल-टाइम अपडेट्स:</strong> जैसे ही काउंटर से कोई ऑर्डर दिया जाएगा, वह तुरंत यहाँ लाइव दिखने लगेगा ताकि शेफ बिना किसी देरी के खाना बनाना शुरू कर सके।</li>
                </ul>
            </div>

            <!-- Step 7 -->
            <h4 class="section-title">स्टेप 7: ऑर्डर हिस्ट्री और पेमेंट ट्रैकिंग</h4>
            <div class="step-box">
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>Orders History:</strong> बाएं मेनू में <strong>Orders History</strong> पर क्लिक करके आप सभी पुराने और नए ऑर्डर्स की सूची देख सकते हैं।</li>
                    <li><strong>स्थिति ट्रैक करें:</strong> प्रत्येक ऑर्डर के सामने उसकी राशि, भुगतान की स्थिति (Paid या Unpaid), और पूर्ण होने की स्थिति (Completed) दिखाई देती है। यहाँ से आप किसी भी बिल को प्रिंट, देख, संपादित (Edit) या भुगतान कर सकते हैं।</li>
                </ul>
            </div>

        </div>
    </div>

    <footer class="bg-dark text-white py-4 text-center">
        <div class="container">
            <p class="small mb-0 text-muted">&copy; {{ date('Y') }} Tidong Marketing Pvt. Ltd. | Registered Office: Agra, UP, India.</p>
        </div>
    </footer>

</body>
</html>