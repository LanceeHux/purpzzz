const input = document.getElementById("to-do-text")
const button = document.getElementById("add-btn")
const list = document.getElementById("list")
const dailyChanges = document.getElementById("dailyChanges");
const newBtn = document.getElementById("newBtn");
const inputRow = document.getElementById("inputRow");
const doneBtn = document.getElementById("doneBtn");

const dailyTexts = [
    "Muchiee's here! Hawayou? Did you get tired?",
    "Muchiee doesn't want you to get sick!! :P",
    "Muchie thinks how can he help your pending tasks!",
    "Big sibling, did you rest properly today?",
    "Muchie believes you can finish that task. Even the scary one!",
    "If it feels heavy, Muchie will sit beside you while you do it!",
    "One step at a time, okay? Muchie is counting with you! ;>",
    "You look serious today? That means productivity mode is on, right?",
    "Muchie says drink water. WATAH, right now!",
    "Even heroes need breaks. You are a hero!",
    "Muchie is proud of you already. You showed up today!",
    "If you feel tired, lean on Muchie for a bit!"
];

// Load tasks on page load
window.addEventListener("DOMContentLoaded", () => {
    fetch("get_tasks.php")
        .then(res => res.json())
        .then(tasks => {
            tasks.forEach(data => addTaskToDOM(data));
            checkEmptyState();   // 👈 THIS is Step 2
        })
        .catch(() => {
            checkEmptyState();   // also show empty if fetch fails
        });
});

// Add task on click or Enter
button.addEventListener("click", addTask)
input.addEventListener("keypress", function(e) {
  if (e.key === "Enter") addTask()
})

// Function to add task via API
function addTask() {
  const value = input.value.trim();
  if (!value) return;

  // create a readable timestamp on the client
  const now = new Date();
  const created_at = now.toISOString(); // best for DB storage

  fetch("add_task.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ task: value, created_at })
  })
    .then(res => res.json())
    .then(data => {
      addTaskToDOM(data);
      input.value = "";
    });
}

function checkEmptyState() {
  const existingMessage = document.getElementById("emptyMessage");

  if (list.querySelectorAll(".to-do-activity").length === 0) {
    if (!existingMessage) {
      const empty = document.createElement("p");
      empty.id = "emptyMessage";
      empty.textContent = "No tasks yet? Muchie Muchiee is waiting for your first mission! <3";
      empty.style.opacity = "0.7";
      empty.style.fontSize = "13px";
      empty.style.marginTop = "10px";
      empty.style.color = "rgba(214,201,255,.85)";
      list.appendChild(empty);
    }
  } else {
    if (existingMessage) {
      existingMessage.remove();
    }
  }
}
function addTaskToDOM(data) {
  const task = document.createElement("div");
  task.className = "to-do-activity";

  const checkbox = document.createElement("input");
  checkbox.type = "checkbox";

  const textWrap = document.createElement("div");
  textWrap.style.flex = "1";
  textWrap.style.display = "flex";
  textWrap.style.flexDirection = "column";
  textWrap.style.gap = "4px";

  const text = document.createElement("p");
  text.textContent = data.task;

  // ---- DATE LINE ----
  const dateLine = document.createElement("small");
  dateLine.className = "task-date";

  const iso = data.created_at || data.createdAt || data.date_created || null;

  if (iso) {
    let d;

    if (typeof iso === "string" && iso.includes(" ")) {
      // MySQL format: "YYYY-MM-DD HH:MM:SS"
      // Convert to ISO UTC: "YYYY-MM-DDTHH:MM:SSZ"
      d = new Date(iso.replace(" ", "T") + "Z");
    } else {
      // Already ISO formatted
      d = new Date(iso);
    }

    dateLine.textContent = d.toLocaleString("en-PH", {
      timeZone: "Asia/Manila",
      weekday: "short",
      month: "short",
      day: "2-digit",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit"
    });

  } else {
    dateLine.textContent = "—";
  }

  // ---- CHECKBOX ----
  checkbox.addEventListener("change", () => {
    task.classList.toggle("completed", checkbox.checked);
  });

  // ---- REMOVE BUTTON ----
  const removeBtn = document.createElement("button");
  removeBtn.textContent = "Remove";
  removeBtn.addEventListener("click", () => {
  fetch(`delete_task.php?id=${data.id}`, { method: "DELETE" })
    .then(() => {
      list.removeChild(task);
      checkEmptyState();
    });
});

  // ---- APPEND ----
  textWrap.appendChild(text);
  textWrap.appendChild(dateLine);

  task.appendChild(checkbox);
  task.appendChild(textWrap);
  task.appendChild(removeBtn);

  list.appendChild(task);
   checkEmptyState();
}

let number = Math.floor(Math.random() * 12);
dailyChanges.innerText = dailyTexts[number];

newBtn.addEventListener("click", function() {
    inputRow.style.display = 'flex';
    newBtn.style.display = "none";
    doneBtn.style.display = "block";
});

doneBtn.addEventListener("click", function() {
    inputRow.style.display = "none";
    newBtn.style.display = "block";
    doneBtn.style.display = "none";
});
