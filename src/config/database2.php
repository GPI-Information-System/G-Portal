<?php
/* Control and Monitoring System database configuration. */

define('DB2_HOST', '192.168.5.106:3306');
define('DB2_USER', 'root');
define('DB2_PASS', 'root');
define('DB2_NAME', 'nginx-proxy-manager');
define('DB2_CHARSET', 'utf8mb4');
define('DB2_TIMEOUT', 5);

function isControlDatabaseConfigured()
{
    return DB2_HOST !== '' && DB2_USER !== '' && DB2_NAME !== '';
}

function getControlDBConnection()
{
    static $conn = null;

    if (!isControlDatabaseConfigured()) {
        return null;
    }

    if ($conn !== null && $conn->ping()) {
        return $conn;
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = new mysqli(DB2_HOST, DB2_USER, DB2_PASS, DB2_NAME);

    if ($conn->connect_error) {
        error_log('Control and Monitoring System database connection error: ' . $conn->connect_error);
        $conn = null;
        return null;
    }

    $conn->set_charset(DB2_CHARSET);
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, DB2_TIMEOUT);

    return $conn;
}

/**
 * Return NPM forward endpoints indexed by the matching proxy domain.
 * The empty result lets callers retain the primary-table fallback value.
 */
function getControlMonitoringDomainsBySystemDomains(array $systemDomains)
{
    $systemDomains = array_values(array_unique(array_filter(array_map(function ($domain) {
        return normalizeProxyDomain($domain);
    }, $systemDomains))));
    if (empty($systemDomains)) {
        return [];
    }

    $conn = getControlDBConnection();
    if ($conn === null) {
        return [];
    }

    $stmt = $conn->prepare('SELECT domain_names, forward_host, forward_port FROM proxy_host');

    if (!$stmt) {
        error_log('Control and Monitoring System query prepare error: ' . $conn->error);
        return [];
    }

    if (!$stmt->execute()) {
        error_log('Control and Monitoring System query error: ' . $stmt->error);
        $stmt->close();
        return [];
    }

    $result = $stmt->get_result();
    $domains = [];
    while ($row = $result->fetch_assoc()) {
        $proxyDomains = json_decode($row['domain_names'] ?? '[]', true);
        if (!is_array($proxyDomains)) {
            continue;
        }

        $forwardHost = trim($row['forward_host'] ?? '');
        $forwardPort = trim((string) ($row['forward_port'] ?? ''));
        if ($forwardHost === '' || $forwardPort === '') {
            continue;
        }

        foreach ($proxyDomains as $proxyDomain) {
            $proxyDomain = normalizeProxyDomain($proxyDomain);
            if (in_array($proxyDomain, $systemDomains, true)) {
                $domains[$proxyDomain] = $forwardHost . ':' . $forwardPort;
            }
        }
    }

    $stmt->close();
    return $domains;
}

function normalizeProxyDomain($domain)
{
    $domain = strtolower(trim((string) $domain));
    if ($domain === '') {
        return '';
    }

    $domain = preg_replace('#^https?://#', '', $domain);
    $domain = explode('/', $domain, 2)[0];
    return rtrim($domain, '.');
}
