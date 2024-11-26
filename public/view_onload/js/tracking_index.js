map=undefined;
function initTrack(data) {
    if(map!=undefined){
        //destroy previously generated map
        map.off();
        map.remove();
    }
    let el = document.getElementById('baseUrl');
    // let targetUrl = el.dataset.sourceUrl;
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
            L.tileLayer(mbUrl[x], {
                id: ids[x],
                attribution: mbAttr[x]
            })
        );
    }
    map = L.map('mapId').setView([0, 0], 9, {
        "layers": layers
    });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    let layerControlBase = {
        'Base': layers[0]
    };
    let layerControlSatellite = {
        'Satellite': layers[1]
    };
    L.control.layers(layerControlBase, layerControlSatellite).addTo(map);
    map.on('layeradd', (e) => {
        $('.leaflet-control').draggable();
    });

    //----add watermark----//
    L.Control.Watermark = L.Control.extend({
        onAdd: function (map) {
            var img = L.DomUtil.create('img');
            img.src = el.dataset.sourceUrl + '/asset/img/imt-logo.png';
            img.style.width = '200px';
            return img;
        },

        onRemove: function (map) {
            // Nothing to do here
        }
    });

    L.control.watermark = function (opts) {
        return new L.Control.Watermark(opts);
    }

    L.control.watermark({
        position: 'bottomleft'
    }).addTo(map);



    if(data.length == 0 ) return;

        initView = [data[0].lat, data[0].lng];
        map.setView(initView, 9)

        let baseUrl = document.getElementById('baseUrl').dataset.sourceUrl;

        let trackplayback = L.trackplayback(data, map, {
            clockOptions: {
                speed: 8,
                maxSpeed: 20
            },
            targetOptions: {
                useImg: true,
                imgUrl: baseUrl + '/dist/img/fuel-truck.png',
                width: 32,
                height: 32
            },
            trackLineOptions: {
                // whether draw track line
                isDraw: false,
                stroke: true,
                color: '#52FF33',
                weight: 2,
                fill: false,
                fillColor: '#000',
                opacity: 0.8
            }
        });
        const trackplaybackControl = L.trackplaybackcontrol(trackplayback);
        trackplaybackControl.addTo(map);
    
}