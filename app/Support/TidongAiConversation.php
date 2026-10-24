<?php

namespace App\Support;

/**
 * Deterministic, cost-free conversational rules.
 * The caller MUST persist greeting_sent per conversation, not per request.
 */
final class TidongAiConversation
{
    public const WELCOME = 'नमस्ते! TIDONG में आपका स्वागत है। मैं TIDONG AI हूँ। बताइए, आपकी क्या सहायता कर सकती हूँ?';

    public static function welcomeIfNew(bool $greetingSent): ?string
    {
        return $greetingSent ? null : self::WELCOME;
    }

    public static function reply(string $utterance): ?string
    {
        $s = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $utterance) ?? ''));
        $s = trim($s, "।!?., \t\n\r");
        if ($s === '') return null;

        if (preg_match('/(तुम्हारा|आपका|तेरा|अपना).{0,16}नाम|what(?: is|\x27s) your name|who are you|तुम कौन हो|आप कौन हो|क्या बुलाऊँ/u', $s)) {
            return 'मेरा नाम TIDONG AI है। मैं TIDONG की डिजिटल सहायक हूँ।';
        }
        if (preg_match('/(तुम|आप).{0,15}(इंसान|रोबोट)|are you (human|real)/u', $s)) {
            return 'मैं इंसान नहीं, TIDONG की AI सहायक हूँ।';
        }
        if (preg_match('/^(नमस्ते|नमस्कार|राम राम|हेलो|हैलो|hello|hi|hey)$/u', $s)) {
            if (in_array($s, ['hello', 'हेलो', 'हैलो'], true)) return 'हैलो!';
            if (in_array($s, ['hi', 'hey'], true)) return 'हाय!';
            return $s . '!';
        }
        if (preg_match('/^(धन्यवाद|शुक्रिया|थैंक यू|thank you|thanks)( जी)?$/u', $s)) {
            return 'आपका स्वागत है!';
        }
        if (preg_match('/^(क्या हाल|कैसे हो|कैसी हो|क्या हाल है|how are you)$/u', $s)) {
            return 'मैं बढ़िया हूँ! आप बताइए, कैसे हैं?';
        }
        return null; // Delegate to context-aware intent resolver; NEVER force a generic fallback.
    }
}
