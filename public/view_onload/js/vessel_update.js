(
    function(){
        let vessel = document.getElementById('vesselId');
        if(vessel == undefined) return;
        let targetUrl = `${vessel.dataset.baseUrl}/vessels/get/${vessel.value}`;
        fetch(targetUrl)
        .then(response => response.json())
        .then(data => {
            for(let prop in data){
                let input = document.getElementsByName(prop);
                if(input.length == 0) continue;
                if(data[prop] != null){
                    if(prop == 'vessel_image'){
                        document.getElementById('vesselThumbnail').src = `${vessel.dataset.baseUrl}/${data[prop]}`;
                        continue;
                    }
                    input[0].value = data[prop];
                }else{
                    input[0].value = '';
                }                
            }
        });
    }
)();

/**
 * 
 * @param {*} event 
 */
function showPreview(event) {
    if (event.target.files.length > 0) {
        var src = URL.createObjectURL(event.target.files[0]);
        var preview = document.getElementById("vesselThumbnail");
        preview.src = src;
        preview.style.display = "block";
    }
}

$("#vesselThumbnail").click(function(){
    $('#inputVesselImage').trigger('click');
});