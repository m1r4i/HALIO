<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="manifest" href="/assets/manifest.json">
  <link rel="stylesheet" href="/HALIO/assets/css/root.css">
  <link rel="stylesheet" href="/HALIO/assets/css/top.css">
  <title>教員在籍状況</title>
  <script>
  if (navigator.serviceWorker) {
            navigator.serviceWorker.register ('/HALIO/assets/js/sw.js')
          }
  </script>
</head>

<body>
  <h2>HAL大阪教員在籍状況
    <br>
    <span id="lastUpdate" style="font-size: 14px;">
    </span>
  </h2>
  <label>科目で絞り込み（複数選択可）</label>
  <div id="checkboxFilter" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
    <label>
      <input type="checkbox" value="0" checked>すべて</label>
    <label>
      <input type="checkbox" value="1">ゲーム企画</label>
    <label>
      <input type="checkbox" value="2">ゲーム制作</label>
    <label>
      <input type="checkbox" value="4">ゲームデザイン</label>
    <label>
      <input type="checkbox" value="8">CG映像</label>
    <label>
      <input type="checkbox" value="16">CGグラフィック</label>
    <label>
      <input type="checkbox" value="32">CGアニメーション</label>
    <label>
      <input type="checkbox" value="64">ミュージック</label>
    <label>
      <input type="checkbox" value="128">カーデザイン</label>
    <label>
      <input type="checkbox" value="256">IT高度</label>
    <label>
      <input type="checkbox" value="512">IT-WEB</label>
    <label>
      <input type="checkbox" value="1024">IT-AI</label>
    <label>
      <input type="checkbox" value="2048">管理者</label>
    <label>
      <input type="checkbox" value="4096">就職担当</label>
    </div> <!-- Todo: ここもconfig.phpと統合しよう -->
    <div>
    <label>並び順を選択：</label>
    <select id="sortOption">
      <option value="course">学科別</option>
      <option value="status">在室状況順</option>
    </select>
    </div>
  <p>
    <small>※退出時刻, 復帰時刻は最大で5分程度の誤差がある場合があります。
      <br>
      <a href="/HALIO/updates.html">更新ログ・ご意見</a>
    </small>
  </p>
  <div class="container" id="teacherContainer">
  </div>
  <script src="/HALIO/assets/js/config.js"></script>
  <script src="/HALIO/assets/js/top.js"></script>
</body>

</html>