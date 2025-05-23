<?php
// viewGameData.php

require_once __DIR__ . '/../protected/adaptation.php';

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

// 2) fetch players
$sqlPlayers = "
    SELECT DISTINCT a.userID, a.firstName, a.lastName
    FROM Account a, PlayerStats ps
    WHERE a.teamID = ? AND ps.playerID = a.userID
    ORDER BY a.lastName, a.firstName
";
$stmt = $databaseConnect->prepare($sqlPlayers);
$stmt->bind_param('i', $homeTeamID);
$stmt->execute();
$players = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 3) if a player is selected, fetch that player’s stats
$selectedPlayerID = isset($_GET['playerID']) ? $_GET['playerID'] : null;
$playerStats = [];
$playerInfo  = null;

if ($selectedPlayerID) {
    foreach ($players as $p) {
        if ($p['userID'] === $selectedPlayerID) {
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
          ps.serveRate,
          p.positionName
        FROM PlayerStats ps
        JOIN Positions p ON ps.positionID = p.positionID
        JOIN GameStats g ON ps.gameID = g.gameID
        WHERE ps.playerID = ?
        ORDER BY g.gameDate ASC
    ";
    $stmt = $databaseConnect->prepare($sqlStats);
    $stmt->bind_param('s', $selectedPlayerID);
    $stmt->execute();
    $playerStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}


// 4) Fetch all set stats
$query = '
SELECT *
FROM SetStats
ORDER BY gameID ASC
';

$stmt = $databaseConnect->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$sets = $result->fetch_all();

$setObj = (object)[];
foreach($sets as $set) {
   $setData = (object)[
      'setNumber' => $set[1],
      'setTimeMins' => $set[2],
      'setTimeSecs' => $set[3],
      'homeScore' => $set[4],
      'oppScore' => $set[5],
    ];

  $gameID = $set[0];
  if (!isset($setObj->$gameID)) {
    $setObj->$gameID = array($setData);
  } else {
    array_push($setObj->$gameID, $setData);
  }
}

$stmt->free_result();
$result->free_result();
$databaseConnect->close();
?>

<script>
  function openGame(e) {
    const gameId = e.target.value;
    const gameStatsBox = e.target.querySelector(".game-stats");
    gameStatsBox.style.display = gameStatsBox.style.display == 'block' ? 'none' : 'block';
  }
</script>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Game & Player Stats</title>
</head>
<body>
  <div class="content">
    <h1>Home Team Game Results</h1>
    <?php if (empty($games)): ?>
      <p>No games found.</p>
    <?php else: ?>
      <ul class="game-list">
        <?php foreach ($games as $g): 
            $id = $g['gameID'];
            $gameSets = $setObj->$id;

            $homeScore = 0;
            $oppScore = 0;

            foreach ($gameSets as $set) {
              $homeScore += $set->homeScore;
              $oppScore += $set->oppScore;
            }

            $gameResult = ($homeScore == $oppScore 
              ? 'tie'
              : $homeScore > $oppScore)
                ? 'win'
                : 'loss';
          ?>
          <li class="<?= $gameResult ?> game" onclick="openGame(event)" value=<?=$g['gameID']?>>
            <?= date('M d, Y', strtotime($g['gameDate'])) ?>
            vs <?= htmlspecialchars($g['teamName']) ?> (<?= htmlspecialchars($g['schoolName']) ?>)
            — <?= ucfirst(htmlspecialchars($gameResult)) ?>

            <div class="game-stats" style="display:none;">
              <?php 
                $id = $g['gameID'];
                $sets = $setObj->$id;
                
                foreach($sets as $set) {
                  ?>
                  <table>
                    <thead>
                      <th colspan=2>Set <?=$set->setNumber?></th>
                    </th>
                    <thead>
                    <tbody>
                      <tr>
                        <th>Time</th>
                        <td>
                          <?=$set->setTimeMins.':'.$set->setTimeSecs?>
                        </td>
                      </tr>
                      <tr>
                        <th>Home score</th>
                        <td>
                          <?=$set->homeScore?>
                        </td>
                      </tr>
                      <tr>
                        <th>Opponent Score</th>
                        <td>
                          <?=$set->oppScore?>
                        </td>
                      </tr>
                    </tbody>

                    </tr>
                  </table>
                  <?php 
                }
              ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <h1>Player Stats</h1>
    <form method="get">
      <label for="playerID">Select a player:</label>
      <input type="hidden" name="page" value=0>
      <select name="playerID" id="playerID" onchange="this.form.submit()">
        <option value="">-- choose --</option>
        <?php foreach ($players as $p): ?>
          <option
            value="<?= $p['userID'] ?>"
            <?= $selectedPlayerID===$p['userID'] ? 'selected': '' ?>
          >
            <?= htmlspecialchars($p['firstName'].' '.$p['lastName']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
    <?php if ($playerInfo): ?>
      <h2>
        Stats for <?= htmlspecialchars($playerInfo['firstName'].' '.$playerInfo['lastName']) ?>
      </h2>
      <?php if (empty($playerStats)): ?>
        <p>No stats recorded for this player.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Date</th><th>Points</th><th>Assists</th>
              <th>Attack%</th><th>Defend%</th><th>Set%</th><th>Serve%</th>
              <th>Position</th>
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
                <td><?= htmlspecialchars($row['positionName']) ?></td>
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
  </div>
</body>
</html>

<style>
  .content {
    text-align: center;
  }
  .game-list { list-style: none; padding: 0; max-width: 600px; margin: 20px auto; }
  .game-list li { margin: 8px 0; padding: 8px 12px; border-radius: 4px; font-weight: bold; opacity: 0.7; user-select: none; }
  .game-list li.win  { background: rgba(30,144,255,0.1); color: #1e90ff; }
  .game-list li.loss { background: rgba(255,165,0,0.1);   color: #ffa500; }
  .game-list .game:hover {
    opacity: 1;
    cursor: pointer;
  }
  .game-list .game-stats {
    pointer-events: none;
  }

  table { border-collapse: collapse; width: 100%; max-width: 800px; margin: 20px auto; }
  th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
  th { background: #f4f4f4; }

  
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
