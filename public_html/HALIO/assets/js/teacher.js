async function fetchData() {
    const response = await fetch(config_fetchUri);
    const data = await response.json();
    const me = data["<?= $id; ?>"];
    document.getElementById("update").innerHTML = `最終更新: ${data.update}`;

    const timeInfo = me.status == 1 ? `復帰時刻: ${me.back}` : `退出: ${me.leave}`;
    document.getElementById("time").innerHTML = timeInfo;

    const statusEl = document.getElementById('status');
    if (statusEl) {
        statusEl.className = `status status-${me.status}`;
        statusEl.title = `${me.status == 1 ? '教務室在室中' : '現在の状態: ' + ['退出', '教務室', '学内', '校外'][me.status]}`;
    }
}
setInterval(fetchData, 20000);
fetchData();