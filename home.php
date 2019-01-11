<?php

require $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
loggedin();

?>

<!DOCTYPE html>
<html>

<head>
    <!-- Config -->
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="manifest" href="/manifest.json"></link>
    <title>Home || Logs</title>

    <!-- SEO -->
    <link href="https://logs.lucacastelnuovo.nl" rel="canonical">
    <meta content="A system to store and access all my logs" name="description">

    <!-- Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

    <!-- Styles -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link rel="stylesheet" href="https://cdn.lucacastelnuovo.nl/general/css/materialize.css" />
    <style>
        .input-field input:focus + label {
            color: #2962FF !important;
        }

        .input-field input:focus {
            border-bottom: 1px solid #2962FF !important;
            box-shadow: 0 1px 0 0 #2962FF !important;
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-wrapper blue accent-4">
          <a href="/" class="brand-logo">Documentation</a>
          <a href="#" data-target="mobile" class="right sidenav-trigger"><i class="material-icons">menu</i></a>
          <ul id="nav-mobile" class="right hide-on-med-and-down">
            <?php

            $services = sql_select('services', 'id,name', 'true ORDER BY name DESC', false);
            while ($service = $services->fetch_assoc()) {
                echo "<li><a href='/home?service_id={$service['id']}'>{$service['name']}</a></li>";
            }

            ?>
            <li><a href="/?logout"><i class="material-icons tooltipped" data-position="bottom" data-tooltip="Logout">exit_to_app</i></a></li>
          </ul>
        </div>

        <ul class="sidenav" id="sidenav">
            <li><a href="/home">Home</a></li>
            <li class="divider"></li>
            <?php

            $services = sql_select('services', 'id,name', 'true ORDER BY name DESC', false);
            while ($service = $services->fetch_assoc()) {
                echo "<li><a href='/home?service_id={$service['id']}'>{$service['name']}</a></li>";
            }

            ?>
            <li class="divider"></li>
            <li><a href="/?logout">Logout</a></li>
        </ul>
    </nav>
    <main class="section">
        <div class="container">
            <?php if (isset($_GET['service_id'])) {
                ?>
            <div class="row">
                <div class="col s12 input-field">
                    <input type="search" id="searchBar" class="light-table-filter" data-table="order-table" placeholder="Search the logs...">
                </div>
            </div>
            <?php
            } ?>
            <div class="row">
                <?php
                    if (!isset($_GET['service_id'])) {
                        echo '<h4>Please select a service.</h4>';
                    } else {
                        $service_id = check_data($_GET['service_id'], true, 'Service ID', true, true, '/home');
                        $logs = sql_select('logs', 'date,ip,action,user_id,client_id', "service_id='{$service_id}' ORDER BY date DESC", false);

                        if ($logs->num_rows == 0) {
                            redirect('/home', 'Logs empty');
                        }

                        echo <<<HTML
                        <table class="responsive-table order-table">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Date</th>
                                    <th>User_ID</th>
                                    <th>Client_ID</th>
                                    <th>IP</th>
                                </tr>
                            </thead>

                            <tbody>
HTML;

                        while ($log_item = $logs->fetch_assoc()) {
                            echo <<<HTML
                            <tr>
                                <td>{$log_item['action']}</td>
                                <td>{$log_item['date']}</td>
                                <td>{$log_item['user_id']}</td>
                                <td>{$log_item['client_id']}</td>
                                <td>{$log_item['ip']}</td>
                            </tr>
HTML;
                        }
                        echo <<<HTML
                            </tbody>
                        </table>
HTML;
                    }
                ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.lucacastelnuovo.nl/general/js/materialize.js"></script>
    <script src="/js/init.js"></script>
    <script src="/js/filter.js"></script>
</body>
</html>
