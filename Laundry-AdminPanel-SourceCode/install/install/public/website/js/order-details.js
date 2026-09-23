
const stars = document.querySelectorAll(".rs-rate-star .star");
stars.forEach((star, index) => {
    star.addEventListener("click", () => {
        stars.forEach((s, i) => {
            if (i <= index) {
                s.src = "../assets/icons/star-big.svg";
            } else {
                s.src = "../assets/icons/star-big-grey.svg";
            }
        });
    });
});


// const ratingToggler = (containerClass,) => {
//     let container = document.querySelectorAll(containerClass)

    
// }