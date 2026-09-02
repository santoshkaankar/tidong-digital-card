<?php

namespace App\Services;

class TranslationEngineService
{
    // Supported Languages
    public static $languages = [
        'en' => 'English',
        'hi' => 'हिंदी',
        'es' => 'Español',
        'fr' => 'Français',
        'de' => 'Deutsch',
        'ja' => '日本語'
    ];

    // UI Translation Data Dictionary
    public static $dictionary = [
        'welcome' => [
            'en' => 'Welcome to Tidong Services',
            'hi' => 'टिडोंग सर्विसेज में आपका स्वागत है',
            'es' => 'Bienvenido a Servicios Tidong',
            'fr' => 'Bienvenue aux Services Tidong'
        ],
        'select_service' => [
            'en' => 'Select a Service to Continue',
            'hi' => 'आगे बढ़ने के लिए सेवा का चयन करें',
            'es' => 'Seleccione un servicio para continuar',
            'fr' => 'Sélectionnez un service pour continuer'
        ],
        'food_menu' => [
            'en' => 'Food & Restaurant',
            'hi' => 'खाना और रेस्टोरेंट',
            'es' => 'Comida y Restaurante',
            'fr' => 'Nourriture et Restaurant'
        ],
        'taxi_booking' => [
            'en' => 'Taxi & Tourister Vehicle',
            'hi' => 'टैक्सी एवं टूरिस्ट गाड़ी',
            'es' => 'Taxi y Vehículo Turístico',
            'fr' => 'Taxi et Véhicule Touristique'
        ],
        'hotel_booking' => [
            'en' => 'Hotel Room Stay',
            'hi' => 'होटल रूम स्टे',
            'es' => 'Estancia en Hotel',
            'fr' => 'Séjour à l\'Hôtel'
        ],
        'money_exchange' => [
            'en' => 'Money & Currency Exchange',
            'hi' => 'मुद्रा विनिमय (Money Exchange)',
            'es' => 'Cambio de Moneda',
            'fr' => 'Change de Devise'
        ],

        // ------------------ नई टूरिस्ट सर्विसेज (NEW ADDITIONS) ------------------

        // 1. Souvenir, Gifts & Local Handicrafts Shopping (स्मृति चिन्ह एवं हस्तशिल्प)
        'souvenir_handicrafts' => [
            'en' => 'Souvenirs & Local Emporium',
            'hi' => 'हस्तशिल्प एवं स्मृति चिन्ह (Souvenirs)',
            'es' => 'Recuerdos y Artesanías Locales',
            'fr' => 'Souvenirs et Artisanat Local'
        ],

        // 2. Approved Tourist Guides (टूर गाइड सेवाएं)
        'tourist_guides' => [
            'en' => 'Approved Tourist Guides',
            'hi' => 'प्रमाणित टूर गाइड (Tourist Guides)',
            'es' => 'Guías Turísticos Autorizados',
            'fr' => 'Guides Touristiques Agréés'
        ],

        // 3. Sightseeing & Tickets (दर्शनीय स्थल एवं शो टिकटिंग)
        'sightseeing_tickets' => [
            'en' => 'Sightseeing & Entry Tickets',
            'hi' => 'दर्शनीय स्थल एवं शो टिकट',
            'es' => 'Visitas y Entradas',
            'fr' => 'Visites et Billets d\'Entrée'
        ]
    ];

    public static function get($key, $lang = 'en')
    {
        return self::$dictionary[$key][$lang] ?? self::$dictionary[$key]['en'] ?? $key;
    }
}