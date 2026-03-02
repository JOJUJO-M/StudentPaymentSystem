<?php
// config/theme.php
// GLOBAL THEME FILE - Changing this updates the entire UI

$theme = [
    'primary' => '#1e3a8a', // Blue 900 (Academic Blue)
    'secondary' => '#3730a3', // Indigo 800
    'background' => '#f8fafc', // Slate 50
    'sidebar' => '#0f172a', // Slate 900
    'text' => '#1e293b', // Slate 800
    'light_text' => '#ffffff',
    'accent' => '#f59e0b', // Amber 500
    'font_family' => "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
];

/**
 * Function to output CSS variables based on the theme
 */
function get_theme_css($theme)
{
    $css = ":root {\n";
    foreach ($theme as $key => $value) {
        $css .= "  --$key: $value;\n";
    }
    $css .= "}";
    return $css;
}
