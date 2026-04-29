document.getElementById('headlineSelect').addEventListener('change', function () {
    const selected = this.value;

    document.querySelectorAll('.hlGraphic').forEach(img => {
        const currentSrc = img.src;

        // extract color between _ and _
        const match = currentSrc.match(/_([0-9a-fA-F]{6})_/);
        const color = match ? match[1] : 'ffffff'; // fallback if not found

        img.src = `https://www.realtyrepublic.com/images/headline_graphics/${selected}/ul/${selected}_${color}_ulx.png`;
    });
});