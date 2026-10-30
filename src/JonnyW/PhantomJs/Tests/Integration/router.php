<?php
// Local-only HTTP fixtures for php-phantomjs integration tests.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
header('Cache-Control: no-store');
switch ($path) {
    case '/tests/test-default.php':
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!doctype html><html><head><title>Fixture</title></head><body>';
        echo '<div>PHANTOMJS_DEFAULT_TEST</div>';
        echo '<p>userAgent=' . htmlspecialchars(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '', ENT_QUOTES, 'UTF-8') . '</p>';
        foreach ($_COOKIE as $name => $value) {
            echo '<p>cookie_' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '=' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</p>';
        }
        echo '</body></html>';
        break;
    case '/tests/test-console-error.php':
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!doctype html><html><body><script>invalid.nonexistent();</script></body></html>';
        break;
    case '/tests/test-post.php':
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!doctype html><html><body><ul>';
        foreach ($_POST as $key => $value) {
            // Keep fixture shape used by existing test assertions.
            echo '<li>' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '=' . $value . '</li>';
        }
        echo '</ul></body></html>';
        break;
    case '/tests/test-capture.php':
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!doctype html><html><body><h1>Capture fixture</h1></body></html>';
        break;
    case '/tests/test-redirect.php':
        header(
            'Location: http://127.0.0.1:' . $_SERVER['SERVER_PORT']
            . '/tests/test-default.php',
            true,
            302
        );
        break;
    case '/tests/test-lazy.php':
        header('Content-Type: text/html; charset=UTF-8');
        echo <<<'HTML'
<!DOCTYPE html>
<html>
<body>
    <p id="content"></p>

    <script>
        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById('content').innerHTML =
                    xhr.responseText;
            }
        };

        xhr.open('GET', '/tests/lazy-content.php', true);
        xhr.send(null);
    </script>
</body>
</html>
HTML;
        break;
    case '/tests/lazy-content.php':
        usleep(1500000);

        header('Content-Type: text/plain; charset=utf-8');
        echo 'loaded';
        break;
    default:
        http_response_code(404);
        echo 'Unknown test fixture';
}
