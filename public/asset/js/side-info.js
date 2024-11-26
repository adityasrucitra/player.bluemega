/**
 * 
 * @param {*} container is main container where side information placed 
 * @param {*} bubleModel is a child container where contain spesific information (this container is immutable)
 */
function SideInfo(container, source = {}){
    this.container = container;
    this.bubbles = {};
    this.paramVisibility = {};
    this.source = source;
}

SideInfo.prototype.addBubble = function(strKey, objInfo, opt){
    let elOut = document.createElement('div');
    elOut.className = 'bubble';
    elOut.style.border = 'black thin solid';
    // elOut.style.borderRadius = '5px';
    elOut.style.marginBottom = '5px';
    elOut.style.cursor = 'pointer';  
    elOut.style.padding = '5px';
    // elOut.style.position = 'relative';
    elOut.onclick = function(){        
        if(opt['center'] != undefined){
            opt['map'].setZoom(opt['zoom']);
            opt['map'].setView(
                opt['center']
            );
        }
    };

    let elIn = document.createElement('p');
    elIn.id = strKey;
    elIn.className = 'collapse';
    elIn.setAttribute('aria-expanded', false);
    this.paramVisibility[strKey] = {};
    strInf = `<span><strong>${objInfo['equipment_name']}</strong></span> <br>`;
    for(let inf in objInfo.param){
        elIn.dataset[inf] = objInfo.param[inf];
        this.paramVisibility[strKey][inf] = true;
        strInf += `${inf} : ${objInfo.param[inf]} <br>`; 
    }
    elIn.innerHTML = strInf;       
    elOut.appendChild(elIn);

    let btn = document.createElement('span');
    btn.className = 'glyphicon glyphicon-wrench';
    btn.style.position = 'absolute';
    btn.style.top = '0.5em';
    btn.style.right = '0.5em';
    btn.onclick = function(){ 

        //-----remove previously rendered modal content-----//
        let node = document.getElementById('modalContent');
        while(node.firstChild){
            node.removeChild(node.firstChild);
        }    
        //-----remove previously rendered modal footer-----//
        let node2 = document.getElementById('modalFooter');
        while(node2.firstChild){
            node2.removeChild(node2.firstChild);
        }       
        for(let pr in window.sideInfo.paramVisibility[strKey]){
            let row = document.createElement('div');
            row.className = 'row';
            let parName = document.createElement('div');
            parName.className = 'col-md-6';
            parName.innerHTML = pr;
            let parStat = document.createElement('input');
            parStat.type = 'checkBox';
            parStat.className = 'checkbox-visibility';
            parStat.checked =  window.sideInfo.paramVisibility[strKey][pr];
            parStat.onchange = function(){
                window.sideInfo.setParamVisibility(strKey, pr, this.checked, {
                    equipment_name: objInfo['equipment_name'],
                    parts_id: objInfo['parts_id']
                });
            }
            row.appendChild(parName);
            row.appendChild(parStat);
            document.getElementById('modalContent').appendChild(row);
        }
        
        //-----create check all and un-check all checkbox-----//
        let colCheckAll = document.createElement('div');
        colCheckAll.className = 'col-md-6';
        let inpCheckAll = document.createElement('input');
        inpCheckAll.checked = true;
        inpCheckAll.type = 'checkBox';
        inpCheckAll.onchange = function () {
            if (this.checked) {
                inpCheckAll.checked = true;
                let dt = document.getElementsByClassName('checkbox-visibility');
                for (let el of dt) {
                    el.checked = false;
                    el.click();
                }
            }else{
                inpCheckAll.checked = false;
                let dt = document.getElementsByClassName('checkbox-visibility');
                for (let el of dt) {
                    el.checked = true;
                    el.click();
                }
            }
        }
        let inpCheckAllTxt = document.createElement('span');
        inpCheckAllTxt.innerHTML = 'Check all';
        colCheckAll.appendChild(inpCheckAll);
        colCheckAll.appendChild(inpCheckAllTxt);
        document.getElementById('modalFooter').appendChild(colCheckAll);
        $('#modalParamVisibility').modal({show: true});
    }

    let anchor1 = document.createElement('a');
    anchor1.setAttribute('role', 'button');
    anchor1.className = 'collapsed';
    anchor1.setAttribute('data-toggle', 'collapse');
    anchor1.href = `#${strKey}`;
    anchor1.setAttribute('aria-expanded', false);
    anchor1.setAttribute('aria-controls', strKey);

    // elOut.appendChild(btn);
    elOut.append(anchor1);
    document.getElementById(this.container).appendChild(elOut)
    this.bubbles[strKey] = elOut;
}

SideInfo.prototype.removeBubble = function(strKey){
    delete this.bubbles[strKey];
}

SideInfo.prototype.updateBubble = function(strKey, objInfo, opt){
    let elem = document.getElementById(strKey);
    for(let inf in objInfo.param){
        elem['dataset'][inf] = objInfo.param[inf]; 
        this.bubbles[strKey][inf] = objInfo.param[inf];
    }
    this.bubbles[strKey].onclick = () => {
        if(opt['center'] != undefined){
            opt['map'].setZoom(opt['zoom']);
            opt['map'].setView(
                opt['center']
            );
        }
    }  
}


SideInfo.prototype.renderBubble = function() {
    for(let bubble in this.bubbles){
        let elem = document.getElementById(bubble);

        //-----remove previously rendered info-----//
        elem.innerHTML = '';

        let strInf = '';
        for(let inf in elem.dataset){
            if(this.paramVisibility[bubble][inf]){
                strInf += `${inf} : ${elem['dataset'][inf]} <br>`;
            }
        }
        elem.innerHTML = strInf;
    }
}

/**
 * 
 */
SideInfo.prototype.setParamVisibility = function(key, param, visibility, opt){
    this.paramVisibility[key][param] = visibility;
    $.post(
        this.source + 'setDataConfig',
        {

        },
        function(data, status){

        }
    );
    let elem = document.getElementById(key);
    let str = `<span><strong>${opt['equipment_name']}</strong></span> <br>`;
    for(let vs in elem.dataset){
        if(this.paramVisibility[key][vs]){
            str += `${vs} : ${elem.dataset[vs]} <br>`;
        }
    }
    elem.innerHTML = str;
}