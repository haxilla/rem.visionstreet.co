document.getElementById('headlineSelect').addEventListener('change', function () {
    const selected = this.value;

    document.querySelectorAll('.hlGraphic').forEach(img => {
        const currentSrc = img.src;

        // extract color
        const colorMatch = currentSrc.match(/_([0-9a-fA-F]{6})_/);
        const color = colorMatch ? colorMatch[1] : 'ffffff';

        // extract style (ul, bold, 3d)
        const styleMatch = currentSrc.match(/_([a-z0-9]+)x\.png$/i);
        const style = styleMatch ? styleMatch[1] : 'ul';

        img.src = `https://www.realtyrepublic.com/images/headline_graphics/${selected}/${style}/${selected}_${color}_${style}x.png`;
    });
});

document.getElementById('headlineStyle').addEventListener('change', function () {
    const newStyle = this.value;

    document.querySelectorAll('.hlGraphic').forEach(img => {
        const currentSrc = img.src;

        // extract headline (folder + filename base)
        const nameMatch = currentSrc.match(/headline_graphics\/([^/]+)\//);
        const name = nameMatch ? nameMatch[1] : '';

        // extract color
        const colorMatch = currentSrc.match(/_([0-9a-fA-F]{6})_/);
        const color = colorMatch ? colorMatch[1] : 'ffffff';

        img.src = `https://www.realtyrepublic.com/images/headline_graphics/${name}/${newStyle}/${name}_${color}_${newStyle}x.png`;
    });
});

