<?php

namespace App\Services\Kds;

/**
 * Offline/zero-paid-API: only read facts from the already authorized order.
 * If language is ambiguous, ask the user instead of guessing or issuing commands.
 */
final class FreeKitchenAssistant
{
    public function answer(string $question, array $snapshot): array
    {
        $q = mb_strtolower(trim($question), 'UTF-8');
        $q = preg_replace('/\s+/u', ' ', $q) ?? $q;
        if (mb_strlen($q) < 2) {
            return $this->unclear('कृपया पूरा सवाल बोलें या लिखें।');
        }

        if (preg_match('/(?:टेबल|table)\s*(\d+)/iu', $q, $match)) {
            preg_match('/\d+/', (string) ($snapshot['table'] ?? ''), $actual);
            if (! $actual || (int) $match[1] !== (int) $actual[0]) {
                return $this->unclear('यह दूसरी टेबल है। सही टेबल का ऑर्डर खोलें।');
            }
        }

        if (preg_match('/(?:कच्च|जली|खराब|शिकायत|गलत|बदलना|रिफंड|refund|complaint|quality)/iu', $q)) {
            return $this->unclear('यह जटिल अनुरोध है। अभी वेटर या मालिक से बात करें; Paid AI बंद है।');
        }

        if (preg_match('/(?:भेजो|भेजें|लगा दो|जोड़ो|जोड़ना|बढ़ा दो|add\b|तैयार है|बन गई|परोस दिया|लगा दिया)/iu', $q)
            && ! preg_match('/(?:कितन|कुल|total|how many|status|बाकी|बची)/iu', $q)) {
            return ['resolved' => true, 'needs_confirmation' => true,
                'reply' => 'आपने काम करने को कहा है। बदलाव अभी नहीं किया। संबंधित आइटम और मात्रा जाँचकर अधिकृत बटन से पुष्टि करें।'];
        }

        $groups = [
            ['chapati','chapatti','chappati','roti','रोटी','चपाती','फुल्का'],
            ['dal','daal','दाल'],
            ['chai','tea','चाय'],
        ];
        $matches = [];
        foreach (($snapshot['items'] ?? []) as $item) {
            $name = mb_strtolower((string)($item['name'] ?? ''), 'UTF-8');
            $isMatch = $name !== '' && str_contains($q, $name);
            foreach ($groups as $group) {
                $itemGroup = false;
                $questionGroup = false;
                foreach ($group as $alias) {
                    $itemGroup = $itemGroup || str_contains($name, $alias);
                    $questionGroup = $questionGroup || str_contains($q, $alias);
                }
                if ($itemGroup && $questionGroup) $isMatch = true;
            }
            if ($isMatch) $matches[] = $item;
        }
        if (count($matches) > 1) {
            return $this->unclear('कई आइटम मिल रहे हैं। पूरा आइटम नाम बताइए।');
        }

        if ($matches === []) {
            if (preg_match('/(?:पूरे|सभी|सब|पूरा|all|entire)/iu', $q)) {
                $matches = $snapshot['items'] ?? [];
            } else {
                return $this->unclear('कौन-सा आइटम? उसका नाम बताइए ताकि गलत जवाब न दूँ।');
            }
        }

        $metric = 'ordered';
        $name = 'कुल ऑर्डर';
        if (preg_match('/(?:पहुँच|पहुंच|परोस|served|serve)/iu', $q)) {
            $metric = 'served'; $name = 'परोसे';
        } elseif (preg_match('/(?:तैयार|ready|बन चुकी|बन गया)/iu', $q)) {
            $metric = 'ready'; $name = 'तैयार';
        } elseif (preg_match('/(?:अतिरिक्त|extra|added|और कितन)/iu', $q)) {
            $metric = 'added'; $name = 'अतिरिक्त';
        } elseif (preg_match('/(?:बाकी|शेष|remaining|pending)/iu', $q)) {
            $metric = 'remaining_to_prepare'; $name = 'बनना बाकी';
        } elseif (!preg_match('/(?:कितन|कुल|total|ऑर्डर|order|गिन)/iu',$q)) {
            return $this->unclear('Free Mode में यह बात स्पष्ट नहीं है। प्रश्न दोबारा लिखें।');
        }

        $lines = [];
        foreach ($matches as $item) {
            $lines[] = (string)$item['name'].': '.$name.' '.(int)($item[$metric] ?? 0);
        }
        return [
            'resolved' => true, 'needs_confirmation' => false,
            'reply' => ($snapshot['table'] ?? 'टेबल').': '.implode('। ', $lines).'।',
        ];
    }

    private function unclear(string $message): array
    {
        return ['resolved' => false, 'needs_confirmation' => false, 'reply' => $message];
    }
}
