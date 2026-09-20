@php
    // Yeh code apne aap current route ka naam ya URL utha lega aur usko Sundar Title bana dega
    $currentRouteName = Route::currentRouteName();
    
    // Default fallback titles agar route match na ho
    $autoTitle = ucwords(str_replace(['.', '-'], ' ', $currentRouteName ?? 'Tidong Digital Platform'));
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
    "name": "Tidong Marketing Private Limited",
    "url": "https://tidong.in"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Tidong Marketing Private Limited",
    "logo": {
      "@type": "ImageObject",
      "url": "https://tidong.in/logo.png"
    }
  }
}
</script>