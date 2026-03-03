<?php
// developer_suggestions.php
session_start();

// CHANGE THIS:
$ADMIN_PASS = "CHANGE_ME_STRONG_PASSWORD";

if (isset($_POST["pass"])) {
  if ($_POST["pass"] === $ADMIN_PASS) {
    $_SESSION["purpz_admin"] = true;
  } else {
    $err = "Wrong password.";
  }
}

if (!($_SESSION["purpz_admin"] ?? false)) {
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Developer • Suggestions</title>
<style>
body{font-family:system-ui;background:#0b0620;color:#fff;margin:0;padding:18px}
.box{max-width:520px;margin:0 auto}
.card{border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.05);border-radius:16px;padding:14px}
input,button{padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);color:#fff;outline:none}
button{cursor:pointer;font-weight:900}
</style></head>
<body>
<div class="box">
  <h1>🔒 Developer Board</h1>
  <div class="card">
    <form method="POST">
      <div style="margin-bottom:10px;">Enter password:</div>
      <input name="pass" type="password" style="width:100%" />
      <button style="margin-top:10px;width:100%">Login</button>
      <?php if (!empty($err)) echo "<div style='margin-top:10px;color:#ff5b7a'>".$err."</div>"; ?>
    </form>
  </div>
</div>
</body></html>
<?php
exit;
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Developer • Suggestions</title>
  <style>
    body{font-family:system-ui;background:#0b0620;color:#f5f0ff;margin:0;padding:18px}
    .box{max-width:1100px;margin:0 auto}
    .item{padding:12px;border-radius:14px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.04);margin-top:10px}
    textarea{width:100%;min-height:54px;border-radius:12px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);color:#fff;padding:10px;outline:none}
    select,button{padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);color:#fff}
    button{cursor:pointer;font-weight:900}
    .row{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-top:10px}
    .pill{display:inline-block;padding:4px 10px;border-radius:999px;border:1px solid rgba(255,255,255,.12);font-size:12px;margin-left:8px}
  </style>
</head>
<body>
<div class="box">
  <h1>🧰 Suggestions Board</h1>
  <div id="list"></div>
</div>

<script>
const API = "./api";
const list = document.getElementById("list");
const esc = (s)=>String(s||"").replaceAll("&","&amp;").replaceAll("<","&lt;").replaceAll(">","&gt;");

async function loadAll(){
  const res = await fetch(`${API}/suggestions_admin_list.php`, {cache:"no-store"});
  const data = await res.json();
  if(!data.ok) return;

  list.innerHTML = data.items.map(it=>{
    return `
      <div class="item">
        <div>
          <b>#${it.id}</b> ${esc(it.suggestion)}
          <span class="pill">${esc(it.status).toUpperCase()}</span>
        </div>
        <div style="opacity:.8;margin-top:6px;font-size:13px">Created: ${esc(it.created_at)}</div>

        <div class="row">
          <select id="st_${it.id}">
            <option value="pending" ${it.status==="pending"?"selected":""}>pending</option>
            <option value="approved" ${it.status==="approved"?"selected":""}>approved</option>
            <option value="declined" ${it.status==="declined"?"selected":""}>declined</option>
          </select>
          <button onclick="saveReview(${it.id})">Save</button>
        </div>

        <div style="margin-top:10px">
          <textarea id="note_${it.id}" placeholder="Admin note shown to user...">${esc(it.admin_note||"")}</textarea>
        </div>
      </div>
    `;
  }).join("");
}

async function saveReview(id){
  const status = document.getElementById("st_"+id).value;
  const admin_note = document.getElementById("note_"+id).value;

  const res = await fetch(`${API}/suggestions_admin_review.php`, {
    method:"POST",
    headers:{ "Content-Type":"application/json" },
    body: JSON.stringify({ id, status, admin_note })
  });

  if(!res.ok){
    alert("Failed to save review.");
    return;
  }
  await loadAll();
}

loadAll();
</script>
</body>
</html>