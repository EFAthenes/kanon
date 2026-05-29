var id_map_leaflet = 'mapTopo';
var normal = null;
var france = null;
var awmc = null;
var vms = null;
var stadia_smooth = null;
var kms = null;
var geojson = null;
var satellite = null;
var roadmap = null;
var mymap = null;
var layerControl=null;
var mymax_zoom=null;
var site_basemap=null;
var redIcon = null;
var blueIcon = null;
var yellowIcon = null;
var noIcon = null;
var baseMaps = null;
var otherLayers = null;
var markers = null;
var markerMovable = null;
var markerY = null;


var search_markersAll = null;
var search_markerLocation = null;
var search_marker = null;
var search_popupText = null;

var open_streetmap_attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';

//var markersAll = null;  
//var popup = null;
//var markersTab = null;
//var markerLocation=null;
//var marker = null;
//var popupText=null; 

function createMarkerCluster() {
    let markerCluster = L.markerClusterGroup({
        iconCreateFunction: function (cluster) {
            var t = cluster.getChildCount(),
                    i = " marker-cluster-";
            return i += 10 > t ? "small" : 100 > t ? "medium" : "large", new L.DivIcon({html: "<div><span>" + t + "</span></div>", className: "marker-cluster mycluster" + i, iconSize: new L.Point(45, 45)})
        }
    });
    return markerCluster;
}


if (document.getElementById(id_map_leaflet)) {
    initLeafLetMap(id_map_leaflet);
}

function initLeafLetMap(id_map_leaflet,center_x,center_y,zoom,max_zoom,init_GIS_EFA) 
{  
    normal = L.tileLayer.wms('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        id: 'normal',
        maxZoom: 20,
        maxNativeZoom: 19, // OSM max available zoom is at 19.     
        attribution: open_streetmap_attribution
    });

    france = L.tileLayer.wms('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
        id: 'france',
        maxZoom: 20,
        maxNativeZoom: 19, // OSM max available zoom is at 19. 
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap France</a>'
    });


    vms = L.tileLayer.wms('https://geoserver.efa.gr/geoserver/wms?', {
        id: 'vms',
        layers: 'SIG_delos:fd_delos',
        transparent: true,
        maxZoom: 20,
        opacity: 0.5
    });

    vms2 = L.tileLayer.wms('https://geoserver.efa.gr/geoserver/wms?', {
        id: 'vms2',
        layers: 'SIG_thasos:fd_thasos_4326',
        transparent: true,
        maxZoom: 20,
        opacity: 0.5
    });

    awmc = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiaXNhd255dSIsImEiOiJBWEh1dUZZIn0.SiiexWxHHESIegSmW8wedQ', {

        maxZoom: 16,
        id: 'isawnyu.map-knmctlkh',
        accessToken: 'pk.eyJ1IjoiaXNhd255dSIsImEiOiJBWEh1dUZZIn0.SiiexWxHHESIegSmW8wedQ'
    });

    //kms = omnivore.kml('other/hydro-thasos.kml');

    //geojson = omnivore.geojson('other/hydro-thasos.json');


    satellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google <a href="https://maps.google.com">Google Maps</a>'
    });

    roadmap = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google <a href="https://maps.google.com">Google Maps</a>'
    });

