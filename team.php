<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$saarasFile = 'saaras.xlsx';

$teams = [];

if (file_exists($saarasFile)) {
    $spreadsheet = IOFactory::load($saarasFile);
    $teamMembersSheet = $spreadsheet->getSheetByName('team_members');
    $highestRow = $teamMembersSheet->getHighestRow();
    $highestColumn = $teamMembersSheet->getHighestColumn();

    for ($column = 'A'; $column <= $highestColumn; ++$column) {
        $teamName = $teamMembersSheet->getCell($column . '1')->getValue();

        if ($teamName !== null) {
            $teamMembers = [];
            $teamRoles = [];

            $captain = $teamMembersSheet->getCell($column . '2')->getValue();
            if ($captain !== null) {
                $teamMembers[] = $captain;
                $teamRoles[] = 'Captain';
            }

            $mentor = $teamMembersSheet->getCell($column . '3')->getValue();
            if ($mentor !== null) {
                $teamMembers[] = $mentor;
                $teamRoles[] = 'Mentor';
            }

            for ($row = 4; $row <= 5; ++$row) {
                $viceCaptain = $teamMembersSheet->getCell($column . $row)->getValue();
                if ($viceCaptain !== null) {
                    $teamMembers[] = $viceCaptain;
                    $teamRoles[] = 'Vice Captain';
                }
            }

            for ($row = 6; $row <= $highestRow; ++$row) {
                $memberName = $teamMembersSheet->getCell($column . $row)->getValue();
                if ($memberName !== null) {
                    $teamMembers[] = $memberName;
                    $teamRoles[] = 'Member';
                }
            }

            $teams[$teamName]['team_member'] = $teamMembers;
            $teams[$teamName]['roles'] = $teamRoles;
        }
    }
}

$teamName = urldecode($_GET['team'] ?? '');

if (isset($teams[$teamName]['team_member'])) {
    $teamMembers = $teams[$teamName]['team_member'];
    $teamRoles = $teams[$teamName]['roles'];
} else {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <title><?= $teamName ?></title>
</head>

<body>
    <div class="container mt-5">
        <h2><?= $teamName ?></h2>
        <button class="back-btn btn btn-primary"><a href="index.php" style="color: #fff; text-decoration: none;">Back</a></button>

        <!-- Team Members -->
        <ul class="list-group">
            <?php for ($i = 0; $i < count($teamMembers); $i++): ?>
                <li class="list-group-item">
                    <?= $teamRoles[$i] ?>: <?= $teamMembers[$i] ?>
                </li>
            <?php endfor; ?>
        </ul>
    </div>
</body>

</html>
