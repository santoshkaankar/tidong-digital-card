<style>
    /* Self-contained auth styling: login and registration must not depend on
       a running Vite server or a generated public/build/manifest.json. */
    *, *::before, *::after { box-sizing: border-box; }
    html { font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif; }
    body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
           background: #f1f5f9; padding: 16px; color: #1e293b; }
    body > div { width: 100%; max-width: 448px; background: white; border-radius: 16px;
                 padding: 32px; box-shadow: 0 10px 30px rgba(15, 23, 42, .1); }
    h1 { font-size: 28px; line-height: 1.2; margin: 0; font-weight: 750; }
    h1 + p { margin: 8px 0 0; color: #64748b; font-size: 14px; }
    .mb-6 { margin-bottom: 24px; }
    form > div { margin-bottom: 20px; }
    form label { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 600; }
    form input:not([type="checkbox"]) { display: block; width: 100%; padding: 12px 14px;
       border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; outline-offset: 2px; }
    form input:focus-visible { outline: 2px solid #6366f1; }
    form input[type="checkbox"] { vertical-align: middle; accent-color: #4f46e5; }
    form .flex { display: flex; gap: 10px; align-items: center; }
    form .flex label { margin: 0; font-weight: 400; }
    form button { cursor: pointer; display: block; width: 100%; padding: 13px;
      border: 0; background: #4f46e5; border-radius: 8px; color: white; font: inherit; font-weight: 700; }
    form button:hover, form button:focus-visible { background: #4338ca; }
    a { color: #4f46e5; font-weight: 600; text-decoration: none; }
    a:hover, a:focus-visible { text-decoration: underline; }
    p.text-center, div.text-center { text-align: center; }
    p.mt-6 { margin-top: 24px; margin-bottom: 0; font-size: 14px; color: #475569; }
    .bg-red-50 { margin-bottom: 20px; padding: 16px; background: #fef2f2;
        border-radius: 8px; color: #b91c1c; font-size: 14px; }
    .bg-red-50 p { margin: 0 0 8px; font-weight: 600; }
    .bg-red-50 ul { margin: 0; padding-left: 24px; }
    @media (max-width: 480px) { body > div { padding: 24px 18px; } }
</style>