//    stadia_smooth = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.png', {
//        maxZoom: 20,
//        attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
//    });

 //center_x,center_y,zoom,max_zoom
    mymap = L.map(id_map_leaflet, {
        maxZoom: max_zoom,
        center: [center_x, center_y],
        zoom: zoom,
        fullscreenControl: true,
        layers: [normal],
        pmIgnore: false,
    });

    
    redIcon = L.icon({
        //iconUrl: 'css/images/marker-icon-red.png',
        iconUrl: 'data:image/png;base64,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',
        iconSize: [25, 41],
    });

    blueIcon = L.icon({
        //iconUrl: 'css/images/marker-icon-blue.png',
        // iconUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAApCAYAAADAk4LOAAAFgUlEQVR4Aa1XA5BjWRTN2oW17d3YaZtr2962HUzbDNpjszW24mRt28p47v7zq/bXZtrp/lWnXr337j3nPCe85NcypgSFdugCpW5YoDAMRaIMqRi6aKq5E3YqDQO3qAwjVWrD8Ncq/RBpykd8oZUb/kaJutow8r1aP9II0WmLKLIsJyv1w/kqw9Ch2MYdB++12Onxee/QMwvf4/Dk/Lfp/i4nxTXtOoQ4pW5Aj7wpici1A9erdAN2OH64x8OSP9j3Ft3b7aWkTg/Fm91siTra0f9on5sQr9INejH6CUUUpavjFNq1B+Oadhxmnfa8RfEmN8VNAsQhPqF55xHkMzz3jSmChWU6f7/XZKNH+9+hBLOHYozuKQPxyMPUKkrX/K0uWnfFaJGS1QPRtZsOPtr3NsW0uyh6NNCOkU3Yz+bXbT3I8G3xE5EXLXtCXbbqwCO9zPQYPRTZ5vIDXD7U+w7rFDEoUUf7ibHIR4y6bLVPXrz8JVZEql13trxwue/uDivd3fkWRbS6/IA2bID4uk0UpF1N8qLlbBlXs4Ee7HLTfV1j54APvODnSfOWBqtKVvjgLKzF5YdEk5ewRkGlK0i33Eofffc7HT56jD7/6U+qH3Cx7SBLNntH5YIPvODnyfIXZYRVDPqgHtLs5ABHD3YzLuespb7t79FY34DjMwrVrcTuwlT55YMPvOBnRrJ4VXTdNnYug5ucHLBjEpt30701A3Ts+HEa73u6dT3FNWwflY86eMHPk+Yu+i6pzUpRrW7SNDg5JHR4KapmM5Wv2E8Tfcb1HoqqHMHU+uWDD7zg54mz5/2BSnizi9T1Dg4QQXLToGNCkb6tb1NU+QAlGr1++eADrzhn/u8Q2YZhQVlZ5+CAOtqfbhmaUCS1ezNFVm2imDbPmPng5wmz+gwh+oHDce0eUtQ6OGDIyR0uUhUsoO3vfDmmgOezH0mZN59x7MBi++WDL1g/eEiU3avlidO671bkLfwbw5XV2P8Pzo0ydy4t2/0eu33xYSOMOD8hTf4CrBtGMSoXfPLchX+J0ruSePw3LZeK0juPJbYzrhkH0io7B3k164hiGvawhOKMLkrQLyVpZg8rHFW7E2uHOL888IBPlNZ1FPzstSJM694fWr6RwpvcJK60+0HCILTBzZLFNdtAzJaohze60T8qBzyh5ZuOg5e7uwQppofEmf2++DYvmySqGBuKaicF1blQjhuHdvCIMvp8whTTfZzI7RldpwtSzL+F1+wkdZ2TBOW2gIF88PBTzD/gpeREAMEbxnJcaJHNHrpzji0gQCS6hdkEeYt9DF/2qPcEC8RM28Hwmr3sdNyht00byAut2k3gufWNtgtOEOFGUwcXWNDbdNbpgBGxEvKkOQsxivJx33iow0Vw5S6SVTrpVq11ysA2Rp7gTfPfktc6zhtXBBC+adRLshf6sG2RfHPZ5EAc4sVZ83yCN00Fk/4kggu40ZTvIEm5g24qtU4KjBrx/BTTH8ifVASAG7gKrnWxJDcU7x8X6Ecczhm3o6YicvsLXWfh3Ch1W0k8x0nXF+0fFxgt4phz8QvypiwCCFKMqXCnqXExjq10beH+UUA7+nG6mdG/Pu0f3LgFcGrl2s0kNNjpmoJ9o4B29CMO8dMT4Q5ox8uitF6fqsrJOr8qnwNbRzv6hSnG5wP+64C7h9lp30hKNtKdWjtdkbuPA19nJ7Tz3zR/ibgARbhb4AlhavcBebmTHcFl2fvYEnW0ox9xMxKBS8btJ+KiEbq9zA4RthQXDhPa0T9TEe69gWupwc6uBUphquXgf+/FrIjweHQS4/pduMe5ERUMHUd9xv8ZR98CxkS4F2n3EUrUZ10EYNw7BWm9x1GiPssi3GgiGRDKWRYZfXlON+dfNbM+GgIwYdwAAAAASUVORK5CYII=',
        iconSize: [25, 41],
    });

    yellowIcon = L.icon({
        iconUrl: 'css/images/marker-icon-yellow.png',
        //iconUrl:  'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAApCAYAAADAk4LOAAAR0npUWHRSYXcgcHJvZmlsZSB0eXBlIGV4aWYAAHjarZlpkmM5joT/8xRzBIIbyOOAm9ncYI4/HyhlVlZW1XS32aQyQoqnJy6Aw91BhfM//33Df/Gv5FhCqdrbaC3yr4wykvGix8+/8X5LLO/354/xfU/+fD2kH28kLmWe8+dPte/9xvX6xwd+zCHzz9dD/76T+ncg+Tnw+5d9Zn+9f10k19PnupTvQON8XrTR9delzu9A63vjW8r3p/xc1ufJ/w5/uqBEaVcmyimdLDm+3/2zguw/ko3nxu+UC/dJru9KDu9S+w5GQP60vR/PMf4aoD8Feazv1n6P/s9XvwU/2fd6/i2W7cdA7e/fkPrb9fxzmvTrxPn7KnD5T2+MEe9ftvP9uXf3e89nd1YaEW1fRL1gy49huHES8vw+1ngoP5XX+h6DR48WFynfccXJY8mQRFZukCJbTK6c97xkscSSTlKeU1okyq/1rGmklT1PxR9yk+aRd+4ka6UTcuZy+rkWefOON9+SzsxbuDUJgwkf+cdH+L/e/E8e4V4HgkjsnziBC9aVHNcswzPnv7mLhMj95q2+AP94fNMff8EPUCWD9YW5s0GL8zPErPIHtvLLc+a+yvOnhCTo/g5AiJi7shjJZCA20C9NoqakIsSxkyBj5V4bkwxIrWmzyFRybilo6snn5jMq795UU0t+GW4iEZXKUnIzspGsUir40dLBkNVcS621Va091FGt5VZaba1pc5IzzVq0alPVrkOt51567a1r7310G2lkOLCONnT0MYZZCsZExljG/caVmWaeZdbZps4+x7QFfFZZdbWlq6+xbKedNzSx29bd99h2JByY4pRTTzt6+hnHLli7+ZZbb7t6+x3XfmZNvmX7++M/yJp8s5Zepvw+/Zk1rgbVH0OI00n1nJGxVISMq2cAQCfPWexSSvLMec7iSBRFTSyyem7CFs8YKSxHUr3yM3d/ZO7fyluo/d/KW/pXmQueuv+PzAVS99e8/U3Wtuvcq8NvFXpMY6b6RuqWerC7brxWIn9YrTJa9Jm4uZYZ91xTZc5lZe+pTBFrubEy+jpV11rwn56zW3h40j1UGXFObXm2LN3WMeIobPjEUfnEPetMljZOvayjM/0VArHTYlMpTCsE9EbLZGXIGZXoyDk1KsllBna59BqZYMGp2Rqssy/VNF1W4d2TeSd0abtnYsEsxTRO5WOiZ5R7jKRchlp9EK5y56iocWbINE9pg2WXjoSOlntYEejWenLjEyffnca8rfpol9HmsVktHQZaPe9ZWDNcPf+y0fB2yuwEyPzSe+cFwIX3DGfuexITgNu6yNWAaNi07C24IAXmi1gGBmH/q7R288yAmoj7IFsNMS/1VDsKMsctts2LYvdyS8qe5WQ9vuSbCyQvxhoy1xi797X7mYwHbNomtGJaIMKlSZoSaJnWfaJo5ARonEI6mkgg44TDbu4rkkpi9zxF13bFs1ObkKs9ZyG6pCr3tKlXparbItBrV+sn1cBfZZAeaFlOLcoPe6QoyQJ7pjCpVp3M2jRb5+UUkZbdYa1hqsXayOeGMsZVO32iqY4muTrOWXpa36uN965rKAV4+gJp8XK9r+Pv8c4BFtvmCah0nxYPtmVnismIBkusZBE5FsC206yZwbvqKGpYscONRGKwzSyVIJXTQyvS03pYEJAjdZLcHLXXu+CBZUSrM+U6OqOdPLYMKn+MuRqGBDU7mq7twEVrsUM1Pc1B/cMmsyo6tpKtRRyYSAXUztpnrlTxwSFOuJMMkJtoUOHagenH3nWwYpfHV6c+JuG23G9WsHlmLQYu1x7cTn6vkk2rA0IAN/fo3mEsbVyFL+3UGU+eQ2vLuN+9IrVZTrMD0mrfpHXBIrmtCWozwxmpgEeSzRhubHOf0TbW6diya/gihrBGMBzJXGtMgaTg7VlyA05Aa2RiljeFnnxhO5wO7M6tpjNT46I5ySwVViJtINHKAfda4mklVQdbIiJF8GIKjGIeBtX3GHB35CKfflui5m1IXaWs69YS3q1OiKV7oUXwrJni7QDmkjTyOOwUuwVkhbq2J1Rgf2Jznn5UdmKL4kMdLuRI4Fga8J+g+IDc1qvhXWQfBKUJEChhVy+jqvvO3kgk9yOXjXZF68NiTdABwOM1QCD0FPnu7HhTv2AOswoEMtWPZWSjVfPauTEnt5xD0TSQs8uHHMiH+YjIAc5uVYDhbQYfI1xL+GjAF5nyJwqGvBYEL+P+9iIhRLthiEkmcgXs2jWfKY6bFvMWsnVryiOhpTVAoJ31zrnXsvhhRqMWVi7OirfFsYcVL5Q0+4V60UwQhDrVPk5Gd7UuoUQGuOU/ZWiQOYtMZlsJLBafxGcwKXmmrggmPNb8Ui+OXrJN+U2XSI2hUTCT6vJZD0U3sOVuN/pBjmAtFICqkj0K8PFGZT0eNF8V/H8IJK8nMYLfXMCbgvZzNRnrxYjCrniWMx3wyCtNAoDLpe+zZz6Kts0JVlAeyo+EhQW1AsLTJlm5CbVbEMlGIhVubfAbHxC6l0b6cBejuAkhb4W+AhWp6vxYaqCNfGyNOaLw4ajUDmyGglBcizRR7CzSntxFCudJlrpkuWDjlQAmeQu4ZZvdsrYJTRWyTgHjZHoVosu6rXW0FGqaEMEZ2YWTq8PB2lfb1F/v7QbSGPsGMNgzqrAQ3wVBeWNzkHUHn8sB7dnODETZ5bg/0Gw1rVU6UNHCQEQjbccasoarsDlWR0jJcaJCkXsCXNGzVNDMXBEU2CSOAoixbXfiLuaVHLpm4FIfWAvY6U6SXntpEGjsIYWuCkCwk3AHcfeS2xtpytUzAMgi44fc6wbu0PXG1JFcqIDUwknuQJBaCOYUfBi2EDLBzi00gzgtln7lyTumw8KBZi9ZdCMZ7SJmuBlWrbjJhsID6rtEL9YJdzZWchubgDWrbwlvUvBpw+XIqAodYAcPjMq/kqiUAqV7iXaEpzc4Q4G7+zn08CgwwaplFt5RnLvyxYy6K2ZjEklqy3XwdsIfJD17yM1nYow2hfR5M4oyhbvTREXieSa6TgUbK6IVZsK9MOM1PiDQ8/IX9pqII3PDl5fdFrEKjDYcVzVt2gQ6gekdAJwP+WPXGnBOkDrutNFJH8xgxQ5IpWcu28kbg5lg9IiazgMhuXMxwFYo4E7YNID2UxTO6SgDHKfQI1TgxDhPj5dW3DmS7TJwEugfzD//3OR2eE8ukw8JWNaNmqIGlCmYnIJ38CLY6L8B1N3xo7hVbKmn+kgm9dvHG55viB/klxyAInaPvHkldbqGBjPI82Tu/fEH7mG3VTe++Co1sQZnYwkgABw88glRJDibtmQW7BktFs6vuw92upU1LrSIb8HmkzxqGx/lAkxP8cEAXRRlMg/NkYR1S0vOVZRqnTIWDDdwpwgXdxwcs7g7x5FC8i0exIfbeHMCHhdpOgx1W9NmMj3Xhrd3hAK2FsVZIBIdBkTLUWQmIAXUmgLRmre7tVyQP6/dgX1KOcA+il2J81wWM1tyc0QRYRORvnm3eDKOewPyYyzFVYKStIqa0PAL6kfgAvy9fdS8sDZLFtjHnZMnCABbNOnGiCkq0yvOjcJg2+AJLFFtG9qGi5f3axQ//RGtwKFB2GeWLl57YLzdKlTsRni2N3z5Avkp5OV2LIchn3rwNK1BkSPoXM6Iz4S7r8RF3iLMdTsG4GAHhm6cFVYCStdNcbikXVcipbx2IqexjJBwZ0xDk5YpQreRu9B14hdl4IT5aP5EyBcBavHxkF05CA6UiagkEIBDDwm+xb5GouHtq0pEa5vbLyMbEWb1Pk3zMQgJ19xx93TkJGpfTAOJpF/HhoaK9VXkwhb+5uBgYErwfMpNeGN8IiFEyWl1kTNkloJAwjEaYMubNUwZKewLezyzRN/UoUlwbsEBgeNKS/QQfrElZSNfNGV+rECf42JDg4IPJrlwqp8zoGsJwxOxxFhgZ1s46AK3lJ1aTlasNigWXwJyb3TK+wVFEVoK2DtfouLIrrxGderbPWnCto2PkT44ioTazQmLk7+N06ESKpVQWc7sbMv9GWW5Asumf6vJSSizazz5xknQNzY/OGIX75yg0SEsqsw7SEwS9YihSSuj2pSld5A0G0AL7MFfyCz7RwyxnN5n8BZV4M56VfpGuuPu12irqkRSF5e3jNQJMQ9nuY247qCbdMwCeeV9OqvtScYwWKaxPO6nwUVzk503qk/L7Heh0JRvvKHipSV7I9ISro1BXaxpaFo9EfxcHLxhxbDB4NMPAdBIao6VAWB3A9urqofdsQ69WVPsi6HwbIcEDEhg+/EFHk2jOgSXE6AB6uEa7hLCUNR2oVZHwo30GNETrOR1lOBgW8fIeJDQuwGe6OjQPhqPvjHzrIFZKFNAiXs9bquSrDCTJpgCf8EekFJQ+6J7HVuH/rWflwIollD88+vwyx+4FD+euAQogRkHaPejjQaIBlVmJ/k5FYbCT9vJKp2IHjrvrZICwoDFd4jiYDcqRKbTERhnRAzcOY5AmhKA50Z6KwhwjANSlBzWxis68wa3SzG7Y3VRXBFRfOrYxed28cAU5oJDilDg8o6+0PDANxnynhNxtNTojrgtOmIrzlVoEMAFXANabgPoRLRCnY10ott4JpoLb+ertkaH6RVC04ZnlcDOMZSwCMzNrAuY+TB+3Ev/90GgNMD+A/i0l8mPV1zWJ46Qqf3cMtBzgeUiu+LnCBZuaMMcYFcbvmZmOh5zc9gKMYTcb0zYT1iwvfVgv8y9AVsjKjScNBd5OnppHU39YCC6MGNqHZ4g7tmBFCHOTy/Okj5dOQknWqF4X5+9FGvMZI5GZwzcO76f6tTkbdOM9F4yo58COmaXf+lDp3JKgqtoW7NpgBEn+FuEbC+aS8iW2GJCE9zpfYDwYiGZCyJN7hLcKuO3I0VG+xjfIraEh0S/4V2jf/QDeMFv+4kuzUt2E5yPnzlCr35QqzTL7PZZjRndKYCxBY1g0fyUoGmDdKhvVNQ5G7JlsQppQZn0IE7SyDAgopKwGI62jqUBsn6oF2r/nG2V/AWiN5D33QavUK9eMsB3JSWRi/1HVxWMh5/3YjO9h6gZM0rLhcmkfTA/348UVsIT7FjKSfBXpp6ibj95yUY1JT/Hyq4EqA6djR+V4ndacNqH82mX7KJBKX9q+jgQ2Wqfr/rLpgU3HJF/bcLuoptI+sYqC3LyHjH4F1IA73TnRPBHwfvpzMh0IzlOOthOKp3rq9AM0Dgg71R2XVbcBtLsLRq3HvqJm3Z754ohgCTUzxZ/gK7L7D9B9/szGIAy3tkwJRoaHIrjgIOZDBIBzGCdNmSABCic8vRDGTG6QdxygiXhFtR2Dbe9OCvaNOoTHHW0rGXvoOk5qsOGVrYsB1X2/BF+aB7AUogIvtHvwAMHRw3fEsrhhx8jQIYEjcoiVgM34TJHyN3sJ9xg6n5WhsRhT8kqDgG3BsVVihVOWp5SP3IGR8lrQKgCcuelsa9r8Hy3eqaHW5EaQSAAjJKXH1Ac+k5GHoaz2keJQtDlOPrglEL/xrK3TyPp3wz//XNhS7Tp3v8j4s0ChDLu6530nR6n7F9PkEfpBBOzkQ/WdaTodj95Z42phOYWvRj5GIP2iG5MAh4DgdqYrgHrdvH+EqRU0ItjIGdYVvygHx7T7sOZfphL81ZjoykE2ebfydcd4qC3YB3vaJKZ/VB8g2EiCFMeq1i75MeL2pNws7cFpWARE/pOUcwEwinTgKWB4HeDYj1h/i0mhlPJiVGmu8Pz2XlyKlJKc03lIH5Y1adOfhJxLsCDj7Y+S+NatD4HWN1i/JfPM/v92HncGUyUQyN7tLGuqdUwKFQMUiN0j6SfysN0wZ00689ResWLUYH0jvQe0xLd5up0wKGldScXabqEWsRCk9jkXwxhF6gd1k8vzJYR4pmnOTKeoRtpj2d8MI1134A5P7XjUM7BylXYefr5MYJ4eqGDJ7Ti3xQ0skOLXCaoXyci8tUP+SgqSKEDyIuuUxOtl08f6wf1PzT7Hwv/b56Dv/D+ZLhxkCrXv2U52KBbDutc5mf+VB2d2nX7V4EIHb05fjKEpBApRrWEnwn7xwRdnF6M4X8BuRrl6v1UQBEAAAGEaUNDUElDQyBwcm9maWxlAAB4nH2RPUjDQBzFX1O1IhUHOxRxyFCdLIgWcZQqFsFCaSu06mBy6Rc0aUhSXBwF14KDH4tVBxdnXR1cBUHwA8TNzUnRRUr8X1JoEePBcT/e3XvcvQOEZpWpZs8koGqWkU7ExVx+VQy8og9hBBGDIDFTT2YWs/AcX/fw8fUuyrO8z/05BpWCyQCfSDzHdMMi3iCe2bR0zvvEIVaWFOJz4gmDLkj8yHXZ5TfOJYcFnhkysul54hCxWOpiuYtZ2VCJY8QRRdUoX8i5rHDe4qxW66x9T/7CYEFbyXCd5igSWEISKYiQUUcFVViI0qqRYiJN+3EP/4jjT5FLJlcFjBwLqEGF5PjB/+B3t2ZxespNCsaB3hfb/hgDArtAq2Hb38e23ToB/M/Aldbx15rA7CfpjY4WOQKGtoGL644m7wGXO0D4SZcMyZH8NIViEXg/o2/KA8O3wMCa21t7H6cPQJa6Wr4BDg6B8RJlr3u8u7+7t3/PtPv7AXzvcqtMOlIgAAAABmJLR0QASABIAEiXTr0tAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QYYDQMcp0Q2ngAABsZJREFUWMOtV1tsXFcVXfucc1/z8EwmdpxH2zjgxGmCUilSK4NQKQKBhFQBH/1qQeKDD/6jqgVFUKGKQkG8ykOoUkF8BCGkqj+VKkEUHh8txQ2xYispidt0mmvP2GOPPY/7OudsPmIcjzO2xxL3aulqdM/aa9be5+59L/3lOQx0nJ5Q+4noCEseJkNLrsvz70zrxUG4aqebHzstJojV1wj8FDMfEZIiJaTVZEWWcXDmlFsD44JlfuXqtWx6uzjUz4lXKYjjo8nTJPg5xx/h2LnPa6kxGMpt+ndd5HSIIPkg1ck82Nrv/6eW+06y3La7ijw4IT+iBP/RddyTzfyn8qkcguI2hE0gEUFwCksuDAJY4UFTAR6vodT6aydNszmW/MTVGXt9c0yx+ceJcfl5gGfd3OiZxdLjeQYjn83B0zU4tglhE4AZwiZwbBOeriGf3YS1jMXS43knf/hBo3HlxLj8cl8npyfU/szwTbfyUGlNjcE3tyE4waCHJQ+xPIKCrcI2pjqu4YnpOb7d4yTT/LvKkO+31APIZzcgbASw7QGxBUjeuW6BsBHy2Q10xBEUC2UVk/h9T7ruP+o+6QjzmZr3ac/LQlhm8CZYESBSY+g440jEIXSccURqDFYEveuY4WUhlvxPeK4wkw8cc78OAPT+H9xcFmOpdOBk0BX7oUzv1tdyBBAuRuI30e2sopm6KLspCrkcFv1JGEg4ev4eTs42sFq/Fjk+hkWrpc4GKsOaGoPStZ4UWDGEgFfh195ArfhVNB9r4uiTETqfXcNc5Tzc+kXkTQ1G7OvhKV3DmhpDoDK0WuqssISHizkBZVsAzF2QgBYl5Drv4ObxV3HssZ9h/GAJjhQ4OlzEqU8+g6lTcxDNaVgZACR7+Mq2UMwJWMLDgpkehSoH0nYA8AZIKJTS68gpD5OPfLHvjvrC2WMYKt6Pfck0mIIevrQdQJUDZnpUsMVkKkdBNukporEBPK7jVukbEETbbt3Zyrfg6GUwqR4+2QSpHAVbTKqOdu70CtYgNhtkBkAmReIc2PH5SGUFsBpg9PABDRDQtU4gAFx2TQ0gBd50Cu5CyyJOrPx0R5GTzR+DpQ9h4x4+SME1NbDFv0Vk1d9tvJKBHIB5A8xdtOVHcaPewN9mq30Fpt9fRLU6hZaaANDp4YMcmLiZxiwvCcrs2+Gqk1pyQaTvgiMwJzg4UsK+fxzHpTf/jMzcabCWGW9cnkPx4iiOjrowIMB2eviWXHzYdFOd0dtqNfP/mbZl8BCnYHbAnG1qSE20nGM4fHARhdnP4dJbFXx8uIHLyyUc9mKUR0exLI+BsrC3tZMDySnCdj7oZO6UKsfhQuIc+Fc+nnmk5YzD2pVNyw0oXUBEJaiDkzgzsorMOjgxVEEqS+joNpBWsXWACFFAIZ5li8JUOQ4XBABEWv7wSuhHTHkQelNrGTB2FTapIjUxOighNTGQVmHMCixvKQUApjymPgziRIsXNhqkVuq1RuQnBXMTJHzY9Wa3FdomyEwb2ibQtv8aEj4K5iZWEq81ErRf2xAZblVTY+iXjeZqBCrArjvYKwCARBG3F9sRG7z4v1Es7uYRP5+u7xeeacCVHvqMjF2hhAc/C/HuSlk0bO6Ve8ZvOQ4XrKGXaktLEYn8tinbDnf6XR636p3IGvrRcX2j0XfGN2zue++ulEVg5hE4bk9Bd4PvOgjMPG6tFm3A6Qvbvkgc1zcaxPyD92pJRCK//uANVguggGuhjhh4PsiWWtuKAIBr9IvVtYL1TYicIwdykXNd+CZE2M1nnsle2hrzHpEgW2ox8Pz1eY6I1t1Y3hYAQPAxGyIiw9/d6qKvCADorvpJvRskThai4KodXRQ9CScLsRgFkY3lL/rF6ysyLKqRAX37el1GQvggor61ICIQBZhZcCJiPj8sqtHAIgBglfz1YhS0VHobBU9A23tR8ARUehsridfSjnp5u1jbigy3qqm2dH5m3o+U8KCEhWa5ASUslPAwM+9HAvbZ4VY13bMIANyXX315LXVXKA5RcCVScxdFT4LiEM3UWzoQtH+7U5wdRZLltjWWnp5eKMaudOBKg8xIuNLAEQ6mF4oxWzzb73NhYBEAOFRYu9DVKkQcouLfiVXxLTgK0dUqPFRYu7BbjF1FkuW2JeJnrtZLcaCA/UEHgQKu1MuJJHtuNxcDiQDAmd986U9drd6z0SL25RyknQanRs4NRQuvDsIfSOSDr/yKGTh3pV5OoBPMLA2lDJwb9NtFDLqwEoevp0bOzobMqZGzlTh8/f8usv5Wea6dKdqLiz2LVOLwogB/sxKHF/fC+y8ySqNNXVSdGQAAAABJRU5ErkJggg==',
        iconSize: [25, 41],
    });
    
    noIcon = L.icon({
       iconSize: [0,0]
    });    

