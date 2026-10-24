<?php

namespace App\Services\Customer;

use Carbon\CarbonImmutable;

/** Deterministic India calendar answers; no paid API, chat storage or DB writes. */
final class TidongCalendarAnswers
{
    public static function answer(string $question): ?string
    {
        $q = mb_strtolower(trim($question));
        $today = CarbonImmutable::now('Asia/Kolkata')->startOfDay();
        $days = ['रविवार', 'सोमवार', 'मंगलवार', 'बुधवार', 'गुरुवार', 'शुक्रवार', 'शनिवार'];
        $months = [1=>'जनवरी','फरवरी','मार्च','अप्रैल','मई','जून','जुलाई','अगस्त','सितंबर','अक्टूबर','नवंबर','दिसंबर'];
        $format = static fn (CarbonImmutable $d): string => $d->day.' '.$months[$d->month].' '.$d->year.', '.$days[$d->dayOfWeek];
        if (preg_match('/(आज.*(?:तारीख|दिनांक|कौन सा वार|कौनसा वार|कौन सा दिन|कौनसा दिन)|(?:today|what date|which day).*|आज क्या तारीख)/iu', $q)) {
            return 'आज '.$format($today).' है।';
        }
        if (preg_match('/(कल|tomorrow|yesterday|परसों|परसो|नरसों|नरसो)/iu', $q, $m)) {
            $term = mb_strtolower($m[1]);
            $n = match ($term) { 'yesterday'=>-1, 'tomorrow'=>1, 'नरसों','नरसो'=>3, 'परसों','परसो'=>2, default=>preg_match('/(बीता|गुजरा|गुज़रा|yesterday)/u', $q)?-1:1 };
            return $m[1].' '.$format($today->addDays($n)).' होगा।';
        }
        $numbers = ['एक'=>1,'दो'=>2,'तीन'=>3,'चार'=>4,'पांच'=>5,'पाँच'=>5,'छह'=>6,'सात'=>7,'आठ'=>8,'नौ'=>9,'दस'=>10,'ग्यारह'=>11,'बारह'=>12,'पंद्रह'=>15,'पन्द्रह'=>15,'बीस'=>20,'तीस'=>30];
        if (preg_match('/([0-9]{1,3}|एक|दो|तीन|चार|पांच|पाँच|छह|सात|आठ|नौ|दस|ग्यारह|बारह|पंद्रह|पन्द्रह|बीस|तीस)\s*(दिन|days?|हफ्ते|सप्ताह|weeks?)\s*(बाद|पहले|later|after|ago|before)/iu', $q, $m)) {
            $n = isset($numbers[$m[1]]) ? $numbers[$m[1]] : (int) $m[1];
            $n = min($n, 365);
            $multiplier = preg_match('/(हफ्ते|सप्ताह|week)/iu', $m[2]) ? 7 : 1;
            $delta = preg_match('/(पहले|ago|before)/iu', $m[3]) ? -$n*$multiplier : $n*$multiplier;
            return abs($delta).' दिन '.($delta < 0?'पहले ':'बाद ').$format($today->addDays($delta)).' '.($delta < 0?'था।':'होगा।');
        }
        $fixed = [
            ['/(स्वतंत्रता दिवस|independence day)/iu',8,15,'स्वतंत्रता दिवस'],
            ['/(गणतंत्र दिवस|republic day)/iu',1,26,'गणतंत्र दिवस'],
            ['/(गांधी जयंती|gandhi jayanti)/iu',10,2,'गांधी जयंती'],
            ['/(शिक्षक दिवस|teachers? day)/iu',9,5,'शिक्षक दिवस'],
            ['/(बाल दिवस|childrens? day)/iu',11,14,'बाल दिवस'],
            ['/(संविधान दिवस|constitution day)/iu',11,26,'संविधान दिवस'],
            ['/(अंबेडकर जयंती|ambedkar jayanti)/iu',4,14,'अंबेडकर जयंती'],
            ['/(क्रिसमस|christmas)/iu',12,25,'क्रिसमस'],
            ['/(नया साल|new year)/iu',1,1,'नया साल'],
            ['/(अंतरराष्ट्रीय महिला दिवस|women.s day)/iu',3,8,'अंतरराष्ट्रीय महिला दिवस'],
            ['/(योग दिवस|yoga day)/iu',6,21,'अंतरराष्ट्रीय योग दिवस'],
            ['/(पर्यावरण दिवस|environment day)/iu',6,5,'विश्व पर्यावरण दिवस'],
        ];
        foreach ($fixed as [$pattern,$month,$day,$name]) {
            if (preg_match($pattern, $q)) {
                $date = $today->setDate($today->year, $month, $day);
                if ($date->lessThan($today)) $date = $date->addYear();
                return $name.' हर साल '.$day.' '.$months[$month].' को मनाया जाता है। अगली बार '.$format($date).' होगा।';
            }
        }
        if (preg_match('/(नानक जयंती|गुरु नानक|guru nanak|दिवाली|दीपावली|diwali|holi|होली|रक्षा बंधन|रक्षाबंधन|ईद|eid|पंचांग|तिथि|nakshatra|नक्षत्र|मुहूर्त|मुहूरत)/iu', $q)) {
            return 'इस त्योहार या पंचांग तिथि की तारीख साल, शहर और स्थानीय पंचांग के अनुसार बदल सकती है। अभी मेरे पास सत्यापित लाइव पंचांग उपलब्ध नहीं है, इसलिए गलत तारीख नहीं बताऊँगी। आधिकारिक अवकाश कैलेंडर या स्थानीय पंचांग से पुष्टि करें।';
        }
        if (preg_match('/(आसपास.*त्योहार|अगला त्योहार|आने वाले त्योहार|upcoming festival|next festival)/iu', $q)) {
            return 'आने वाले त्योहारों के लिए साल और शहर के अनुसार सत्यापित कैलेंडर चाहिए। इस चैट में अभी लाइव त्योहार कैलेंडर जुड़ा नहीं है, इसलिए अनुमानित तारीख नहीं बताऊँगी।';
        }
        return null;
    }
}
