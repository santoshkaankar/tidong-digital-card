/**
 * Call speakTidongWelcome() ONLY after a newly opened chat is confirmed.
 * Browser speech requires an allowed user interaction on some mobile devices.
 * Do not assume a click succeeded: show errors through onStatus.
 */
export function speakTidong(text, onStatus = () => {}) {
  if (!('speechSynthesis' in window) || !('SpeechSynthesisUtterance' in window)) {
    onStatus('इस ब्राउज़र में बोलकर सुनाने की सुविधा उपलब्ध नहीं है।');
    return false;
  }
  window.speechSynthesis.cancel();
  const utterance = new SpeechSynthesisUtterance(text);
  utterance.lang = 'hi-IN';
  utterance.rate = 0.95;
  const voices = window.speechSynthesis.getVoices();
  utterance.voice = voices.find(v => v.lang?.toLowerCase() === 'hi-in') || voices.find(v => v.lang?.startsWith('hi')) || null;
  utterance.onstart = () => onStatus('playing');
  utterance.onend = () => onStatus('ended');
  utterance.onerror = () => onStatus('आवाज नहीं चल सकी। फोन का मीडिया वॉल्यूम और ब्राउज़र की आवाज सेटिंग जाँचें।');
  window.speechSynthesis.speak(utterance);
  return true;
}

export function stopTidongVoice() {
  if ('speechSynthesis' in window) window.speechSynthesis.cancel();
}