//
//    louisIcon = L.icon({
//        iconUrl: 'css/images/layers.png',
//        //iconUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAQAAAADQ4RFAAACf0lEQVR4AY1UM3gkARTePdvdoTxXKc+qTl3aU5U6b2Kbkz3Gtq3Zw6ziLGNPzrYx7946Tr6/ee/XeCQ4D3ykPtL5tHno4n0d/h3+xfuWHGLX81cn7r0iTNzjr7LrlxCqPtkbTQEHeqOrTy4Yyt3VCi/IOB0v7rVC7q45Q3Gr5K6jt+3Gl5nCoDD4MtO+j96Wu8atmhGqcNGHObuf8OM/x3AMx38+4Z2sPqzCxRFK2aF2e5Jol56XTLyggAMTL56XOMoS1W4pOyjUcGGQdZxU6qRh7B9Zp+PfpOFlqt0zyDZckPi1ttmIp03jX8gyJ8a/PG2yutpS/Vol7peZIbZcKBAEEheEIAgFbDkz5H6Zrkm2hVWGiXKiF4Ycw0RWKdtC16Q7qe3X4iOMxruonzegJzWaXFrU9utOSsLUmrc0YjeWYjCW4PDMADElpJSSQ0vQvA1Tm6/JlKnqFs1EGyZiFCqnRZTEJJJiKRYzVYzJck2Rm6P4iH+cmSY0YzimYa8l0EtTODFWhcMIMVqdsI2uiTvKmTisIDHJ3od5GILVhBCarCfVRmo4uTjkhrhzkiBV7SsaqS+TzrzM1qpGGUFt28pIySQHR6h7F6KSwGWm97ay+Z+ZqMcEjEWebE7wxCSQwpkhJqoZA5ivCdZDjJepuJ9IQjGGUmuXJdBFUygxVqVsxFsLMbDe8ZbDYVCGKxs+W080max1hFCarCfV+C1KATwcnvE9gRRuMP2prdbWGowm1KB1y+zwMMENkM755cJ2yPDtqhTI6ED1M/82yIDtC/4j4BijjeObflpO9I9MwXTCsSX8jWAFeHr05WoLTJ5G8IQVS/7vwR6ohirYM7f6HzYpogfS3R2OAAAAAElFTkSuQmCC',
//        iconSize: [25, 41],
//    });

    baseMaps = {
        "OSM": normal,
        "Français": france,
        "Satellite": satellite,
        "Google Plan": roadmap,
     //   "AWMC": awmc,
    };
    
    if(init_GIS_EFA)
    {
        otherLayers = {
            "SIG Délos": vms,
            "SIG Thasos": vms2,
            //        "Add KMS": kms,
            //        "Add JSON": geojson,
        };
        layerControl=L.control.layers(baseMaps, otherLayers).addTo(mymap);
    }
    else
    {
        layerControl=L.control.layers(baseMaps).addTo(mymap);
    }

    markers = createMarkerCluster();
    markerMovable = {}
    markerY = null;
}

