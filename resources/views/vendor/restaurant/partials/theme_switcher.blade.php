<div class="dropdown me-2">
    <button class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" 
            type="button" id="themeDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px;">
        <i class="bi bi-sun-fill text-warning theme-icon-active" id="currentThemeIcon"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="themeDropdown">
        <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2" onclick="setTheme('light')">
                <i class="bi bi-sun-fill text-warning"></i> Light Mode
            </button>
        </li>
        <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2" onclick="setTheme('dark')">
                <i class="bi bi-moon-stars-fill text-primary"></i> Dark Mode
            </button>
        </li>
        <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2" onclick="setTheme('system')">
                <i class="bi bi-circle-half text-secondary"></i> System Default
            </button>
        </li>
    </ul>
</div>