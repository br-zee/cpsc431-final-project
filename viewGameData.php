<?php
// viewGameData.php

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/protected/adaptation.php';

$databaseConnect = new mysqli(DB_HOST, USER_NAME, USER_PASS, DB_NAME);
if ($databaseConnect->connect_error) {
    die("Connection failed: " . htmlspecialchars($databaseConnect->connect_error));
}

$homeTeamID = 1;

// 1) fetch all games
$sqlGames = "
    SELECT g.gameID, g.gameDate, o.teamName, o.schoolName, g.result
    FROM GameStats g
    JOIN OpponentTeam o ON g.opponentID = o.opponentID
    WHERE g.teamID = ?
    ORDER BY g.gameDate ASC
";
$stmt = $databaseConnect->prepare($sqlGames);
$stmt->bind_param('i', $homeTeamID);
$stmt->execute();
$games = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 2) fetch players + positions
$sqlPlayers = "
    SELECT p.playerID, p.firstName, p.lastName, pos.positionName
    FROM Player p
    JOIN Positions pos ON p.positionID = pos.positionID
    WHERE p.teamID = ?
    ORDER BY p.lastName, p.firstName
";
$stmt = $databaseConnect->prepare($sqlPlayers);
$stmt->bind_param('i', $homeTeamID);
$stmt->execute();
$players = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 3) if a player is selected, fetch that player’s stats
$selectedPlayerID = isset($_GET['playerID']) ? (int)$_GET['playerID'] : null;
$playerStats = [];
$playerInfo  = null;

if ($selectedPlayerID) {
    foreach ($players as $p) {
        if ($p['playerID'] === $selectedPlayerID) {
            $playerInfo = $p;
            break;
        }
    }

    $sqlStats = "
        SELECT 
          g.gameDate,
          ps.points,
          ps.assists,
          ps.attackSuccessRate,
          ps.defendSuccessRate,
          ps.settingRate,
          ps.serveRate
        FROM PlayerStats ps
        JOIN GameStats g ON ps.gameID = g.gameID
        WHERE ps.playerID = ?
        ORDER BY g.gameDate ASC
    ";
    $stmt = $databaseConnect->prepare($sqlStats);
    $stmt->bind_param('i', $selectedPlayerID);
    $stmt->execute();
    $playerStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$databaseConnect->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Game & Player Stats</title>
  <style>
    /* game list styling */
    .game-list { list-style: none; padding: 0; max-width: 600px; margin: 20px auto; }
    .game-list li { margin: 8px 0; padding: 8px 12px; border-radius: 4px; font-weight: bold; }
    .game-list li.win  { background: rgba(30,144,255,0.1); color: #1e90ff; }
    .game-list li.loss { background: rgba(255,165,0,0.1);   color: #ffa500; }

    /* table styling */
    table { border-collapse: collapse; width: 100%; max-width: 800px; margin: 20px auto; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background: #f4f4f4; }

    /* your existing page-buttons style */
    .page-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-block: 20px;
    }
    .page-buttons button {
        border: none;
        background: white;
        border-radius: 50px;
        padding: 10px 20px;
        cursor: pointer;
    }
    .page-buttons button:hover {
        background: lightgray;
    }
  </style>
</head>
<body>
  <h1>Home Team Game Results</h1>
  <?php if (empty($games)): ?>
    <p>No games found.</p>
  <?php else: ?>
    <ul class="game-list">
      <?php foreach ($games as $g): ?>
        <li class="<?= $g['result']==='win' ? 'win' : 'loss' ?>">
          <?= date('M d, Y', strtotime($g['gameDate'])) ?>
          vs <?= htmlspecialchars($g['teamName']) ?> (<?= htmlspecialchars($g['schoolName']) ?>)
          — <?= ucfirst(htmlspecialchars($g['result'])) ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <h1>Player Stats</h1>
  <form method="get">
    <label for="playerID">Select a player:</label>
    <select name="playerID" id="playerID" onchange="this.form.submit()">
      <option value="">-- choose --</option>
      <?php foreach ($players as $p): ?>
        <option 
          value="<?= $p['playerID'] ?>"
          <?= $selectedPlayerID===$p['playerID'] ? 'selected':'' ?>
        >
          <?= htmlspecialchars($p['firstName'].' '.$p['lastName']) ?>
          (<?= htmlspecialchars($p['positionName']) ?>)
        </option>
      <?php endforeach; ?>
    </select>
  </form>

  <?php if ($playerInfo): ?>
    <h2>
      Stats for <?= htmlspecialchars($playerInfo['firstName'].' '.$playerInfo['lastName']) ?>
      — <?= htmlspecialchars($playerInfo['positionName']) ?>
    </h2>
    <?php if (empty($playerStats)): ?>
      <p>No stats recorded for this player.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Date</th><th>Points</th><th>Assists</th>
            <th>Attack%</th><th>Defend%</th><th>Set%</th><th>Serve%</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($playerStats as $row): ?>
            <tr>
              <td><?= date('M d, Y', strtotime($row['gameDate'])) ?></td>
              <td><?= htmlspecialchars($row['points']) ?></td>
              <td><?= htmlspecialchars($row['assists']) ?></td>
              <td><?= htmlspecialchars($row['attackSuccessRate']) ?></td>
              <td><?= htmlspecialchars($row['defendSuccessRate']) ?></td>
              <td><?= htmlspecialchars($row['settingRate']) ?></td>
              <td><?= htmlspecialchars($row['serveRate']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  <?php endif; ?>

  <div class="page-buttons">
    <form action="index.php" method="get">
      <button type="submit">← Back to Dashboard</button>
    </form>
  </div>
</body>
</html>