function addBaseMapWmsSite(url_wms,layer_name,attribution)
{
    var attribution_string='';
    if(attribution)
    {
        attribution_string='@ <a href="'+attribution+'" target="_blank">'+attribution+'</a>';
    }
    
    if(site_basemap)
    {
        mymap.removeLayer(site_basemap);
        layerControl.removeLayer(site_basemap);
    }
    if(url_wms&&layer_name&&url_wms.length&&layer_name.length)
    {      
        site_basemap = L.tileLayer.wms(url_wms, {
            id: layer_name,
            layers: layer_name,
            maxZoom: 40,
            /*BGCOLOR: '0xE6F9FF',*/
            attribution:attribution_string
        });   
        site_basemap.addTo(mymap); 
        mymap.setMaxZoom(40);
        mymap.attributionControl.removeAttribution(open_streetmap_attribution);
        //mymap.attributionControl.setPrefix(false);
        layerControl.addBaseLayer(site_basemap, layer_name);
    }
}

function addEditMarker(lat, long, fiche) {
    //lat=null, long=null,  fiche=false
    if (markerY !== null) {
        // the variable is defined
        mymap.removeLayer(markerMovable);
    }

    markerY = L.marker([lat, long], {
        draggable: true
    });
    if (!fiche) {
        $('[id^=toponymes_lat_]').val(lat);
        $('[id^=toponymes_long_]').val(long);
    } else {
        $('[id^=fiches_lat_]').val(lat);
        $('[id^=fiches_lng_]').val(long);
    }
    markerY.on('drag', function (e) {
        lat = markerY.getLatLng().lat.toFixed(6);
        long = markerY.getLatLng().lng.toFixed(6);

        if (!fiche) {
            $('[id^=toponymes_lat_]').val(lat);
            $('[id^=toponymes_long_]').val(long);
        } else {
            $('[id^=fiches_lat_]').val(lat);
            $('[id^=fiches_lng_]').val(long);
        }
    });
    markerY.setIcon(yellowIcon);
    markerY.setZIndexOffset(256);
    markerMovable = markerY;
    mymap.addLayer(markerMovable);
    mymap.flyTo([lat, long], mymap.getZoom());
}

