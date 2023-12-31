document.querySelectorAll('.card').forEach(function(element) {
    element.addEventListener('mouseover', function() {
        this.classList.add('mouseover');
    });
    element.addEventListener('mouseout', function() {
        this.classList.remove('mouseover');
    });
});

// random ticket;
// var max = 200;
// var ticket = Math.random();
// ticket = ticket * (max - 100) + 100; // This will now generate a number between 100 and 200.
// ticket = Math.round(ticket);
// ticket = "A"+ticket;
// console.log(ticket);

