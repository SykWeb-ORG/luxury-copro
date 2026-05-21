<?php
declare(strict_types=1);

$root = dirname(__DIR__);

if (!is_dir($root . '/wp-content/themes/luxurycopro-theme')) {
    $docker_root = getenv('WP_PATH') ?: '/var/www/html';
    if (is_dir($docker_root . '/wp-content/themes/luxurycopro-theme')) {
        $root = $docker_root;
    }
}

$source = $root . '/wp-content/themes/luxurycopro-theme/assets/css/main.css';
$target = $root . '/wp-content/themes/luxurycopro-theme/assets/css/main.min.css';

if (!is_readable($source)) {
    fwrite(STDERR, "Unable to read source CSS: {$source}\n");
    exit(1);
}

$css = file_get_contents($source);

if ($css === false) {
    fwrite(STDERR, "Unable to load source CSS: {$source}\n");
    exit(1);
}

$minified = lc_minify_css($css);
$target_dir = dirname($target);

if (!is_dir($target_dir) && !mkdir($target_dir, 0775, true) && !is_dir($target_dir)) {
    fwrite(STDERR, "Unable to create target directory: {$target_dir}\n");
    exit(1);
}

if (file_put_contents($target, $minified . "\n") === false) {
    fwrite(STDERR, "Unable to write minified CSS: {$target}\n");
    exit(1);
}

$source_size = strlen($css);
$target_size = strlen($minified) + 1;
$saved = $source_size > 0 ? round((1 - ($target_size / $source_size)) * 100, 1) : 0;

echo "Generated {$target}\n";
echo "Reduced {$source_size} bytes to {$target_size} bytes ({$saved}% smaller).\n";

function lc_minify_css(string $css): string {
    $css = lc_strip_css_comments($css);
    $css = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $css);

    $output = '';
    $length = strlen($css);
    $quote = null;
    $pending_space = false;

    for ($i = 0; $i < $length; $i++) {
        $char = $css[$i];

        if ($quote !== null) {
            $output .= $char;

            if ($char === '\\' && $i + 1 < $length) {
                $output .= $css[++$i];
                continue;
            }

            if ($char === $quote) {
                $quote = null;
            }

            continue;
        }

        if ($char === '"' || $char === "'") {
            if ($pending_space && lc_css_needs_space($output, $char)) {
                $output .= ' ';
            }

            $pending_space = false;
            $quote = $char;
            $output .= $char;
            continue;
        }

        if (ctype_space($char)) {
            $pending_space = $output !== '';
            continue;
        }

        if (lc_css_no_space_before($char)) {
            $output = rtrim($output);
            $pending_space = false;
        } elseif ($pending_space && lc_css_needs_space($output, $char)) {
            $output .= ' ';
        }

        $pending_space = false;
        $output .= $char;
    }

    return str_replace(';}', '}', trim($output));
}

function lc_strip_css_comments(string $css): string {
    $output = '';
    $length = strlen($css);
    $quote = null;

    for ($i = 0; $i < $length; $i++) {
        $char = $css[$i];

        if ($quote !== null) {
            $output .= $char;

            if ($char === '\\' && $i + 1 < $length) {
                $output .= $css[++$i];
                continue;
            }

            if ($char === $quote) {
                $quote = null;
            }

            continue;
        }

        if ($char === '"' || $char === "'") {
            $quote = $char;
            $output .= $char;
            continue;
        }

        if ($char === '/' && $i + 1 < $length && $css[$i + 1] === '*') {
            $i += 2;

            while ($i + 1 < $length && !($css[$i] === '*' && $css[$i + 1] === '/')) {
                $i++;
            }

            $i++;
            continue;
        }

        $output .= $char;
    }

    return $output;
}

function lc_css_no_space_before(string $char): bool {
    return strpos('{}:;,>~)](!', $char) !== false;
}

function lc_css_needs_space(string $output, string $char): bool {
    if ($output === '') {
        return false;
    }

    $previous = substr($output, -1);

    if (strpos('{}:;,>~([!', $previous) !== false) {
        return false;
    }

    return strpos('{}:;,>~)](!', $char) === false;
}
