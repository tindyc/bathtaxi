// Load current progress
fetch("progress.json")
    .then(res => res.json())
    .then(data => updateUI(data))
    .catch(err => console.error("Error loading progress:", err));

// Handle +1 Job button with confirmation
document.getElementById("addJobBtn").addEventListener("click", function () {
    if (confirm("Are you sure you want to mark this job as completed?")) {
        updateProgress("increment");
    }
});

// Send update request
function updateProgress(action) {
    fetch("update-progress.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=" + action
    })
        .then(res => res.json())
        .then(data => updateUI(data))
        .catch(err => console.error(err));
}

// Update UI elements
function updateUI(data) {
    document.getElementById("jobsDone").textContent = data.done;
    document.getElementById("jobsTotal").textContent = data.total;
    document.getElementById("jobsRemaining").textContent = data.remaining;
    document.getElementById("progressBar").style.width = data.percent + "%";
    document.getElementById("message").textContent = data.message;
}
