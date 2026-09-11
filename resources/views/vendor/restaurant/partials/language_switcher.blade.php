<div class="input-group input-group-sm style-select">
    <span class="input-group-text bg-white border-end-0"><i class="bi bi-translate text-primary"></i></span>
    <select class="form-select form-select-sm border-start-0 fw-semibold" onchange="window.location.href='/change-language/' + this.value">
        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
        <option value="hi" {{ app()->getLocale() == 'hi' ? 'selected' : '' }}>Hindi (हिंदी)</option>
        <option value="ar" {{ app()->getLocale() == 'ar' ? 'selected' : '' }}>Arabic (العربية)</option>
        <option value="bn" {{ app()->getLocale() == 'bn' ? 'selected' : '' }}>Bengali (বাংলা)</option>
        <option value="de" {{ app()->getLocale() == 'de' ? 'selected' : '' }}>German (Deutsch)</option>
        <option value="es" {{ app()->getLocale() == 'es' ? 'selected' : '' }}>Spanish (Español)</option>
        <option value="fr" {{ app()->getLocale() == 'fr' ? 'selected' : '' }}>French (Français)</option>
        <option value="gu" {{ app()->getLocale() == 'gu' ? 'selected' : '' }}>Gujarati (ગુજરાતી)</option>
        <option value="it" {{ app()->getLocale() == 'it' ? 'selected' : '' }}>Italian (Italiano)</option>
        <option value="ja" {{ app()->getLocale() == 'ja' ? 'selected' : '' }}>Japanese (日本語)</option>
        <option value="kn" {{ app()->getLocale() == 'kn' ? 'selected' : '' }}>Kannada (ಕನ್ನಡ)</option>
        <option value="ko" {{ app()->getLocale() == 'ko' ? 'selected' : '' }}>Korean (한국어)</option>
        <option value="mr" {{ app()->getLocale() == 'mr' ? 'selected' : '' }}>Marathi (मराठी)</option>
        <option value="pa" {{ app()->getLocale() == 'pa' ? 'selected' : '' }}>Punjabi (ਪੰਜਾਬੀ)</option>
        <option value="pt" {{ app()->getLocale() == 'pt' ? 'selected' : '' }}>Portuguese (Português)</option>
        <option value="ru" {{ app()->getLocale() == 'ru' ? 'selected' : '' }}>Russian (Русский)</option>
        <option value="ta" {{ app()->getLocale() == 'ta' ? 'selected' : '' }}>Tamil (தமிழ்)</option>
        <option value="te" {{ app()->getLocale() == 'te' ? 'selected' : '' }}>Telugu (తెలుగు)</option>
        <option value="ur" {{ app()->getLocale() == 'ur' ? 'selected' : '' }}>Urdu (اردو)</option>
        <option value="zh" {{ app()->getLocale() == 'zh' ? 'selected' : '' }}>Chinese (中文)</option>
    </select>
</div>