@php
    // Yeh code apne aap current route ka naam ya URL detect kar lega
    $currentRouteName = Route::currentRouteName();
    
    if ($currentRouteName) {
        $autoTitle = ucwords(str_replace(['.', '-'], ' ', $currentRouteName));
    } else {
        $segments = request()->segments();
        $autoTitle = count($segments) > 0 ? ucwords(str_replace(['-', '_'], ' ', end($segments))) : 'Tidong Digital Platform';
    }

    $autoDesc = 'Complete guide, instructions, and digital services for ' . $autoTitle . ' on Tidong Digital ecosystem.';
@endphp

<!-- Automatic AI & Search Engine Instant Reader Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "TechArticle",
  "headline": "{{ $autoTitle }}",
  "description": "{{ $autoDesc }}",
  "author": {
    "@type": "Organization",
    "name": "Tidong Marketing Pvt. Ltd.",
    "url": "https://tidong.in"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Tidong Marketing Pvt. Ltd.",
    "logo": {
      "@type": "ImageObject",
      "url": "https://tidong.in/logo.png"
    }
  }
}
</script>