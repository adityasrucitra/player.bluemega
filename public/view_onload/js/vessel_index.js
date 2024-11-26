(function () {
    let vessels = document.getElementsByClassName('vessel-card');
    let inc = 0;
    for (let vessel of vessels) {
        console.log(`${vessel.dataset.baseUrl}/vessels/update/${vessel.dataset.vesselId}`);
        vessel.style.cursor = 'pointer';
        vessel.onclick = function () {
            window.location.href = `${this.dataset.baseUrl}/vessels/update/${this.dataset.vesselId}`;
        }
        inc++;
    }
})();
