<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\AiRestaurantAssistantService;
use App\Services\Platform\FeatureRegistry;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        $request->session()->forget('tidong_ai_food_history');
        return redirect()->route('customer.ai-restaurant.index');
    }

    public function ask(Request $request, AiRestaurantAssistantService $assistant): View|\Illuminate\Http\JsonResponse
    {
        $this->guard();
        $data = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:400'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
        ]);

        $latitude = isset($data['latitude']) ? (float) $data['latitude'] : null;
        $longitude = isset($data['longitude']) ? (float) $data['longitude'] : null;
        $restaurants = $assistant->nearby($latitude, $longitude);

        if ($latitude === null || $longitude === null) {
            $answer = 'नज़दीकी रेस्टोरेंट बताने के लिए नीचे “मेरी लोकेशन लें” दबाकर अनुमति दें। बिना आपकी अनुमति के लोकेशन नहीं ली जाएगी।';
        } elseif ($restaurants === []) {
            $answer = 'आपकी बताई लोकेशन के 12 किमी के दायरे में अभी ऑर्डर स्वीकार करने वाला कोई स्वीकृत रेस्टोरेंट नहीं मिला।';
        } else {
            $userKey = 'tidong-ai-food:user:'.$request->user()->id.':'.now()->format('Ymd');
            $globalKey = 'tidong-ai-food:global:'.now()->format('Ymd');
            $dailyUser = (int) config('tidong_ai_restaurant.daily_user_requests', 20);
            $dailyGlobal = (int) config('tidong_ai_restaurant.daily_platform_requests', 250);
            if (RateLimiter::tooManyAttempts($userKey, $dailyUser)
                || RateLimiter::tooManyAttempts($globalKey, $dailyGlobal)) {
                $answer = 'आज की AI बातचीत की सीमा पूरी हो गई है। वास्तविक रेस्टोरेंट सूची और मेन्यू नीचे देख सकते हैं।';
            } else {
                RateLimiter::hit($userKey, 86400);
                RateLimiter::hit($globalKey, 86400);
                $history = (array) $request->session()->get('tidong_ai_food_history', []);
                $result = $assistant->answer($data['message'], $restaurants, $history);
                $answer = $result['text'];
                $history[] = ['role' => 'user', 'content' => $data['message']];
                $history[] = ['role' => 'assistant', 'content' => $answer];
                $request->session()->put('tidong_ai_food_history', array_slice($history, -6));
            }
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
