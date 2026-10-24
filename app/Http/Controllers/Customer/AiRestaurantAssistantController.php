<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\AiRestaurantAssistantService;
use App\Services\Platform\FeatureRegistry;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

final class AiRestaurantAssistantController extends Controller
{
    public function index(): View
    {
        $this->guard();
        return view('customer.ai-restaurant', [
            'answer' => null, 'message' => '', 'restaurants' => [],
            'latitude' => null, 'longitude' => null,
        ]);
    }

    public function newChat(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->guard();
        // Conversation history lives only in the open browser page; nothing to clear server-side.
        return redirect()->route('customer.ai-restaurant.index');
    }

    public function ask(Request $request, AiRestaurantAssistantService $assistant): View|\Illuminate\Http\JsonResponse
    {
        $this->guard();
        $data = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:400'],
            'chat_history' => ['nullable', 'array', 'max:6'],
            'chat_history.*.role' => ['required_with:chat_history', 'in:user,assistant'],
            'chat_history.*.content' => ['required_with:chat_history', 'string', 'max:600'],
            'chat_started' => ['nullable', 'in:0,1'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
        ]);

        $latitude = isset($data['latitude']) ? (float) $data['latitude'] : null;
        $longitude = isset($data['longitude']) ? (float) $data['longitude'] : null;
        // Short-lived, rounded-coordinate cache avoids repeating the same catalogue
        // queries on every chat turn. No location means no database lookup.
        $restaurants = ($latitude !== null && $longitude !== null)
            ? Cache::remember(
                'tidong-ai:nearby:v1:'.round($latitude, 3).':'.round($longitude, 3),
                now()->addSeconds(45),
                fn () => $assistant->nearby($latitude, $longitude)
            )
            : [];

