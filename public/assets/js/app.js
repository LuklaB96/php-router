function showDropdown() {
    document.getElementById("moreDropdown").getElementsByClassName("dropdown-content")[0].style.display = "block";
}

function hideDropdown() {
    document.getElementById("moreDropdown").getElementsByClassName("dropdown-content")[0].style.display = "none";
}
function toggleDarkMode() {
    const body = document.body;
    body.classList.toggle('dark-mode');
    const isDarkMode = body.classList.contains('dark-mode');
    window.localStorage.setItem('darkMode', isDarkMode);
}

// Check and apply dark mode state on page load
function applyDarkModeState() {
    const body = document.body;
    const savedDarkModeState = window.localStorage.getItem('darkMode');
    if (savedDarkModeState === 'true') {
        body.classList.add('dark-mode');
    }
}