(
    function () {
        let profile = document.getElementById('profileId');
        if(profile == undefined) return;

        let elem = document.getElementById('targetUrl');
        if(elem == undefined) return;

        fetch(elem.value)
            .then(response => response.json())
            .then((data) => {
                for(let prop in data){
                    if(prop == 'active'){
                        let isActive = data[prop] == 1 ? true : false;
                        $("[name='active']").bootstrapSwitch('state', isActive);
                        continue;
                    }
                    if(prop == 'qr_code'){  
                        if(data[prop] !=''){
                            $('#qr_code').append(data[prop]);  
                            continue;
                        } 
                        
                    }

                    if(prop == 'role_id'){
                        $('#input_role').val(data[prop]); 
                        continue;
                    }

                    if(prop == 'role_name'){
                        var oOpt = new Option(data['role_name'], data['role_id'], true, true);
                        $('#sel_roles').append(oOpt).trigger('change');
                        continue;
                    }

                    if(prop == 'country_id'){
                        var oOpt = new Option(data['country_name'], data['country_id'], true, true);
                        $('#select2_countries').append(oOpt).trigger('change');
                        $('#country_id').val(data['country_id']);
                        continue;
                    } 

                    if(prop == 'state_id'){
                        var oOpt = new Option(data['state_name'], data['state_id'], true, true);
                        $('#select2_states').append(oOpt).trigger('change');
                        $('#state_id').val(data['state_id']);
                        continue;
                    }

                    if(prop == 'city_id'){
                        var oOpt = new Option(data['city_name'], data['city_id'], true, true);
                        $('#select2_cities').append(oOpt).trigger('change');
                        $('#city_id').val(data['city_id']);
                        continue;
                    }

                    if(prop == 'timezone'){
                        let input = document.getElementsByName(prop);
                        let oOpt = new Option(data['timezone'], -1, true, true);
                        $('#sel_timezones').append(oOpt).trigger('change');

                        input[0].value = data[prop];

                        continue;
                    }

                    if(prop == 'id'){
                        $('#profileId').val(data[prop])
                        continue;
                    }     
                    
                    if(prop == 'social_media'){
                        for(let sm of data[prop]){
                            $(`#sm_${sm.social_media}`).val(sm.username);
                        }
                        continue;
                    }     

                    let input = document.getElementsByName(prop);
                    if(input.length == 0) continue;
                    if(data[prop] != null){
                        let pt = document.getElementById('profileThumbnail');
                        if(prop == 'profile_image'){ 
                            if(data[prop] == ""){
                                pt.style.display = 'none'; 
                                continue;
                            }                            
                            pt.src = `${profile.dataset.baseUrl}/${data[prop]}`; 
                            pt.style.display = 'block';
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


function showPreview(event) {
    if (event.target.files.length > 0) {
        var src = URL.createObjectURL(event.target.files[0]);
        var preview = document.getElementById("profileThumbnail");
        preview.src = src;
        preview.style.display = "block";
    }
}


$("#profileThumbnail").click(function(){
    $('#inputProfileImage').trigger('click');
});