        // Answer conversational questions without forcing location. Coordinates are
        // required only for factual nearby restaurant discovery.
        $question = trim((string) $data['message']);
        $locationHelpIntent = (bool) preg_match('/(लोकेशन|location|gps|जीपीएस).*(कैसे|देने|दें|देना|allow|enable|permission|अनुमति|चालू|on|share)|(?:कैसे|देने|दें|देना|allow|enable|permission|अनुमति|चालू|share).*(लोकेशन|location|gps|जीपीएस)/iu', $question);
        $nearbyIntent = (bool) preg_match('/(near\\s*(?:me|by)|nearest|nearby|around\\s*me|close\\s*by|पास\\s*(?:के|में|वाला|वाले)|नजदीक|नज़दीक|आसपास|लोकेशन|location|मेरे यहाँ|मेरे पास)/iu', $question);
        // Never expose professional work records from a customer AI chat.
        // This endpoint has no verified professional assignment or outlet-scoped
        // report reader. Admin enabling the transparency feature is NOT permission.
        $professionalWorkIntent = (bool) preg_match('/(\\bca\\b|c\\.a\\.|chartered accountant|professional|accountant|gst filing|tax filing|audit report|सीए|प्रोफेशनल|अकाउंटेंट|फाइलिंग|जीएसटी|ऑडिट|मेरे काम की रिपोर्ट)/iu', $question);
        if ($professionalWorkIntent) {
            $answer = app(FeatureRegistry::class)->isEnabled('ai.professional_work_transparency')
                ? 'आप अपने आउटलेट से जुड़े Professional के काम की प्रगति पूछ रहे हैं। इस Customer AI चैट में आपके Outlet और Professional Assignment की सत्यापित जाँच तथा रिपोर्ट पढ़ने की सुविधा अभी उपलब्ध नहीं है। इसलिए मैं कोई प्रगति या Completed होने का दावा नहीं करूँगा। अधिकृत Outlet Dashboard में उपलब्ध रिकॉर्ड देखें।'
                : 'Professional के काम की विस्तृत प्रगति AI से साझा करने की सुविधा अभी Admin द्वारा चालू नहीं है। आप अपने अधिकृत Outlet Dashboard में उपलब्ध स्थिति देख सकते हैं।';
        } elseif ($locationHelpIntent) {
            $answer = 'पास के रेस्टोरेंट खोजने के लिए चैट में 📍 लोकेशन बटन दबाएँ। ब्राउज़र अनुमति माँगे तो Allow करें। अनुमति नहीं दिखे तो ब्राउज़र की Site Settings में Location को Allow करें। सामान्य सवालों के लिए लोकेशन जरूरी नहीं है।';
        } elseif ($restaurants === [] && $nearbyIntent && ($latitude === null || $longitude === null)) {
            $answer = 'अभी मुझे आपकी लोकेशन की अनुमति नहीं मिली है, इसलिए मैं जाँच नहीं कर पा रही हूँ कि आपके आसपास TIDONG पर कौन-से पंजीकृत रेस्टोरेंट उपलब्ध हैं। कृपया 📍 बटन दबाकर लोकेशन शेयर करें या शहर का नाम बताएँ। दूसरे सामान्य सवालों के लिए लोकेशन जरूरी नहीं है।';
        } elseif ($restaurants === [] && $nearbyIntent) {
            $answer = 'आपकी लोकेशन के 12 किमी में अभी ऑर्डर लेने वाला कोई स्वीकृत रेस्टोरेंट नहीं मिला। दूसरे सवाल पूछ सकते हैं।';
        } else {
            $userKey = 'tidong-ai-food:user:'.$request->user()->id.':'.now()->format('Ymd');
            $globalKey = 'tidong-ai-food:global:'.now()->format('Ymd');
            $dailyUser = (int) config('tidong_ai_restaurant.daily_user_requests', 20);
            $dailyGlobal = (int) config('tidong_ai_restaurant.daily_platform_requests', 250);
            $paidEnabled = (bool) config('tidong_ai_restaurant.paid_provider_enabled', false)
                && (string) config('tidong_ai_restaurant.api_key', '') !== '';
            if ($paidEnabled && (RateLimiter::tooManyAttempts($userKey, $dailyUser)
                || RateLimiter::tooManyAttempts($globalKey, $dailyGlobal))) {
                $answer = 'बाहरी AI की आज की सीमा पूरी है। सामान्य ग्राहक सहायता जारी है; My Orders या Explore में संबंधित जानकारी देखें।';
            } else {
                if ($paidEnabled) {
                    RateLimiter::hit($userKey, 86400);
                    RateLimiter::hit($globalKey, 86400);
                }
                $history = (array) ($data['chat_history'] ?? []);
                $result = $assistant->answer($question, $restaurants, $history);
                $answer = $result['text'];
            }
        }

        // Greeting is scoped to this chat, not to every message or every request.
        // Preserve the actual answer to a first-turn question.
        $firstTurn = ($data['chat_started'] ?? '0') !== '1';
        if ($firstTurn) {
            $answer = 'नमस्ते! '.preg_replace('/^\\s*(?:नमस्ते[!।, ]*|नमस्कार[!।, ]*)/u', '', $answer);
        } elseif (preg_match('/^\\s*(good\\s*morning|गुड\\s*मॉर्निंग|सुप्रभात)[!?.। ]*$/iu', $question)) {
            $answer = 'गुड मॉर्निंग!';
        } elseif (preg_match('/^\\s*(good\\s*evening|गुड\\s*इवनिंग|शुभ संध्या)[!?.। ]*$/iu', $question)) {
            $answer = 'गुड इवनिंग!';
        } elseif (preg_match('/^\\s*(नमस्ते|नमस्कार)[!?.। ]*$/u', $question)) {
            $answer = 'नमस्ते!';
        }

        if ($request->expectsJson()) {
            return response()->json(['answer' => $answer]);
        }

        return view('customer.ai-restaurant', [
            'answer' => $answer, 'message' => $data['message'],
            'restaurants' => $restaurants, 'latitude' => $latitude, 'longitude' => $longitude,
        ]);
    }

    private function guard(): void
    {
        abort_unless(
            app(ModuleRegistry::class)->isEnabled('ai_restaurant_assistant')
            && app(FeatureRegistry::class)->isEnabled('ai.restaurant_assistant'),
            404
        );
    }
}
