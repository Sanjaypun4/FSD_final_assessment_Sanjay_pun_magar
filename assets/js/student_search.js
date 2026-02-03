document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search");
    const tableBody = document.getElementById("studentTable");

    if (!searchInput || !tableBody) return;

    searchInput.addEventListener("keyup", function () {
        const query = searchInput.value;

        fetch("../ajax/search_student.php?q=" + encodeURIComponent(query))
            .then(res => res.text())
            .then(data => {
                tableBody.innerHTML = data;
            })
            .catch(err => console.error(err));
    });
});
