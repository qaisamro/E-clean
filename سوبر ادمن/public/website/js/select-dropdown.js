document.querySelectorAll(".select-dropdown li").forEach(li => {
    li.onclick = () =>
        document.querySelector(".select-dropdown > b").textContent =
        li.querySelector("b").textContent;
});
