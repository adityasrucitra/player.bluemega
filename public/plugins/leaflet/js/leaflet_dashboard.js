var leafletDashboard = {
    /**
     * 
     * @param {*} container 
     */
    renderMap: (container) => {
        L.Map.addInitHook(function () {
            // Keep track of added controls
            this.addedControls = [];

            // Override the addControl method
            var originalAddControl = this.addControl;
            this.addControl = function (control) {
                // Call the original method
                originalAddControl.call(this, control);

                // Add the control to the addedControls array
                this.addedControls.push(control);

                // Trigger a custom event or callback
                this.fire('controladded', { control: control });
            };
        });

        window.dataset = {};

        let mbAttr = [
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        ];
        let mbUrl = [
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
        ];
        let ids = ['mapbox.street', 'mapbox.satellite'];

        let layers = [];
        for (let x = 0; x < mbAttr.length; x++) {
            layers.push(
                L.tileLayer(mbUrl[x], { id: ids[x], attribution: mbAttr[x] })
            );
        }

        map = L.map(container, {
            zoomControl: false,
            layers: layers
        }).setView([-0.048534, 118.650299], 5);

        map.on("click", function (e) {
            // console.log(e.latlng.lat + "::" + e.latlng.lng);
        })

        let layerControlBase = {
            'Base': layers[0]
        };
        let layerControlSatellite = {
            'Satellite': layers[1]
        };
        L.control.layers(layerControlBase, layerControlSatellite).addTo(map);


        // map.addControl(L.control.zoom({
        //     position: 'bottomright'
        // }));

        map.on('layeradd', (e) => {
            $('.leaflet-control').draggable();
        });

        //----add watermark----//
        L.Control.Watermark = L.Control.extend({
            onAdd: function (map) {
                // var img = L.DomUtil.create('img');
                // img.src = 'https://www.indomegateknologi.com/web/public/uploads/logo/822-logo_imt_297x60_mm-01-01.png';
                // img.style.width = '200px';
                // return img;
            },

            onRemove: function (map) {
                // Nothing to do here
            }
        });

        // L.control.watermark = function (opts) {
        //     return new L.Control.Watermark(opts);
        // }

        // L.control.watermark({
        //     position: 'bottomleft'
        // }).addTo(map);

        return map;
    },

    /**
     * 
     * @param {*} strDatasource 
     * @returns 
     */
    getData: async (strDatasource) => {
        return await fetch(strDatasource)
            .then(response => response.json())
            .then(data => {
                let inc = 1;
                let mainCont = document.getElementById('right-pb');
                while (mainCont.firstChild) {
                    mainCont.removeChild(mainCont.firstChild);
                }

                for (let vessel of data) {
                    if (vessel['parts'] == undefined || vessel['parts'].length == 0) {
                        continue;
                    }

                    window.dataExcel[vessel.equipment_name] = {
                        info: [
                            ['Vessel name'],
                            [vessel['equipment_name']]
                        ],
                        overview: vessel['parts'][0]['latest_data'][0]['overview'],
                        weather: vessel['parts'][0]['latest_data'][0]['weather']
                    }

                    if (vessel.geofence_message != undefined) {
                        //---start: add geofence information---//
                        let info = document.createElement('div');
                        info.className = 'geofence-info';
                        info.style.marginTop = '5px';
                        if (inc % 2 == 1) {
                            info.style.backgroundColor = 'red';
                        } else {
                            info.style.backgroundColor = '#30F400';
                        }
                        let message = document.createElement('span');
                        let arr = [];
                        for (let mess of JSON.parse(vessel.geofence_message.message)) {
                            arr.push(`move from ${mess.origin.zone_name} to ${mess.dest.zone_name}`);
                        }
                        arr = arr.join(',');

                        let icn = document.createElement('span');
                        icn.className = 'glyphicon glyphicon-remove';
                        icn.onclick = () => {
                            info.remove();
                        }
                        info.appendChild(message);
                        info.appendChild(icn);
                        if (arr.length > 0) {
                            message.innerHTML = `${vessel.geofence_message.created_on} UTC >> ${vessel.equipment_name} >> ${arr}`;
                            mainCont.prepend(info);
                        }
                        inc++;
                    }
                    if (inc > 0) {
                        $('#alarm_title').show();
                    }
                    //---end: add geofence information---//

                    for (let vsl of vessel['parts']) {
                        window.sideInfoConfig = {};
                        let objInfo = {};
                        if (vsl['latest_data'].length > 0) {
                            //-----create info for left-side container-----//
                            objInfo.equipment_name = vessel.equipment_name;
                            objInfo.parts_id = vsl.parts_id;
                            objInfo.param = {};
                            for (let inf in vsl['latest_data'][0]) {
                                if (inf == 'id' || inf == 'created_on' || inf == 'deleted' || inf == 'modified_on' || inf == 'weather' || inf == 'overview') {
                                    continue;
                                }
                                let key = UcFirst(inf);
                                if (inf == 'latitude' || inf == 'longitude') {
                                    objInfo.param[key] = map.decimalToDMS(vsl['latest_data'][0][inf]) + ' ' + (vessel.units[inf] == undefined ? '' : vessel.units[inf]);
                                } else if (inf == 'speed') {
                                    objInfo.param[key] = (vsl['latest_data'][0][inf] * 0.54) + ' ' + (vessel.units[inf] == undefined ? '' : vessel.units[inf]);
                                } else {
                                    objInfo.param[key] = vsl['latest_data'][0][inf] + ' ' + (vessel.units[inf] == undefined ? '' : vessel.units[inf]);
                                }
                            }

                            let strInfo = '';
                            // for(let inf)

                            // if (window.sideInfo.bubbles.hasOwnProperty(`vessel_${vessel['equipment_id']}`)) {
                            //     //update
                            //     window.sideInfo.updateBubble(
                            //         `vessel_${vessel['equipment_id']}`,
                            //         objInfo, {
                            //         map: map.getMap(),
                            //         center: {
                            //             lat: parseFloat(vsl['latest_data'][0]['latitude']),
                            //             lng: parseFloat(vsl['latest_data'][0]['longitude'])
                            //         },
                            //         zoom: 15
                            //     }
                            //     );
                            // } else {
                            //     //add new
                            //     window.sideInfo.addBubble(
                            //         `vessel_${vessel['equipment_id']}`,
                            //         objInfo, {
                            //         map: map.getMap(),
                            //         center: {
                            //             lat: parseFloat(vsl['latest_data'][0]['latitude']),
                            //             lng: parseFloat(vsl['latest_data'][0]['longitude'])
                            //         },
                            //         zoom: 15
                            //     }

                            //     );
                            // }

                            //initialize marker
                            let markerColor = vessel['marker_color'];
                            let heading = parseFloat(vsl.latest_data[0]['heading']);
                            let marker = new google.maps.Marker({
                                position: {
                                    lat: parseFloat(vsl['latest_data'][0]['latitude']),
                                    lng: parseFloat(vsl['latest_data'][0]['longitude'])
                                },
                                map: map.markerVisibility[markerColor] ? window.map.getMap() : null,
                                icon: {
                                    path: "m 19.966311,0.74565711 c -0.02915,-0.005863 -0.06174,0.009942 -0.124731,0.0426298 -0.130656,0.0746638 -0.182812,0.13996546 -0.476811,0.62995849 -0.08868,0.1446499 -0.223714,0.312004 -0.298401,0.3773439 -0.07931,0.065342 -0.199994,0.205268 -0.269981,0.3126115 -0.07002,0.1073206 -0.177738,0.2472928 -0.238407,0.3126107 -0.06534,0.065342 -0.187619,0.2245189 -0.276299,0.3505025 -0.17266,0.2519951 -0.227685,0.3358807 -0.880996,1.4651687 C 17.232698,4.5258071 17.050919,4.8394272 16.994925,4.932754 16.8456,5.1847474 16.312804,6.2520509 16.09814,6.7373739 15.538157,8.0159843 15.253456,9.2854502 15.253456,10.536074 l 0,0.505231 -0.592063,0.03158 c -0.326658,0.01868 -0.606624,0.04285 -0.629962,0.05684 -0.01869,0.0139 -0.03789,0.905066 -0.03789,1.983029 l 0,1.954609 0.625221,0 c 0.527327,0 0.630305,0.01047 0.653643,0.07579 0.01389,0.042 0.02841,3.532108 0.02841,7.755286 0,4.955807 0.01784,7.750792 0.04579,7.872127 0.028,0.102643 0.06666,0.406663 0.09,0.677323 0.06999,0.807305 0.147891,1.408669 0.217881,1.641999 0.03733,0.121313 0.06631,0.317519 0.06631,0.434183 0,0.116663 0.02252,0.261327 0.05526,0.317347 0.028,0.05597 0.06968,0.261805 0.08368,0.453127 0.01869,0.191326 0.05727,0.442246 0.08526,0.558913 0.028,0.116663 0.148584,0.597638 0.265247,1.073614 0.116666,0.47598 0.242191,0.956949 0.284195,1.073615 0.0373,0.116663 0.09349,0.303243 0.116831,0.415235 0.07934,0.396644 0.220231,0.484363 0.691534,0.461023 0.116667,-0.0093 1.478867,-0.01753 3.02349,-0.0221 l 2.8135,-0.01422 0.07104,-0.107363 c 0.03733,-0.06067 0.08724,-0.271342 0.115258,-0.46734 0.02323,-0.200646 0.07477,-0.414755 0.112097,-0.489443 0.03733,-0.06999 0.122565,-0.345026 0.187883,-0.611009 0.07001,-0.26599 0.153645,-0.579699 0.186303,-0.701011 0.03735,-0.116661 0.09693,-0.423815 0.138938,-0.680479 0.042,-0.256644 0.0983,-0.550971 0.126307,-0.653642 0.028,-0.102647 0.07941,-0.368059 0.1121,-0.592069 0.03265,-0.223983 0.09854,-0.560049 0.140516,-0.742058 0.042,-0.186653 0.116121,-0.635357 0.172094,-0.994672 0.09802,-0.643986 0.09817,-0.820241 0.116835,-8.659966 0.01389,-6.061772 0.0322,-8.013008 0.0742,-8.036321 0.03266,-0.01868 0.275685,-0.0379 0.551015,-0.0379 0.317312,0 0.508928,-0.01795 0.546284,-0.05526 0.08398,-0.084 0.07912,-3.719866 -0.0048,-3.831857 -0.04665,-0.06532 -0.158415,-0.0791 -0.615749,-0.08842 l -0.560492,-0.01421 -0.02684,-0.768897 C 24.564132,9.8840604 24.530314,9.4208867 24.502307,9.2808856 24.390321,8.6835661 24.204302,7.9506733 24.124968,7.7920304 24.078293,7.6986799 23.970698,7.4332033 23.896035,7.1952276 23.70471,6.6352442 22.902886,5.0110086 22.478232,4.3343605 22.370889,4.1663742 22.238983,3.9572562 22.182989,3.8686011 22.126999,3.7799461 22.016023,3.6014541 21.936689,3.4754697 21.852679,3.349459 21.786698,3.2240881 21.786698,3.2007509 21.786698,3.1354089 21.52548,2.734672 21.268837,2.3986961 21.152174,2.2447037 20.997821,2.0340307 20.927807,1.9313589 20.797151,1.730687 20.568788,1.437447 20.316793,1.1387775 20.232816,1.0361312 20.11708,0.90098018 20.065759,0.83564025 c -0.04434,-0.0559957 -0.0703,-0.0841635 -0.09947,-0.0899933 z M 19.915791,1.861903 c 0.09335,0 0.531385,0.5775935 1.222025,1.6135777 0.466634,0.7093067 0.466725,0.7098012 0.742058,1.1904499 0.58797,1.0359613 0.695407,1.2365347 0.742056,1.3625437 l 0.0521,0.1357798 -0.243142,-0.023681 c -0.135355,-0.014139 -0.550318,-0.055885 -0.923624,-0.097889 -1.203974,-0.1306569 -3.225569,-0.084248 -3.855541,0.088415 -0.30334,0.084002 -0.433102,0.083951 -0.465761,0.00476 -0.01389,-0.037328 0.05035,-0.199795 0.143678,-0.3631342 0.09333,-0.1586675 0.228541,-0.4207053 0.307873,-0.5747003 0.153994,-0.3219821 0.126126,-0.270981 0.639433,-1.0736152 0.685967,-1.0686189 1.550188,-2.2624838 1.638841,-2.2624838 z m -0.195776,5.9506648 c 1.057392,-0.010723 2.202037,0.134682 3.014015,0.4041862 0.830619,0.2706581 0.905039,0.3406714 1.035722,0.9473054 0.09333,0.4199813 0.08358,0.4255384 -0.448393,0.1641999 -0.214663,-0.102648 -0.57346,-0.2425582 -0.806789,-0.3078744 -0.233329,-0.065342 -0.53237,-0.153774 -0.65838,-0.1957776 -0.31731,-0.097999 -3.350299,-0.112248 -3.770281,-0.01894 -0.671973,0.153995 -1.613985,0.5128391 -1.851986,0.7041642 -0.06064,0.051322 -0.126523,0.075437 -0.140517,0.052104 -0.01389,-0.02324 -0.01395,-0.2147185 0.0048,-0.4247092 0.028,-0.3266546 0.05227,-0.3965489 0.168938,-0.5178616 0.31731,-0.3359989 1.09069,-0.5693956 2.439313,-0.7420561 0.317318,-0.039664 0.661157,-0.061162 1.013619,-0.064731 z m 0.555752,1.5156915 c 0.272337,0.00476 0.530832,0.021353 0.764161,0.050524 0.788616,0.097998 1.998295,0.4723514 2.278273,0.7010087 0.07466,0.06067 0.200122,0.163527 0.279457,0.224195 0.270657,0.20532 0.336292,0.363816 0.336292,0.830473 0,0.592641 -6.03e-4,0.5925 0.685221,0.620486 l 0.551018,0.02368 0.03316,0.23209 c 0.01389,0.130657 0.01863,0.62132 0.0048,1.097297 -0.01869,0.793288 -0.06605,1.119401 -0.154726,1.119401 -0.01869,0 -0.05569,0.05152 -0.08368,0.116835 -0.05135,0.107343 -0.07442,0.11229 -0.508389,0.126307 l -0.457864,0.01421 -0.02841,2.729823 c -0.01389,1.502595 -0.03164,5.235685 -0.03632,8.301572 0,3.149883 -0.02394,5.697265 -0.04736,5.855932 -0.02323,0.158667 -0.111706,0.72801 -0.191038,1.264657 -0.08401,0.541317 -0.167727,1.027177 -0.191041,1.087823 -0.02323,0.06067 -0.139518,0.149406 -0.284191,0.214722 -0.289302,0.135329 -1.433124,0.373729 -2.137757,0.448392 -0.27066,0.028 -0.532826,0.06015 -0.584171,0.06947 -0.172663,0.03268 -1.768537,-0.02273 -2.099866,-0.06947 -0.671973,-0.098 -1.941498,-0.42927 -2.016185,-0.522598 -0.028,-0.03265 -0.08279,-0.312518 -0.129464,-0.620486 -0.042,-0.30799 -0.107834,-0.663501 -0.140517,-0.794156 -0.03733,-0.125989 -0.09848,-0.621322 -0.135782,-1.097298 -0.06534,-0.741966 -0.0742,-1.997002 -0.0742,-8.819431 0,-4.377148 -0.01926,-7.979916 -0.03789,-8.007902 -0.01869,-0.02801 -0.26061,-0.06028 -0.582595,-0.07895 -0.307988,-0.0139 -0.570178,-0.0366 -0.584171,-0.05526 -0.042,-0.042 -0.03739,-2.24985 0.0048,-2.459841 l 0.03789,-0.17683 0.453129,0 c 0.247322,0 0.47573,-0.01925 0.508389,-0.03789 0.07933,-0.05132 0.200513,-0.40535 0.200513,-0.58733 0,-0.387323 0.05638,-0.509443 0.341029,-0.789423 0.363986,-0.3499935 0.788563,-0.5555326 1.57253,-0.7515311 0.703471,-0.1819869 1.638097,-0.2747256 2.455105,-0.2605092 z m -0.36629,3.0376987 c -0.04724,0.0035 -0.09854,0.03002 -0.1942,0.08368 -0.387323,0.20067 -0.462868,0.611636 -0.164201,0.863629 0.07931,0.06534 0.172592,0.12157 0.205251,0.12157 0.03266,0 0.08898,0.0096 0.126307,0.01895 0.03266,0.0093 0.103404,0.0048 0.154729,-0.01421 0.499314,-0.158642 0.521698,-0.789676 0.04105,-1.018355 -0.07934,-0.03966 -0.121691,-0.05877 -0.168935,-0.05526 z m -0.0821,2.944545 -2.309847,0.01421 -0.224198,0.112097 c -0.121312,0.056 -0.275688,0.176503 -0.34103,0.260509 -0.256667,0.335975 -0.252616,0.256415 -0.252616,5.45491 0,5.357119 0.01004,5.525093 0.25735,5.781732 0.07931,0.08401 0.238577,0.196555 0.355243,0.247881 0.195997,0.098 0.28937,0.102906 1.791987,0.121569 0.872623,0.01389 1.963144,0.0094 2.425106,-0.0048 0.760653,-0.02326 0.86356,-0.03787 1.068879,-0.140518 0.195974,-0.098 0.243248,-0.148969 0.355239,-0.396288 l 0.131044,-0.284193 0.01893,-3.827123 c 0.028,-4.792507 -0.0049,-6.564745 -0.116835,-6.770088 -0.111991,-0.214663 -0.424986,-0.466665 -0.653643,-0.527335 -0.140001,-0.042 -0.858357,-0.05197 -2.505626,-0.04263 z m 0.124728,2.006712 c 0.113745,0.0041 0.224802,0.04705 0.369451,0.131045 0.50399,0.284676 0.900652,1.012579 1.157295,2.099863 0.130656,0.564657 0.181157,1.244955 0.153147,2.140914 -0.03266,1.236631 -0.166699,1.890789 -0.544702,2.688775 -0.247321,0.527302 -0.537773,0.881294 -0.813106,1.007303 -0.247322,0.10732 -0.372082,0.107448 -0.652063,0.0095 -0.937962,-0.340675 -1.578112,-2.422206 -1.424118,-4.648124 0.06999,-1.040633 0.233835,-1.726137 0.56049,-2.388792 0.237977,-0.485323 0.508122,-0.797831 0.806789,-0.937833 0.156332,-0.07233 0.273075,-0.106706 0.386817,-0.102625 z m -0.01262,11.236644 c -0.699983,0 -0.877507,0.0288 -0.961515,0.159463 -0.042,0.06532 -0.0468,2.91086 -0.0095,3.400834 0.028,0.312658 0.04258,0.354373 0.145253,0.401024 0.163315,0.07469 1.558297,0.07007 1.660945,-0.0048 0.06999,-0.056 0.07427,-0.237095 0.07894,-1.833041 0.0048,-0.975291 0.0048,-1.809981 0.0048,-1.851984 -0.0048,-0.04667 -0.04656,-0.127086 -0.09789,-0.178409 -0.08399,-0.08398 -0.153698,-0.09315 -0.820999,-0.09315 z m 3.250844,5.885933 c 0.07874,0 0.08999,0.04314 0.08999,0.129465 0,0.07934 -0.01926,0.148627 -0.0379,0.162622 -0.06999,0.042 -1.091538,0.318039 -1.33886,0.364713 -0.615979,0.107343 -1.339923,0.162685 -2.147229,0.167356 -0.793312,0.0093 -0.965569,-0.0037 -1.656208,-0.129465 -0.793312,-0.14 -1.324908,-0.261416 -1.446222,-0.322081 -0.03735,-0.02326 -0.07436,-0.09419 -0.08368,-0.164203 -0.01389,-0.102644 0.0044,-0.121571 0.09315,-0.121571 0.167987,0 0.639643,0.08954 0.863627,0.1642 0.657981,0.219337 2.225669,0.307706 3.350308,0.191042 1.045282,-0.111995 1.333443,-0.158615 1.814092,-0.312613 0.272986,-0.08633 0.420172,-0.129465 0.498917,-0.129465 z m -0.161042,0.887311 c 0.04221,0.002 0.06804,0.01009 0.07737,0.02527 0.01389,0.02326 0.02021,0.08919 0.0063,0.140516 -0.01869,0.07466 -0.122147,0.12146 -0.476811,0.21946 -0.741967,0.200648 -1.161342,0.275842 -2.061972,0.350506 -0.71398,0.06067 -1.153969,0.05574 -1.774622,-0.01896 -1.101303,-0.13533 -1.847637,-0.284234 -1.978293,-0.391555 -0.042,-0.03733 -0.0742,-0.117743 -0.0742,-0.178409 0,-0.135329 -0.07037,-0.13929 0.816262,0.04736 0.951981,0.20067 1.321427,0.228588 2.474052,0.205251 1.180635,-0.02326 1.613349,-0.07054 2.229328,-0.247879 0.367496,-0.105002 0.635934,-0.157478 0.762582,-0.151569 z m -0.472075,1.067301 c 0.172661,0 0.171685,0.0039 0.143676,0.153146 -0.01869,0.0793 -0.04124,0.214398 -0.05526,0.2984 -0.04198,0.233333 -0.140421,0.289149 -0.555754,0.303139 -0.975293,0.03733 -4.735518,0.01042 -4.772849,-0.03158 -0.03734,-0.042 -0.108485,-0.252778 -0.183145,-0.579432 -0.02326,-0.102673 -0.01795,-0.107016 0.154726,-0.08368 0.098,0.0093 0.382574,0.07031 0.625224,0.126308 0.541317,0.121335 2.146844,0.177194 2.96349,0.09789 0.709308,-0.07002 1.286994,-0.163594 1.408332,-0.228931 0.056,-0.03266 0.178236,-0.05526 0.271561,-0.05526 z",
                                    fillColor: markerColor,
                                    fillOpacity: 1,
                                    scale: 1,
                                    strokeColor: markerColor,
                                    rotation: heading,
                                    animation: google.maps.Animation.BOUNCE
                                }
                            });
                            marker.onlineStatus = markerColor;
                            marker.visible = map.markerVisibility[markerColor];
                            //initialize infoWindow
                            let infoWindow = new google.maps.InfoWindow({
                                content: `
                                    <div class="container container-infowindow" style="width:600px; border: thin solid black;">
                                        <div class="row">
                                            <div class="col-sm-12" style='background-color: grey;'>
                                                <h5 class='text-center'>${vessel['equipment_name']}</h5>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">                                                    
                                                <div class="row">
                                                    <div class="col-sm-5">
                                                        Last position time
                                                    </div>
                                                    <div class="col-sm-7">
                                                        : ${vsl['latest_data'][0]['created_on']} (UTC)
                                                    </div>
                                                </div>    
                                                <div class="row">
                                                    <div class="col-sm-5">
                                                        latitude
                                                    </div>
                                                    <div class="col-sm-7">
                                                        : ${map.decimalToDMS(vsl['latest_data'][0]['latitude'])}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-5">
                                                        longitude
                                                    </div>
                                                    <div class="col-sm-7">
                                                        : ${map.decimalToDMS(vsl['latest_data'][0]['longitude'])}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-5">
                                                        speed
                                                    </div>
                                                    <div class="col-sm-7">
                                                        : ${(vsl['latest_data'][0]['speed'] * 0.539957).toFixed(3)} Knot 
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-5">
                                                        heading
                                                    </div>
                                                    <div class="col-sm-7">
                                                        : ${vsl['latest_data'][0]['heading']}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="row">
                                                    <div class="col-sm-6 block-icon">
                                                        <i class="wi wi-thermometer icon"></i> Temperature
                                                    </div> 
                                                    <div class="col-sm-6">
                                                        : <span id='owm-${vessel}-temp'></span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6 block-icon">
                                                        <i class="wi wi-barometer"></i> Pressure
                                                    </div>
                                                    <div class="col-sm-6">
                                                        : <span id="owm-${vessel}-pressure"></span>
                                                    </div>
                                                </div>    
                                                <div class="row">
                                                    <div class="col-sm-6 block-icon">
                                                        <i class="wi wi-humidity"></i> Humidity
                                                    </div>
                                                    <div class="col-sm-6">
                                                        : <span id="owm-${vessel}-humidity"></span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6 block-icon">
                                                        <i class="wi wi-flood"></i> Sea level
                                                    </div>
                                                    <div class="col-sm-6">
                                                        : <span id="owm-${vessel}-sea_level"></span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6 block-icon">
                                                        <i class="wi wi-rotate-180 wi-flood"></i> Ground level
                                                    </div>
                                                    <div class="col-sm-6">
                                                        : <span id="owm-${vessel}-grnd_level"></span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6 block-icon">
                                                        <i class="wi wi-strong-wind"></i> Wind speed
                                                    </div>
                                                    <div class="col-sm-6">
                                                        : <span id="owm-${vessel}-speed"></span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6 block-icon">
                                                        <i class="wi wi-wind from-225-deg"></i> Wind direction
                                                    </div>
                                                    <div class="col-sm-6">
                                                        : <span id="owm-${vessel}-deg"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4" style="margin-top:5px;">
                                                <span class="badge badge-pill badge-info" onclick='export_excel("${vessel['equipment_name']}")'>Export weather dataset </span>
                                            </div>
                                        </div>     
                                    </div>
                                    `
                            });
                            infoWindow.isOpen = false;
                            marker.addListener('click', function () {
                                if (infoWindow.isOpen) {
                                    infoWindow.isOpen = false;
                                    infoWindow.close();
                                } else {
                                    infoWindow.isOpen = true;
                                    let unitFormat = 'metric'; //standard, metric or imperial
                                    let dataProviderOWM = `https://api.openweathermap.org/data/2.5/weather?lat=${vsl['latest_data'][0]['latitude']}&lon=${vsl['latest_data'][0]['longitude']}&units=${unitFormat}&appid=185105b41a19b97b69d815411674201d`;
                                    $.get(dataProviderOWM, function (data) {
                                        for (let info in data['main']) {
                                            let elem = document.getElementById(`owm-${vessel}-${info}`);
                                            if (elem) {
                                                elem.innerHTML = data['main'][info] + ' ' + owmUnits[unitFormat][info];
                                            }
                                        }
                                        for (let info in data['wind']) {
                                            let elem = document.getElementById(`owm-${vessel}-${info}`);
                                            if (elem) {
                                                elem.innerHTML = data['wind'][info] + ' ' + owmUnits[unitFormat][info];
                                            }
                                        }
                                    });
                                    infoWindow.open(map.getMap(), marker);
                                }
                            });
                            map.addMarker(marker);
                        } //ends if  
                    }
                } //ends for

            });
    },

    /**
     * 
     */
     decimalToDMS(decimal){
        let degree =    Math.floor(decimal);
        let minute = (decimal - degree) * 60;
        let second  = ((minute-Math.floor(minute)) * 60).toFixed(2);
        minute = Math.floor(minute);
        return `${degree} &deg ${minute}' ${second}" `;
    }


}