<?php

namespace App\Services\Ai;

/**
 * Deterministic, offline knowledge baseline. No provider calls or user data access.
 * This is an isolated component; Master Chat must review integration.
 */
final class KnowledgeCommonSense
{
    public function answer(string $question, array $context = []): array
    {
        $q = trim(preg_replace('/\s+/u', ' ', $question) ?? '');
        if ($q === '') {
            return $this->reply('कृपया अपना सवाल लिखें।', 'clarification');
        }
        if (mb_strlen($q) > 2000) {
            return $this->reply('सवाल थोड़ा छोटा करके पूछें।', 'validation');
        }
        $lower = mb_strtolower($q);
        if (preg_match('/^(नमस्ते|नमस्कार|hello|hi|hey|हेलो)[!.? ]*$/iu', $lower)) {
            return $this->reply('नमस्ते! आप क्या जानना चाहेंगे?', 'greeting');
        }
        if (preg_match('/^(धन्यवाद|शुक्रिया|thanks|thank you)[!.? ]*$/iu', $lower)) {
            return $this->reply('आपका स्वागत है!', 'courtesy');
        }
        if (preg_match('/(?:भारत की राजधानी|capital of india)/iu', $lower)) {
            return $this->reply('भारत की राजधानी नई दिल्ली है।', 'fact', 'india_capital');
        }
        if (preg_match('/(?:पानी का रासायनिक सूत्र|chemical formula of water|water formula)/iu', $lower)) {
            return $this->reply('पानी का रासायनिक सूत्र H₂O है।', 'fact', 'water_formula');
        }
        if (preg_match('/(?:पृथ्वी का प्राकृतिक उपग्रह|natural satellite of earth)/iu', $lower)) {
            return $this->reply('पृथ्वी का प्राकृतिक उपग्रह चंद्रमा है।', 'fact', 'earth_moon');
        }
        if (preg_match('/(?:उसकी राजधानी|इसकी राजधानी|its capital|उसका नाम क्या है)/iu', $lower)
            && ($context['last_fact'] ?? '') === 'india_capital') {
            return $this->reply('भारत की राजधानी नई दिल्ली है।', 'follow_up', 'india_capital');
        }
        if (preg_match('/(?:आज|अभी|today|current|latest|live|मौसम|weather|कीमत|price|news)/iu', $lower)) {
            return $this->reply('इस सवाल के लिए ताज़ा और सत्यापित जानकारी चाहिए। अभी मेरे पास लाइव स्रोत का परिणाम नहीं है, इसलिए मैं अनुमान नहीं लगाऊँगा।', 'needs_live_source');
        }
        if (preg_match('/(?:मेरे ऑर्डर|my order|my balance|मेरा बैलेंस)/iu', $lower)) {
            return $this->reply('इसके लिए अधिकृत अकाउंट डेटा की जाँच जरूरी है। बिना जाँच मैं कोई स्थिति नहीं बताऊँगा।', 'needs_authorized_data');
        }
        return $this->reply('आपका सवाल समझने के लिए थोड़ा और संदर्भ चाहिए। कृपया बताइए आप किस विषय में जानना चाहते हैं?', 'clarification');
    }

    private function reply(string $text, string $intent, ?string $fact = null): array
    {
        return [
            'text' => $text,
            'intent' => $intent,
            'last_fact' => $fact,
            'free_mode' => true,
            'verified_live' => false,
        ];
    }
}
