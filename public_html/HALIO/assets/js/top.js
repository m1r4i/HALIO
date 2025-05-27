const courseMap = config_courseMap;

function updateUI(data) {
    const filterValues = Array.from(document.querySelectorAll('#checkboxFilter input[type="checkbox"]:checked')).map(cb => parseInt(cb.value));
    const container = document.getElementById("teacherContainer");
    container.innerHTML = "";

    const sortOption = document.getElementById("sortOption").value;

    const statusSortOrder = [1,
        2,
        3,
        0]; // 在室 → 校内 → 校外 → 退出, Todo: config.phpと統合したい

    // フィルタリング
    const teacherArray = Object.values(data).filter(teacher => teacher.name).filter(teacher => filterValues.includes(0) || filterValues.some(f => (teacher.course & f) !== 0)).sort((a, b) => {
        if (sortOption === "status") {
            return statusSortOrder.indexOf(a.status) - statusSortOrder.indexOf(b.status);
        }
        // ステータスごと: 壊れた... 直そう

        else {
            // コースごと
            const minA = Math.min(...Object.keys(courseMap).filter(c => (a.course & c) !== 0).map(Number));
            const minB = Math.min(...Object.keys(courseMap).filter(c => (b.course & c) !== 0).map(Number));
            return minA - minB;
        }
    }

    );


    teacherArray.forEach(teacher => {
        const subjects = Object.keys(courseMap).filter(c => (teacher.course & c) !== 0).map(c => courseMap[c]);

        const statusText = ["退出", "教務室", "学内", "校外"][teacher.status] || "不明";

        const timeInfo = teacher.status == 1 ? `復帰: ${teacher.back}` : (teacher.status > 1 ? `退出: ${teacher.leave}` : "");

        const card = document.createElement("div");
        card.className = "card";
        card.innerHTML = `
                        <div class="name">${teacher.name} (${teacher.type})</div>
                        <div class="status status-${teacher.status}">${statusText}</div>
                        <div>${timeInfo}</div>
                        <div class="tags">${subjects.map(sub => `<span class="tag">${sub}</span>`).join(" ") || "担当データなし"}</div>
                    `; // 教員のデータを表示
        card.dataset.eid = teacher.id;

        card.addEventListener('click', function () {
            const eid = this.getAttribute('data-eid');
            if (eid) {
                location.href = `/HALIO/teacher?id=${eid}`; // PWA時は同一タブで開く
            }
        });
        container.appendChild(card);
    });
}

async function fetchData() {
    const response = await fetch(config_fetchUri);
    const data = await response.json();
    updateUI(data);
    const update = document.getElementById("lastUpdate");
    update.innerHTML = `最終更新: ${data.update}`;
}
// データの取得

document.querySelectorAll('#checkboxFilter input[type="checkbox"]').forEach(cb => {
    cb.addEventListener("change", () => {
        // 「すべて」が選ばれていたら他のチェックを外す
        if (parseInt(cb.value) === 0 && cb.checked) {
            document.querySelectorAll('#checkboxFilter input[type="checkbox"]').forEach(other => {
                if (other.value !== "0") other.checked = false;
            });
        } else if (cb.checked && document.querySelector('#checkboxFilter input[value="0"]').checked) {
            // 他のチェックが選ばれたら「すべて」を外す
            document.querySelector('#checkboxFilter input[value="0"]').checked = false;
        }
        fetchData();
    });
});
// 表示する学科の選択

document.getElementById("sortOption").addEventListener("change", fetchData);

setInterval(fetchData, 20000);
fetchData();