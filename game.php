<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="X-UA-Compatible" content="IE=edge">
  <title>Memory Game</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div style="position: absolute; top: 10px; right: 10px; font-size: 16px;">
    Logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
    | <a href="logout.php" style="color: yellow; text-decoration: none;">Logout</a>
  </div>

  <div class="emoji-choice">
    <button onclick="startGame('fruits')">Fruits</button>
    <button onclick="startGame('faces')">Faces</button>
  </div>

  <div class="container">
    <h2>Memory Game</h2>
    <p id="categoryLabel" class="category-label">Fruit Category</p>
    <div class="game"></div>
    <div class="controls">
      <div class="attempts">Attempts: <span id="attemptCount">0</span></div>
      <button class="reset" onclick="resetGame()">Reset Game</button>
    </div>
  </div>

  <script>
    const fruits = ['🍎','🍎','🍌','🍌','🍒','🍒','🍇','🍇','🍉','🍉','🍊','🍊','🍋','🍋','🍍','🍍'];
    const faces = ['😀','😀','😎','😎','🤓','🤓','😭','😭','😡','😡','😍','😍','😱','😱','😴','😴'];
    let currentSet = 'fruits';
    let firstCard = null;
    let secondCard = null;
    let lockBoard = false;
    let attemptCount = 0;

    const gameBoard = document.querySelector('.game');
    const attemptDisplay = document.getElementById('attemptCount');

    function resetGame() {
      const items = document.querySelectorAll('.item');
      items.forEach(item => {
        item.classList.add('resetting');
      });

      setTimeout(() => {
        startGame(currentSet);
      }, 400);
    }

    function startGame(setName) {
      currentSet = setName;
      document.getElementById('categoryLabel').textContent =
        setName === 'fruits' ? 'Fruit Category' : 'Faces Category';

      const emojis = setName === 'fruits' ? [...fruits] : [...faces];
      const shuffled = emojis.sort(() => Math.random() - 0.5);

      gameBoard.innerHTML = '';
      attemptCount = 0;
      attemptDisplay.textContent = attemptCount;
      firstCard = null;
      secondCard = null;
      lockBoard = false;

      for (let i = 0; i < shuffled.length; i++) {
        let box = document.createElement('div');
        box.className = 'item';
        box.innerHTML = shuffled[i];

        box.addEventListener('click', function () {
          if (lockBoard || box.classList.contains('boxOpen') || box.classList.contains('boxMatch')) return;

          box.classList.add('boxOpen');

          if (!firstCard) {
            firstCard = box;
          } else {
            secondCard = box;
            lockBoard = true;

            setTimeout(() => {
              if (firstCard.innerHTML === secondCard.innerHTML) {
                firstCard.classList.add('boxMatch');
                secondCard.classList.add('boxMatch');
                firstCard.style.transform = 'rotateY(180deg)';
                secondCard.style.transform = 'rotateY(180deg)';
              } else {
                firstCard.classList.remove('boxOpen');
                secondCard.classList.remove('boxOpen');
                attemptCount++;
                attemptDisplay.textContent = attemptCount;
              }

              firstCard = null;
              secondCard = null;
              lockBoard = false;

              if (document.querySelectorAll('.boxMatch').length === emojis.length) {
                setTimeout(() => {
                  alert('You win!' + '\nAttempts: ' + attemptCount);
                  saveAttempt(attemptCount, currentSet); // Save attempt to DB
                  startGame(currentSet);
                }, 300);
              }
            }, 800);
          }
        });

        gameBoard.appendChild(box);
      }

      setTimeout(() => {
        document.querySelectorAll('.item').forEach(item => {
          item.classList.add('animate');
          item.addEventListener('animationend', () => {
            item.classList.remove('animate');
          }, { once: true });
        });
      }, 50);
    }

    function saveAttempt(attempts, category) {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "save_attempt.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send("attempts=" + attempts + "&category=" + category);
    }

    window.onload = () => startGame('fruits');
  </script>
</body>
</html>
