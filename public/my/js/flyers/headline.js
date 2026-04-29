document.getElementById('headlineSelect').addEventListener('change', function () {
    const selected = this.value;

    document.querySelectorAll('.hlGraphic').forEach(img => {
        img.src = `https://www.realtyrepublic.com/images/headline_graphics/${selected}/ul/${selected}_ffc60b_ulx.png`;
    });
});