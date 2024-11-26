(function(){
    const canvas = document.getElementById("qrcode-canvas");
    const svg = document.getElementById("qrcode-svg");
    canvas.style.display = "none";
    svg.style.display = "none";

    const ecl = qrcodegen.QrCode.Ecc.MEDIUM;
    const text = document.getElementById('citizen_number').value;
    console.log(text);
    const segs = qrcodegen.QrSegment.makeSegments(text);

    const minVer = 1;
    const maxVer = 40;
    const mask = -1;

    const boostEcc = true;
    const qr = qrcodegen.QrCode.encodeSegments(segs, ecl, minVer, maxVer, mask, boostEcc);

    const border = 4;
    const lightColor = '#FFFFFF';
    const darkColor = '#000000';

    const scale = 8;
    if (scale <= 0 || scale > 30)
        return;
    drawCanvas(qr, scale, border, lightColor, darkColor, canvas);
    canvas.style.removeProperty("display");

    // Draws the given QR Code, with the given module scale and border modules, onto the given HTML
    // canvas element. The canvas's width and height is resized to (qr.size + border * 2) * scale.
    // The drawn image is purely dark and light, and fully opaque.
    // The scale must be a positive integer and the border must be a non-negative integer.
    function drawCanvas(qr, scale, border, lightColor, darkColor, canvas) {
        if (scale <= 0 || border < 0)
            throw new RangeError("Value out of range");
        const width = (qr.size + border * 2) * scale;
        canvas.width = width;
        canvas.height = width;
        let ctx = canvas.getContext("2d");
        for (let y = -border; y < qr.size + border; y++) {
            for (let x = -border; x < qr.size + border; x++) {
                ctx.fillStyle = qr.getModule(x, y) ? darkColor : lightColor;
                ctx.fillRect((x + border) * scale, (y + border) * scale, scale, scale);
            }
        }
    }

    //------add fullscreen onclick qrcode-----//
    $(document).ready( function() {
        $("#qrcode-canvas").click( function() {
            this.requestFullscreen();
        });
    })
    
})();