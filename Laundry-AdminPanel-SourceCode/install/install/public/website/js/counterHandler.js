function counterController(id, action) {
    const container = document.getElementById(id);

    // Find the <div class="current_count">
    const counterEl = container.querySelector(".current_count");
    console.log(counterEl);
    
    // Get current value, convert to number
    let currentValue = parseInt(counterEl.textContent);

    // Update value
    if (action === "increase") {
        currentValue++;
    } else if (action === "decrease") {
        currentValue = Math.max(0, currentValue - 1); // prevents going below 0
    }

    // Set updated value
    counterEl.textContent = currentValue;
}
