<?php
// editStatistics.php

// session_start();
if (!isset($_SESSION['user']) || ( isset($_SESSION['role']) && $_SESSION['role'] < 2) ) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../protected/adaptation.php';

// connect to database
$databaseConnect = new mysqli(DB_HOST, USER_NAME, USER_PASS, DB_NAME);
if ($databaseConnect->connect_error) {
    die("Connection failed: " . htmlspecialchars($databaseConnect->connect_error));
}

// Determine selected player
$selectedPlayerID = isset($_GET['playerID']) ? (int)$_GET['playerID'] : null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new stats
    if (isset($_POST['add_stats'], $_POST['playerID'], $_POST['gameID'])) {
        $pid = (int)$_POST['playerID'];
        $gid = (int)$_POST['gameID'];
        $pts = (int)$_POST['points'];
        $ast = (int)$_POST['assists'];
        $atk = (float)$_POST['attackSuccessRate'];
        $def = (float)$_POST['defendSuccessRate'];
        $set = (float)$_POST['settingRate'];
        $srv = (float)$_POST['serveRate'];

        $ins = $databaseConnect->prepare("
            INSERT INTO PlayerStats
              (playerID, gameID, points, assists, attackSuccessRate, defendSuccessRate, settingRate, serveRate)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->bind_param('iiiddddd', $pid, $gid, $pts, $ast, $atk, $def, $set, $srv);
        $ins->execute();
        $ins->close();

        header("Location: ?page=2&playerID={$pid}");
        exit;
    }

    // Delete existing stat
    if (isset($_POST['delete_stat'], $_POST['playerID'], $_POST['deleteGameID'])) {
        $pid = (int)$_POST['playerID'];
        $gid = (int)$_POST['deleteGameID'];

        $del = $databaseConnect->prepare("
            DELETE FROM PlayerStats
             WHERE playerID = ? AND gameID = ?
        ");
        $del->bind_param('ii', $pid, $gid);
        $del->execute();
        $del->close();

        header("Location: ?page=2&playerID={$pid}");
        exit;
    }
}

// Fetch players for dropdown
$sqlPlayers = "
    SELECT playerID, firstName, lastName
    FROM Player
    ORDER BY lastName, firstName
";
$res = $databaseConnect->query($sqlPlayers);
$allPlayers = $res->fetch_all(MYSQLI_ASSOC);

// Fetch games the player has NOT yet played (only if a player is selected)
$homeTeamID = 1;
$allGames = [];
if ($selectedPlayerID) {
    $sqlGames = "
        SELECT g.gameID, g.gameDate
          FROM GameStats g
         WHERE g.teamID = ?
           AND g.gameID NOT IN (
               SELECT gameID
                 FROM PlayerStats
                WHERE playerID = ?
           )
         ORDER BY g.gameDate ASC
    ";
    $stmt = $databaseConnect->prepare($sqlGames);
    $stmt->bind_param('ii', $homeTeamID, $selectedPlayerID);
    $stmt->execute();
    $allGames = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Fetch past stats (for delete form)
$pastStats = [];
if ($selectedPlayerID) {
    $sqlPast = "
        SELECT ps.gameID, g.gameDate
          FROM PlayerStats ps
          JOIN GameStats g ON ps.gameID = g.gameID
         WHERE ps.playerID = ?
         ORDER BY g.gameDate ASC
    ";
    $stmt = $databaseConnect->prepare($sqlPast);
    $stmt->bind_param('i', $selectedPlayerID);
    $stmt->execute();
    $pastStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$databaseConnect->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Statistics</title>
  <style>
    .content { font-family: sans-serif; max-width: 900px; margin: 20px auto; padding: 0 10px; }
    h1, h2 { text-align: center; }
    .content form { margin: 20px 0; display: flex; flex-direction: column; align-items: center; }
    label { margin-bottom: 8px; font-weight: bold; }
    select, input[type="number"], input[type="text"] {
      padding: 8px; width: 200px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px;
    }

    /* blue & orange button styles */
    .page-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-block: 20px;
    }
    .btn-add {
      border: none;
      border-radius: 50px;
      padding: 10px 20px;
      cursor: pointer;
      font-weight: bold;
      background: #1e90ff;  /* DodgerBlue */
    }
    .btn-add:hover {
      background: #1c86ee;
    }
    .btn-delete {
      border: none;
      border-radius: 50px;
      padding: 10px 20px;
      cursor: pointer;
      font-weight: bold;
      background: #ffa500;  /* Orange */
    }
    .btn-delete:hover {
      background: #ff8c00;
    }
  </style>
</head>
<body>
  <div class="content">
    <h1>Edit Statistics</h1>
    <!-- Player selection -->
    <form method="get">
      <label for="playerID">Select Player:</label>
      <input type="hidden" name="page", value=2>
      <select name="playerID" id="playerID" onchange="this.form.submit()">
        <option value="">-- choose player --</option>
        <?php foreach ($allPlayers as $p): ?>
          <option value="<?= $p['playerID'] ?>"
            <?= $selectedPlayerID === (int)$p['playerID'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($p['firstName'].' '.$p['lastName']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
    <?php if ($selectedPlayerID): ?>
      <?php
        // find player name
        $playerName = "";

        foreach ($allPlayers as $pp) {
          if ($pp['playerID'] == $selectedPlayerID) {
            $playerName = $pp['firstName'].' '.$pp['lastName'];
            break;
          }
        }
      ?>
      <h2>Add Stats for <?= htmlspecialchars($playerName) ?></h2>
      <?php if (empty($allGames)): ?>
        <p style="text-align:center;">No available games to add stats for.</p>
      <?php else: ?>
        <form method="post" action="">
          <input type="hidden" name="playerID" value="<?= $selectedPlayerID ?>">
          <label for="gameAdd">Game:</label>
          <select name="gameID" id="gameAdd" required>
            <option value="">-- choose game --</option>
            <?php foreach ($allGames as $g): ?>
              <option value="<?= $g['gameID'] ?>">
                <?= date('M d, Y', strtotime($g['gameDate'])) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <label>Points:</label>
          <input type="number" name="points" min="0" required>
          <label>Assists:</label>
          <input type="number" name="assists" min="0" required>
          <label>Attack Success Rate (0–1):</label>
          <input type="text" name="attackSuccessRate" pattern="0\.\d+" required>
          <label>Defend Success Rate (0–1):</label>
          <input type="text" name="defendSuccessRate" pattern="0\.\d+" required>
          <label>Setting Rate (0–1):</label>
          <input type="text" name="settingRate" pattern="0\.\d+" required>
          <label>Serve Rate (0–1):</label>
          <input type="text" name="serveRate" pattern="0\.\d+" required>
          <div class="page-buttons">
            <button type="submit" name="add_stats" class="btn-add">Add Stats</button>
          </div>
        </form>
      <?php endif; ?>
      <h2>Delete Past Stat for <?= htmlspecialchars($playerName) ?></h2>
      <?php if (empty($pastStats)): ?>
        <p style="text-align:center;">No existing stats to delete.</p>
      <?php else: ?>
        <form method="post">
          <input type="hidden" name="playerID" value="<?= $selectedPlayerID ?>">
          <label for="gameDel">Select Stat to Delete:</label>
          <select name="deleteGameID" id="gameDel" required>
            <option value="">-- choose game --</option>
            <?php foreach ($pastStats as $ps): ?>
              <option value="<?= $ps['gameID'] ?>">
                <?= date('M d, Y', strtotime($ps['gameDate'])) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="page-buttons">
            <button type="submit" name="delete_stat" class="btn-delete">Delete Stat</button>
          </div>
        </form>
      <?php endif; ?>
    <?php endif; ?>
    <div class="page-buttons">
      <form action="index.php" method="get">
        <button type="submit">← Back to Dashboard</button>
      </form>
    </div>
  </div>
</body>
</html>
