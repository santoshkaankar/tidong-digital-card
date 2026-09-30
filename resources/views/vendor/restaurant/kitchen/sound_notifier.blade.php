@php
    $activeCallCount = isset($waiterCalls) ? count($waiterCalls) : 0;
    $firstCall = ($activeCallCount > 0 && isset($waiterCalls[0])) ? $waiterCalls[0] : null;
    $tableNum = $firstCall ? ($firstCall->table->table_number ?? '1') : '1';

    $activeCashCount = isset($cashRequests) ? count($cashRequests) : 0;
    $firstCash = ($activeCashCount > 0 && isset($cashRequests[0])) ? $cashRequests[0] : null;
    $cashTableNum = $firstCash ? ($firstCash->table->table_number ?? '1') : '1';
@endphp

<script>
(function () {
    const BEEP_URL = 'https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3';
    const REPEAT_INTERVAL = 120000;

    let audioInstance = new Audio(BEEP_URL);
    
    let waiterCallTimer = null;
    let cashCallTimer = null;

    let currentCallCount = {{ $activeCallCount }};
    let currentTableNumber = "{{ $tableNum }}";

    let currentCashCount = {{ $activeCashCount }};
    let currentCashTableNumber = "{{ $cashTableNum }}";

    let currentLang = localStorage.getItem('kds_voice_lang') || 'hi-IN';

    const HINDI_NUMS = {
        0:'शून्य', 1:'एक', 2:'दो', 3:'तीन', 4:'चार', 5:'पांच', 6:'छह', 7:'सात', 8:'आठ', 9:'नौ', 10:'दस',
        11:'ग्यारह', 12:'बारह', 13:'तेरह', 14:'चौदह', 15:'पंद्रह', 16:'सोलह', 17:'सत्रह', 18:'अठारह', 19:'उन्नीस', 20:'बीस'
    };

    function convertNumbers(text, lang) {
        if (!text) return '';
        if (lang !== 'hi-IN') return text;

        return text.toString().replace(/\d+/g, function(match) {
            let num = parseInt(match, 10);
            return HINDI_NUMS[num] || match;
        });
    }

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
        }
    };

    function speakText(text) {
        if (!('speechSynthesis' in window) || !text) return;

        // Cancel any pending speech queue to prevent wrong speech type/stack overlap
        window.speechSynthesis.cancel();

        setTimeout(() => {
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
        }, 50);
    }

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

    if (currentCallCount > 0) {
        startWaiterLoop();
    } else if (currentCashCount > 0) {
        startCashLoop();
    }

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
            let freshAudio = new Audio(BEEP_URL);
            freshAudio.play().catch(e => console.log('Audio play blocked', e));

            setTimeout(() => {
                let langMap = TRANSLATIONS[currentLang] || TRANSLATIONS['hi-IN'];
                let template = langMap.newOrder || TRANSLATIONS['hi-IN'].newOrder;
                let sanitizedLoc = sanitizeTextForSpeech(locationNo, currentLang);
                let sanitizedItems = sanitizeTextForSpeech(itemsText, currentLang);
                let msg = template.replace('{table}', sanitizedLoc).replace('{items}', sanitizedItems);
                
                speakText(msg);
            }, 500);
        },

        stopAll: stopAllLoops
    };
})();
</script>