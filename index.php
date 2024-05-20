<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sehen Sie den Status von faysi.de">
    <title>FAYSi Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .status-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .status {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .status h2 {
            margin: 0;
            font-size: 1.5em;
            color: #333;
        }
        .status p {
            margin: 10px 0;
        }
        .status .online {
            color: green;
        }
        .status .offline {
            color: red;
        }
        .status .details {
            color: #666;
        }
        .status-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: green;
            border-radius: 0 0 8px 8px;
        }
        .status-bar.offline {
            background: red;
        }
        .history {
            margin-top: 20px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Website Status</h1>
    <div class="status-container">
        <?php
        $urls = [
            "https://social.faysi.de",
            "https://faysi.de",
            "https://artist.faysi.de"
        ];

        function checkUrlStatus($url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $start = microtime(true);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $totalTime = microtime(true) - $start;
            curl_close($ch);

            $isOnline = ($httpCode >= 200 && $httpCode < 400);
            return [
                'status' => $isOnline,
                'response_time' => round($totalTime * 1000),
                'http_code' => $httpCode
            ];
        }

        foreach ($urls as $url) {
            $result = checkUrlStatus($url);
            $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $statusClass = $result['status'] ? 'online' : 'offline';
            $statusText = $result['status'] ? 'Online' : 'Offline (HTTP Code: ' . $result['http_code'] . ')';

            echo '<div class="status">';
            echo '<h2>' . $safeUrl . '</h2>';
            echo '<p class="' . $statusClass . '">' . $statusText . '</p>';
            echo '<p class="details">Antwortzeit: ' . $result['response_time'] . ' ms</p>';
            echo '<div class="status-bar ' . $statusClass . '"></div>';
            echo '<div class="history" id="history-' . md5($safeUrl) . '">Verlauf: <span class="status-history"></span></div>';
            echo '</div>';
        }
        ?>
    </div>
    <script>
        (function() {
            var urls = <?php echo json_encode($urls); ?>;
            urls.forEach(function(url) {
                var historyId = 'history-' + md5(url);
                var historyElement = document.getElementById(historyId).querySelector('.status-history');
                var statusHistory = JSON.parse(localStorage.getItem(historyId)) || [];

                function updateHistory(status) {
                    var timestamp = new Date().toLocaleTimeString();
                    statusHistory.push({ status: status, time: timestamp });
                    if (statusHistory.length > 10) statusHistory.shift(); // Behalte die letzten 10 Statusprüfungen
                    localStorage.setItem(historyId, JSON.stringify(statusHistory));
                }

                function renderHistory() {
                    historyElement.innerHTML = statusHistory.map(function(entry) {
                        return '<span class="' + entry.status + '">' + entry.status + ' um ' + entry.time + '</span>';
                    }).join(', ');
                }

                var statusElement = document.querySelector('.status-bar.' + (statusHistory[statusHistory.length - 1]?.status || 'offline'));
                if (statusElement) {
                    var currentStatus = statusElement.classList.contains('online') ? 'online' : 'offline';
                    updateHistory(currentStatus);
                    renderHistory();
                }
            });
        })();

        function md5(string) {
            // Basic MD5 hash function implementation
            function md5cycle(x, k) {
                // (omitted for brevity, use a library for real applications)
            }
            function md51(s) {
                // (omitted for brevity, use a library for real applications)
            }
            return md51(string);
        }
    </script>
</body>
</html>
