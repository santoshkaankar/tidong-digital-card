<!-- Global Floating AI Voice Assistant Widget -->
<div id="aiVoiceWidget" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
    <button id="micBtn" class="btn btn-danger rounded-circle shadow-lg d-flex align-items-center justify-content-center p-0" onclick="toggleGlobalVoiceAssistant()" style="width: 50px; height: 50px; border: 2px solid #fff;" title="Bolkar Puchiye ya Order Karein">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-mic-fill text-white" viewBox="0 0 16 16">
            <path d="M5 3a3 3 0 0 1 6 0v5a3 3 0 0 1-6 0V3z"/>
            <path d="M3.5 6.5A.5.5 0 0 1 4 7v1a4 4 0 0 0 8 0V7a.5.5 0 0 1 1 0v1a5 5 0 0 1-4.5 4.975V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 .5-.5z"/>
        </svg>
    </button>
</div>

<!-- Voice Status Box -->
<div id="voiceStatusBox" class="position-fixed bottom-0 start-50 translate-middle-x mb-4 shadow-lg border-0 text-center px-3 py-2" style="display: none; z-index: 9999; width: 90%; max-width: 320px; border-radius: 30px; background-color: rgba(33, 37, 41, 0.95); color: #fff;">
    <div class="d-flex align-items-center justify-content-center gap-2">
        <div class="spinner-grow spinner-grow-sm text-danger" role="status"></div>
        <span id="voiceTextStatus" class="fw-semibold small mb-0">Sun raha hu, boliye...</span>
    </div>
</div>

<script>
    let globalRecognition;
    let isGlobalListening = false;

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        globalRecognition = new SpeechRecognition();
        globalRecognition.continuous = false;
        globalRecognition.lang = 'hi-IN'; // Hindi & Hinglish support
        globalRecognition.interimResults = false;

        globalRecognition.onstart = function() {
            isGlobalListening = true;
            document.getElementById('micBtn').classList.remove('btn-danger');
            document.getElementById('micBtn').classList.add('btn-dark');
            document.getElementById('voiceStatusBox').style.display = 'block';
            document.getElementById('voiceTextStatus').innerText = "Sun raha hu, boliye...";
        };

        globalRecognition.onresult = function(event) {
            let userTranscript = event.results[0][0].transcript;
            document.getElementById('voiceTextStatus').innerText = `Suna: "${userTranscript}"`;
            
            // Backend par command bhejein
            sendGlobalVoiceCommand(userTranscript);
        };

        globalRecognition.onerror = function(event) {
            console.error("Speech recognition error", event.error);
            speakGlobalResponse("Awaaz saaf nahi aayi, kripya dobara bolein.");
            stopGlobalListening();
        };

        globalRecognition.onend = function() {
            // Turant band na ho kar status maintain karega
            setTimeout(() => {
                if(!isGlobalListening) {
                    document.getElementById('voiceStatusBox').style.display = 'none';
                    document.getElementById('micBtn').classList.remove('btn-dark');
                    document.getElementById('micBtn').classList.add('btn-danger');
                }
            }, 1000);
        };
    }

    function toggleGlobalVoiceAssistant() {
        if (!globalRecognition) {
            alert("Aapka browser Voice Recognition support nahi karta. Google Chrome use karein.");
            return;
        }
        if (!isGlobalListening) {
            try {
                globalRecognition.start();
            } catch (e) {
                console.log(e);
            }
        } else {
            globalRecognition.stop();
            stopGlobalListening();
        }
    }

    function stopGlobalListening() {
        isGlobalListening = false;
        document.getElementById('micBtn').classList.remove('btn-dark');
        document.getElementById('micBtn').classList.add('btn-danger');
    }

    function sendGlobalVoiceCommand(userSpeechText) {
        fetch("{{ route('global.voice.process') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ message: userSpeechText })
        })
        .then(res => res.json())
        .then(data => {
            speakGlobalResponse(data.bot_reply);
            document.getElementById('voiceTextStatus').innerText = data.bot_reply;
            
            // Agar order hua hai toh 3 seconds baad page refresh karega taaki cart dikhe
            if (data.intent === 'order') {
                setTimeout(() => {
                    location.reload();
                }, 3500);
            }
        })
        .catch(err => {
            speakGlobalResponse("Mafi chahta hu, process karne me dikkat aayi.");
            document.getElementById('voiceStatusBox').style.display = 'none';
        });
    }

    function speakGlobalResponse(text) {
        window.speechSynthesis.cancel();
        let utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'hi-IN';
        utterance.pitch = 1.0;
        utterance.rate = 0.95;
        window.speechSynthesis.speak(utterance);
    }
</script>