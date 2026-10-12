<!-- Tidong AI Voice Assistant Widget -->
<div id="aiVoiceWidget" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
    <button id="micBtn" class="btn btn-danger rounded-circle shadow-lg d-flex align-items-center justify-content-center p-0" onclick="toggleTidongVoice()" style="width: 52px; height: 52px; border: 2px solid #fff;" title="Tidong se boliye">
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
        <span id="voiceTextStatus" class="fw-semibold small mb-0">Tidong sun raha hai, boliye...</span>
    </div>
</div>

<script>
    let tidongRecognition;
    let isTidongListening = false;

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        tidongRecognition = new SpeechRecognition();
        tidongRecognition.continuous = false;
        tidongRecognition.lang = 'hi-IN'; // Hinglish & Hindi support
        tidongRecognition.interimResults = false;

        tidongRecognition.onstart = function() {
            isTidongListening = true;
            document.getElementById('micBtn').classList.remove('btn-danger');
            document.getElementById('micBtn').classList.add('btn-dark');
            document.getElementById('voiceStatusBox').style.display = 'block';
            document.getElementById('voiceTextStatus').innerText = "Tidong sun raha hai, boliye...";
        };

        tidongRecognition.onresult = function(event) {
            let userTranscript = event.results[0][0].transcript;
            document.getElementById('voiceTextStatus').innerText = `Suna: "${userTranscript}"`;
            sendTidongCommand(userTranscript);
        };

        tidongRecognition.onerror = function(event) {
            speakTidongResponse("Awaaz samajh nahi aayi, kripya dobara bolein.");
            stopTidongListening();
        };

        tidongRecognition.onend = function() {
            setTimeout(() => {
                if(!isTidongListening) {
                    document.getElementById('voiceStatusBox').style.display = 'none';
                    document.getElementById('micBtn').classList.remove('btn-dark');
                    document.getElementById('micBtn').classList.add('btn-danger');
                }
            }, 1500);
        };
    }

    function toggleTidongVoice() {
        if (!tidongRecognition) {
            alert("Aapka browser Voice Recognition support nahi karta. Google Chrome use karein.");
            return;
        }
        if (!isTidongListening) {
            try {
                tidongRecognition.start();
            } catch (e) {
                console.log(e);
            }
        } else {
            tidongRecognition.stop();
            stopTidongListening();
        }
    }

    function stopTidongListening() {
        isTidongListening = false;
        document.getElementById('micBtn').classList.remove('btn-dark');
        document.getElementById('micBtn').classList.add('btn-danger');
    }

    function sendTidongCommand(userSpeechText) {
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
            speakTidongResponse(data.bot_reply);
            document.getElementById('voiceTextStatus').innerText = data.bot_reply;
            
            if (data.intent === 'order') {
                setTimeout(() => {
                    location.reload();
                }, 3500);
            }
        })
        .catch(err => {
            speakTidongResponse("Mafi chahta hu, process karne me dikkat aayi.");
            document.getElementById('voiceStatusBox').style.display = 'none';
        });
    }

    function speakTidongResponse(text) {
        window.speechSynthesis.cancel();
        let utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'hi-IN';
        utterance.pitch = 0.6; // Deep and clear tone for mobile
        utterance.rate = 0.95; 
        window.speechSynthesis.speak(utterance);
    }
</script>