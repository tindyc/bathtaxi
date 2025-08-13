// Load current progress
fetch("progress.json")
    .then(res => res.json())
    .then(data => updateUI(data));

// Handle +1 Job button click with confirmation
const addJobBtn = document.getElementById("addJobBtn");

addJobBtn.addEventListener("click", function () {
    const jobsDone = parseInt(document.getElementById("jobsDone").textContent);
    const jobsTotal = parseInt(document.getElementById("jobsTotal").textContent);

    if (jobsDone >= jobsTotal) {
        alert("🎉 Goal reached! No more jobs to add.");
        return;
    }

    if (confirm("Are you sure you want to mark this job as completed?")) {
        addJobBtn.disabled = true;
        addJobBtn.textContent = "Updating...";
        updateProgress("increment");
    }
});

// Send update request to PHP
function updateProgress(action) {
    fetch("update-progress.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=" + action
    })
    .then(res => res.json())
    .then(data => {
        updateUI(data);
        addJobBtn.disabled = false;
        addJobBtn.textContent = "+1 Job Completed";
    })
    .catch(err => {
        console.error(err);
        addJobBtn.disabled = false;
        addJobBtn.textContent = "+1 Job Completed";
    });
}

// Update HTML elements
function updateUI(data) {
    const messageEl = document.getElementById("message");
    const progressBar = document.getElementById("progressBar");

    document.getElementById("jobsDone").textContent = data.done;
    document.getElementById("jobsTotal").textContent = data.total;
    document.getElementById("jobsRemaining").textContent = data.remaining;
    progressBar.style.width = data.percent + "%";

    if (data.done >= data.total) {
        messageEl.textContent = "🎉 Congratulations! You can now go buy Tindy a meal 🍽️😄";
        messageEl.classList.add("celebrate");
        addJobBtn.disabled = true;
        addJobBtn.textContent = "Goal Reached 🎉";
        progressBar.style.background = "#FFD700"; // gold bar
    } else {
        messageEl.textContent = data.message;
        messageEl.classList.remove("celebrate");
        progressBar.style.background = "#28a745"; // green bar
    }
}
