@php
    $activeCallCount = isset($waiterCalls) ? count($waiterCalls) : 0;
    $firstCall = ($activeCallCount > 0 && isset($waiterCalls[0])) ? $waiterCalls[0] : null;
    $tableNum = $firstCall ? ($firstCall->table->table_number ?? '1') : '1';

    // DEDICATED CASH REQUEST VARIABLES
    $activeCashCount = isset($cashRequests) ? count($cashRequests) : 0;
    $firstCash = ($activeCashCount > 0 && isset($cashRequests[0])) ? $cashRequests[0] : null;
    $cashTableNum = $firstCash ? ($firstCash->table->table_number ?? '1') : '1';
@endphp

<script>
(function () {
    const BEEP_URL = 'https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3';
    const REPEAT_INTERVAL = 120000; // 2 Minutes

    let audioInstance = new Audio(BEEP_URL);
    
    // Timers
    let waiterCallTimer = null;
    let cashCallTimer = null;

    // Waiter Call State
    let currentCallCount = {{ $activeCallCount }};
    let currentTableNumber = "{{ $tableNum }}";

    // Cash Request State
    let currentCashCount = {{ $activeCashCount }};
    let currentCashTableNumber = "{{ $cashTableNum }}";

    // Saved Language (Default: Hindi)
    let currentLang = localStorage.getItem('kds_voice_lang') || 'hi-IN';

    // -------------------------------------------------------------------
    // 1. COMPLETE HINDI NUMBERS MAP
    // -------------------------------------------------------------------
    const HINDI_NUMS = {
        0:'शून्य', 1:'एक', 2:'दो', 3:'तीन', 4:'चार', 5:'पांच', 6:'छह', 7:'सात', 8:'आठ', 9:'नौ', 10:'दस',
        11:'ग्यारह', 12:'बारह', 13:'तेरह', 14:'चौदह', 15:'पंद्रह', 16:'सोलह', 17:'सत्रह', 18:'अठारह', 19:'उन्नीस', 20:'बीस',
        21:'इक्कीस', 22:'बाईस', 23:'तेईस', 24:'चौबीस', 25:'पच्चीस', 26:'छब्बीस', 27:'सत्ताईस', 28:'अट्ठाईस', 29:'उनतीस', 30:'तीस',
        31:'इकतलीस', 32:'बत्तीस', 33:'तैंतीस', 34:'चौंतीस', 35:'पैंतीस', 36:'छत्तीस', 37:'सैंतीस', 38:'अड़तीस', 39:'उनतालीस', 40:'चालीस',
        41:'इकतालीस', 42:'बयालीस', 43:'तैंतालीस', 44:'चवालिस', 45:'पैंतालीस', 46:'छियालीस', 47:'सैंतालीस', 48:'अड़तालीस', 49:'उनचास', 50:'पचास',
        51:'इक्कावन', 52:'बावन', 53:'तिर्पन', 54:'चौवन', 55:'पचपन', 56:'छप्पन', 57:'सत्तावन', 58:'अट्ठावन', 59:'उनसठ', 60:'साठ',
        61:'इकसठ', 62:'बासठ', 63:'तिरसठ', 64:'चौंसठ', 65:'पैंसठ', 66:'छियासठ', 67:'सरसठ', 68:'अड़सठ', 69:'उनहत्तर', 70:'सत्तर',
        71:'इकहत्तर', 72:'बहत्तर', 73:'तिहत्तर', 74:'चौहत्तर', 75:'पचहत्तर', 76:'छिहत्तर', 77:'सतहत्तर', 78:'अठहत्तर', 79:'उनासी', 80:'अस्सी',
        81:'इक्यासी', 82:'बयासी', 83:'तिरासी', 84:'चौरासी', 85:'पचासी', 86:'छियासी', 87:'सत्तासी', 88:'अ्ठासी', 89:'नवासी', 90:'नब्बे',
        91:'इक्यान्वे', 92:'बाण्वे', 93:'तिरान्वे', 94:'चौरान्वे', 95:'पञ्चान्वे', 96:'छियान्वे', 97:'सत्तान्वे', 98:'अट्ठान्वे', 99:'निरांवें',
        100:'सौ', 1000:'हज़ार'
    };

    function convertNumbers(text, lang) {
        if (!text) return '';
        if (lang !== 'hi-IN') return text;

        return text.toString().replace(/\d+/g, function(match) {
            let num = parseInt(match, 10);
            if (HINDI_NUMS[num]) {
                return HINDI_NUMS[num];
            }
            return match;
        });
    }

    // -------------------------------------------------------------------
    // 2. SHORTFORM SANITIZER
    // -------------------------------------------------------------------
    function sanitizeTextForSpeech(text, lang) {
        if (!text) return '';
        let str = text.toString();

        if (lang === 'hi-IN' || lang === 'mr-IN') {
            str = str
                .replace(/\b(no|no\.|num|num\.|number|#)\b/gi, 'नंबर')
                .replace(/#/g, 'नंबर ')
                .replace(/\btbl\b/gi, 'टेबल')
                .replace(/\brm\b/gi, 'रूम')
                .replace(/\bcbn\b/gi, 'केबिन');
        } else {
            str = str
                .replace(/\b(no|no\.|num|num\.|#)\b/gi, 'Number')
                .replace(/#/g, 'Number ')
                .replace(/\btbl\b/gi, 'Table')
                .replace(/\brm\b/gi, 'Room')
                .replace(/\bcbn\b/gi, 'Cabin');
        }

        return convertNumbers(str, lang);
    }

    // -------------------------------------------------------------------
    // 3. TRANSLATIONS (Includes Cash Payment Voice Commands)
    // -------------------------------------------------------------------
    const TRANSLATIONS = {
        'hi-IN': { 
            waiterCall: "ध्यान दें! {table} पर वेटर की आवश्यकता है", 
            cashPayment: "ध्यान दें! {table} से कैश पेमेंट प्राप्त करें",
            newOrder: "नया ऑर्डर आया है {table} से. {items}. धन्यवाद!", 
            updated: "आवाज़ की भाषा हिंदी सेट हो गई है", 
            test: "रसोई प्रदर्शन प्रणाली परीक्षण आवाज़ सक्रिय है" 
        },
        'en-US': { 
            waiterCall: "Attention! Waiter requested at {table}", 
            cashPayment: "Attention! Collect cash payment from {table}",
            newOrder: "New order received from {table}. {items}. Thank you!", 
            updated: "Voice language updated to English", 
            test: "Kitchen Display System Test Voice Active" 
        },
        'mr-IN': { 
            waiterCall: "लक्ष द्या! {table} वर वेटरची गरज आहे", 
            cashPayment: "लक्ष द्या! {table} कडून रोख रक्कम गोळा करा",
            newOrder: "नवीन ऑर्डर आली आहे {table} वरून. {items}. धन्यवाद!", 
            updated: "भाषा मराठी सेट झाली आहे", 
            test: "किचन डिस्प्ले सिस्टम टेस्ट व्हॉईस सक्रिय आहे" 
        },
        'gu-IN': { 
            waiterCall: "ધ્યાન આપો! {table} પર વેઈટરની જરૂર છે", 
            cashPayment: "ધ્યાન આપો! {table} થી રોકડ ચુકવણી મેળવો",
            newOrder: "નવો ઓર્ડર આવ્યો છે {table} થી. {items}. આભાર!", 
            updated: "ભાષા ગુજરાતી સેટ થઈ છે", 
            test: "કિચન ડિસ્પ્લે સિસ્ટમ ટેસ્ટ વૉઇસ" 
        }
    };

    function getNativeNewOrderText(locationText, itemsText) {
        let langMap = TRANSLATIONS[currentLang] || TRANSLATIONS['hi-IN'];
        let template = langMap.newOrder || TRANSLATIONS['hi-IN'].newOrder;

        let sanitizedLocation = sanitizeTextForSpeech(locationText, currentLang);
        let sanitizedItems = sanitizeTextForSpeech(itemsText, currentLang);

        return template.replace('{table}', sanitizedLocation).replace('{items}', sanitizedItems);
    }

    function speakText(text) {
        if (!('speechSynthesis' in window) || !text) return;

        window.speechSynthesis.cancel();

        let utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = currentLang;
        utterance.rate = 0.88;
        utterance.pitch = 1.0;

        let voices = window.speechSynthesis.getVoices();
        let matchedVoice = voices.find(v => 
            v.lang.replace('_', '-') === currentLang || 
            v.lang.startsWith(currentLang.split('-')[0])
        );

        if (matchedVoice) {
            utterance.voice = matchedVoice;
        }

        window.speechSynthesis.speak(utterance);
    }

    // -------------------------------------------------------------------
    // WAITER CALL FUNCTIONS
    // -------------------------------------------------------------------
    function triggerWaiterAlert() {
        if (currentCallCount <= 0) {
            stopWaiterLoop();
            return;
        }

        audioInstance.currentTime = 0;
        audioInstance.play().catch(e => console.log('Audio blocked'));

        setTimeout(() => {
            let langMap = TRANSLATIONS[currentLang] || TRANSLATIONS['hi-IN'];
            let template = langMap.waiterCall || TRANSLATIONS['hi-IN'].waiterCall;
            let sanitizedLocation = sanitizeTextForSpeech(currentTableNumber, currentLang);
            speakText(template.replace('{table}', sanitizedLocation));
        }, 500);
    }

    function startWaiterLoop() {
        stopWaiterLoop();
        if (currentCallCount > 0) {
            triggerWaiterAlert();
            waiterCallTimer = setInterval(triggerWaiterAlert, REPEAT_INTERVAL);
        }
    }

    function stopWaiterLoop() {
        if (waiterCallTimer) {
            clearInterval(waiterCallTimer);
            waiterCallTimer = null;
        }
    }

    // -------------------------------------------------------------------
    // CASH REQUEST FUNCTIONS
    // -------------------------------------------------------------------
    function triggerCashAlert() {
        if (currentCashCount <= 0) {
            stopCashLoop();
            return;
        }

        audioInstance.currentTime = 0;
        audioInstance.play().catch(e => console.log('Audio blocked'));

        setTimeout(() => {
            let langMap = TRANSLATIONS[currentLang] || TRANSLATIONS['hi-IN'];
            let template = langMap.cashPayment || TRANSLATIONS['hi-IN'].cashPayment;
            let sanitizedLocation = sanitizeTextForSpeech(currentCashTableNumber, currentLang);
            speakText(template.replace('{table}', sanitizedLocation));
        }, 500);
    }

    function startCashLoop() {
        stopCashLoop();
        if (currentCashCount > 0) {
            triggerCashAlert();
            cashCallTimer = setInterval(triggerCashAlert, REPEAT_INTERVAL);
        }
    }

    function stopCashLoop() {
        if (cashCallTimer) {
            clearInterval(cashCallTimer);
            cashCallTimer = null;
        }
    }

    function stopAllLoops() {
        stopWaiterLoop();
        stopCashLoop();
        audioInstance.pause();
        audioInstance.currentTime = 0;
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }
    }

    // Auto Sync Dropdown Selection on Load
    document.addEventListener("DOMContentLoaded", () => {
        let topSelect = document.getElementById('kdsTopLangSelect');
        if (topSelect) {
            topSelect.value = currentLang;
        }
    });

    if (currentCallCount > 0) {
        startWaiterLoop();
    } else if (currentCashCount > 0) {
        startCashLoop();
    }

    // Global Notifier Object
    window.KDS_NOTIFIER = {
        changeLanguage: function(langCode) {
            currentLang = langCode;
            localStorage.setItem('kds_voice_lang', langCode);
            let msg = TRANSLATIONS[langCode] ? TRANSLATIONS[langCode].updated : "Language updated";
            speakText(msg);
        },

        updateWaiterCalls: function(count, tableNo = '1') {
            currentCallCount = count;
            currentTableNumber = tableNo;
            if (currentCallCount > 0) {
                startWaiterLoop();
            } else {
                stopWaiterLoop();
            }
        },

        updateCashRequests: function(count, tableNo = '1') {
            currentCashCount = count;
            currentCashTableNumber = tableNo;
            if (currentCashCount > 0) {
                startCashLoop();
            } else {
                stopCashLoop();
            }
        },

        playNewOrderAlert: function(locationNo, itemsText = '') {
            audioInstance.currentTime = 0;
            audioInstance.play().catch(e => console.log('Audio play blocked'));

            setTimeout(() => {
                let msg = getNativeNewOrderText(locationNo, itemsText);
                speakText(msg);
            }, 500);
        },

        testVoice: function() {
            let msg = TRANSLATIONS[currentLang] ? TRANSLATIONS[currentLang].test : TRANSLATIONS['hi-IN'].test;
            speakText(msg);
        },

        stopAll: stopAllLoops
    };
})();
</script>