function editMarkerLat(lat, long, fiche) {
    //lat, long, fiche=false
    lat = parseFloat(lat).toFixed(6);
    long = parseFloat(long).toFixed(6);
    if (long <= 180 && long >= -180 && lat <= 90 && lat >= -90) {
        markers._featureGroup.eachLayer(function (x) {
            longi = x.getLatLng().lng;
        });
        markers.clearLayers();
        var marker = L.marker([lat, longi], {
            draggable: true
        });
        marker.setIcon(yellowIcon);
        marker.on('drag', function (e) {
            lat = marker.getLatLng().lat.toFixed(6);
            long = marker.getLatLng().lng.toFixed(6);
            if (!fiche) {
                $('[id^=toponymes_lat_]').val(lat);
                $('[id^=toponymes_long_]').val(long);
            } else {
                $('[id^=fiches_lat_]').val(lat);
                $('[id^=fiches_lng_]').val(long);
            }
        });
        markers.addLayer(marker);
        mymap.addLayer(markers);
        mymap.flyTo([lat, longi], 16);
    } else {
        $.notify({
            title: "Erreur : ",
            message: "Longitude ou Latitude inccorect !",
            icon: 'fa fa-times-circle'
        }, {
            type: "danger"
        });
    }

}

function editMarkerLong(long, lat, fiche) {
    //long, lat, fiche=false
    lat = parseFloat(lat).toFixed(6);
    long = parseFloat(long).toFixed(6);
    if (long <= 180 && long >= -180 && lat <= 90 && lat >= -90) {
        markers._featureGroup.eachLayer(function (x) {
            lati = x.getLatLng().lat;
        });
        markers.clearLayers();
        var marker = L.marker([lati, long], {
            draggable: true
        });
        marker.setIcon(yellowIcon);
        marker.on('drag', function (e) {
            lat = marker.getLatLng().lat.toFixed(6);
            long = marker.getLatLng().lng.toFixed(6);
            if (!fiche) {
                $('[id^=toponymes_lat_]').val(lat);
                $('[id^=toponymes_long_]').val(long);
            } else {
                $('[id^=fiches_lat_]').val(lat);
                $('[id^=fiches_lng_]').val(long);
            }
        });
        markers.addLayer(marker);
        mymap.addLayer(markers);
        mymap.flyTo([lati, long], 16);
    } else {
        $.notify({
            title: "Erreur : ",
            message: "Longitude ou Latitude inccorect !",
            icon: 'fa fa-times-circle'
        }, {
            type: "danger"
        });
    }
}

