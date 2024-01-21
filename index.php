<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$saarasFile = 'saaras.xlsx';
$teams = [];

if (file_exists($saarasFile)) {
    $spreadsheet = IOFactory::load($saarasFile);
    $teamScoreSheet = $spreadsheet->getSheetByName('team_score');
    $highestRow = $teamScoreSheet->getHighestRow();

    for ($row = 1; $row <= $highestRow; ++$row) {
        $rowData = $teamScoreSheet->rangeToArray('A' . $row . ':B' . $row, null, true, false)[0];
        $teamName = $rowData[0] ?? null;
        $teamScore = $rowData[1] ?? 0;

        if ($teamName !== null) {
            $teams[$teamName]['score'] = $teamScore;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        var teamsData = <?php echo json_encode($teams); ?>;

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Team');
            data.addColumn('number', 'Score');

            <?php foreach ($teams as $teamName => $teamData): ?>
                data.addRow(['<?= $teamName ?>', <?= $teamData['score'] ?>]);
            <?php endforeach; ?>

            var options = {
                title: 'Team Scores',
                legend: { position: 'none' },
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));

            google.visualization.events.addListener(chart, 'select', function () {
                var selectedItem = chart.getSelection()[0];
                if (selectedItem) {
                    var teamIndex = selectedItem.row;
                    var teamNames = Object.keys(teamsData);
                    if (teamIndex < teamNames.length) {
                        var teamName = teamNames[teamIndex];
                        window.location.href = 'team.php?team=' + encodeURIComponent(teamName);
                    }
                }
            });

            chart.draw(data, options);
        }
    </script>
    <title>SAARAs Homepage</title>
</head>

<body>
    <div class="container mt-5">
        <h1 class="display-3">
            <span style="font-weight: bold;">S</span>IES
            <span style="font-weight: bold;">A</span>cademy for
            <span style="font-weight: bold;">A</span>warding
            <span style="font-weight: bold;">R</span>otaract
            <span style="font-weight: bold;">A</span>chievement<span style="font-weight: bold;">s</span>
        </h1>
        <div class="row">
            <div class="col-md-6">
                <h2>Leaderboard</h2>
                <ul class="list-group">
                    <?php
                    $leaderboard = [];
                    foreach ($teams as $teamName => $teamData) {
                        $leaderboard[$teamName] = $teamData['score'];
                    }
                    arsort($leaderboard);
                    foreach ($leaderboard as $teamName => $score): ?>
                        <li class="list-group-item">
                            <?= $teamName ?> -
                            <?= $score ?>
                            <a href='team.php?team=<?= urlencode($teamName) ?>' class='btn btn-primary btn-sm float-end'>View
                                Team</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-md-6">
                <h2>Column Chart</h2>
                <div id="chart_div" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</body>

</html>