function resetLatLng(lat, lng, fiche) {
    //lat,lng, fiche=false
    markers.clearLayers();
    mymap.removeLayer(markerMovable);
    var marker = L.marker([lat, lng], {
        draggable: true
    });
    marker.setIcon(yellowIcon);
    marker.on('drag', function (e) {
        lat = marker.getLatLng().lat.toFixed(6);
        lng = marker.getLatLng().lng.toFixed(6);
        if (!fiche) {
            $('[id^=toponymes_lat_]').val(lat);
            $('[id^=toponymes_long_]').val(lng);
        } else {
            $('[id^=fiches_lat_]').val(lat);
            $('[id^=fiches_lng_]').val(lng);
        }
    });
    markerMovable = marker;
    markers.addLayer(markerMovable);
    mymap.addLayer(markers);
    if (!fiche) {
        $('[id^=toponymes_lat_]').val(lat);
        $('[id^=toponymes_long_]').val(lng);
    } else {
        $('[id^=fiches_lat_]').val(lat);
        $('[id^=fiches_lng_]').val(lng);
    }
    mymap.flyTo([lat, lng], 16);
}

function addMarkerEditFiche(topo_id, topo_name, topo_lat, topo_lng) {
    markerLocation = new L.LatLng(topo_lat, topo_lng);
    marker = new L.Marker(markerLocation);
    popupText = topo_name;
    marker.bindPopup(popupText).openPopup();
    marker.properties = {};
    marker.setIcon(blueIcon);
    marker.properties.id = 27;
    marker.on("click", function (event) {
        addEditMarker(topo_lat, topo_lng, true);
        $("#fiches_fk_id_toponymes_" + topo_id).val(topo_id);
        $.ajax({
            url: "action.php?kroute=groupe_fiche",
            dataType: "JSON",
            data: {toponyme: $("#fiches_fk_id_toponymes_" + topo_id).val()},
            success: function (data) {
                $(".linksupr").remove();
                $("#groupe_ope")
                        .find("option")
                        .remove()
                        .end();
                $(".badge-info").remove();
                var jsonData = JSON.parse(data);
                var affAr = [];
                $.each(jsonData, function (index, value) {
                    affAr.push(value);
                    $("#groupe_ope").append("<option value='" + index + "'>" + value + "</option>");
                });
                if (topo_id != 0) {
                    $("#groupe_ope").append("<option value='new'>" + topo_id + "</option>");
                } else {
                    $("#groupe_ope").append("<option value='new'>NEW</option>");
                }
                if (affAr.length > 0) {
                    var res = affAr[0].split(" ");
                    $.each(res, function (index, value) {
                        if (value.length > 0) {
                            $(".grpInfo").append("<a class=\"badge badge-info\" style=\"margin-left:5px\" href='index.php?kroute=edit_fiches&id=" + value + "'> " + value + " </a>");
                        }
                    });
                }
            }

        });
        $.each(markersTab, function (key, value) {
            value.setIcon(blueIcon);
        });
        ;
        this.setIcon(redIcon);
        $("input[id^=toponymes_full_name_]").val(topo_name);
        oTable.search(topo_name).draw();
        $("input[id^=selectedToponyme_]").val(topo_name);
        $("input[test^=zimm]").attr("id", "selectedToponyme_" + topo_id);
        $("input[test^=zimm]").attr("name", "selectedToponyme_" + topo_id);
        $(".selectedTopo").removeClass("selectedTopo");
        $("tr").removeClass("table-info");
        $("#Table_editFiche_searchTopo tbody").children().each(function () {
            if ($(this).attr("id") == topo_id) {
                $(this).addClass("selectedTopo");
            }
        });
        $(".selectedTopo").addClass("table-info");
    });
    markersAll.addLayer(marker);
    markersTab.push(marker);
}


function clickZoom(e) {
    mymap.setView(e.target.getLatLng());
}
