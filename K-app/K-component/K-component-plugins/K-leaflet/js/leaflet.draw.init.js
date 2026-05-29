
let drawnItems=null;
let fieldId=null;
let singleMarkerOnlyLeafLet=false;
let debugLeafLet=true;

let myStyle = new Map();
//myStyle.set(0, {"color": "#229cf2","weight":"5","opacity":"0.2"});
myStyle.set(1, {"color": "#229cf2","weight":5,"opacity":0.2});
myStyle.set(2, {"color": "#229cf2","weight":5,"opacity":0.2});
myStyle.set(3, {"color": "#229cf2","weight":5,"opacity":0.2});
myStyle.set(4, {"color": "#229cf2","weight":5,"opacity":0.2});
myStyle.set(5, {"color": "#229cf2","weight":5,"opacity":0.2});
myStyle.set(6, {"color": "#229cf2","weight":5,"opacity":0.2});


let selectedStyle = {"color": " #f1ae3d","weight":8,"opacity":0.8};

/* Leaflet Draw to draw map elements */
function initMapDrawForm(json, jsonCenter, fieldName, edit, geo_color, geo_weight, geo_opacity, singleMarkerOnly) 
{
    singleMarkerOnlyLeafLet=singleMarkerOnly;
    fieldId = '#' + fieldName;
    drawnItems = new L.FeatureGroup();
    mymap.addLayer(drawnItems);
  
    loadJSON(json,jsonCenter,geo_color, geo_weight, geo_opacity);
  
    if (edit) 
    {
        if (singleMarkerOnlyLeafLet) 
        {
            mymap.pm.addControls({  
                position: 'topleft',  
                drawCircleMarker: false,
                rotateMode: false,
                drawText : false,
                drawPolyline: false,
                drawCircle : false,
                drawRectangle : false,
                cutPolygon : false,
                drawPolygon:false,
                editMode: false,
              });          
        }
        else
        {
            mymap.pm.addControls({  
                position: 'topleft',  
                drawCircleMarker: false,
                rotateMode: false,
                drawText : false,
                drawPolyline: false,
                drawCircle : false,
              });  
          }
        mymap.pm.setLang("fr");  
        mymap.pm.setGlobalOptions({layerGroup:drawnItems});

        drawnItems.on("pm:edit", (e) => {
            updateDrawnItems();
            leafDebug("=> drawnItems edit");
            leafDebug(e);
        });
        drawnItems.on("pm:update", (e) => {
            updateDrawnItems();
            leafDebug("=> drawnItems update");
            leafDebug(e);
        }); 
        //On change called to often
    //    drawnItems.on("pm:change", (e) => 
    //    {
    //        updateDrawnItems();
    //        leafDebug("=> drawnItems change");
    //        leafDebug(e);
    //    });     
    //    

        mymap.on("pm:edit", (e) => {
          leafDebug(e);
          leafDebug("pm:edit");
          updateDrawnItems();
        });
        mymap.on("pm:drawstart", (e) => {
            leafDebug("pm:drawstart");
        });
        mymap.on("pm:drawend", (e) => {
            leafDebug("pm:drawend");
            leafDebug(e);
            updateDrawnItems();  
        });
        mymap.on("pm:create", (e) => {

            leafDebug("pm:create");
            leafDebug(e);
            if(singleMarkerOnlyLeafLet)
            {
                // removeothers markers
                drawnItems.eachLayer(layer => {
                    if(e.layer!=layer)
                    {
                        mymap.removeLayer(layer);
                    }
                });            
            }
        });
        mymap.on("pm:globaldrawmodetoggled", (e) => {
            leafDebug("pm:globaldrawmodetoggled");
            leafDebug(e);
        });  

        mymap.on("pm:globalremovalmodetoggled", (e) => {
            leafDebug("pm:globalremovalmodetoggled");
            leafDebug(e);    
            if(!e.enabled)
            { 
                updateDrawnItems();
            }
        }); 

        mymap.on("pm:globaleditmodetoggled", (e) => {
            leafDebug("pm:globaleditmodetoggled");
            leafDebug(e);
            updateDrawnItems();     
        });    

         mymap.on("pm:remove", (e) => 
        {
            leafDebug("pm:remove");
            leafDebug(e);   
        });
        
    }

}

function updateDrawnItems()
{
    let count=0;
    drawnItems = L.featureGroup();
    mymap.eachLayer((layer) => {
        if ((layer instanceof L.Polygon || layer instanceof L.Marker) && layer.pm)
        {
            drawnItems.addLayer(layer);
            count++;
        }
    });
    if(count)
    {
        let geoString = drawnItems.toGeoJSON();
        $(fieldId).val(JSON.stringify(geoString));
    }
    else
    {
        $(fieldId).val('');
    }
}

function leafDebug(debugString)
{
    if(debugLeafLet)
    {
        console.log(debugString);
    }
}

function loadJSON(json,jsonCenter,geo_color, geo_weight, geo_opacity)
{
    if(json!='')
    {
        try {
            let JSONObject = JSON.parse(json);
            let site= null;
            if (JSONObject.type != 'Point')
            {
                site = L.geoJSON(JSONObject, {
                    onEachFeature: function(feature, layer) {
                        drawnItems.addLayer(layer);
                        var myStyle = {
                            "color": String(geo_color),
                            "weight": Number(geo_weight),
                            "opacity": parseFloat(geo_opacity)
                        };    
                        layer.setStyle(myStyle);
                    },
                    pmIgnore: false
                });

                let JSONCenterObject = JSON.parse(jsonCenter);
                let site_center = L.geoJSON(JSONCenterObject, {
                    pointToLayer: function(feature, latlng) {
                        mymap.setZoom(15);
                        mymap.panTo(latlng);
                    }
                });
                //console.log(site.getBounds());
                mymap.fitBounds(site.getBounds());  
            }
            else if(JSONObject.type == 'Point')
            {
                site = L.geoJSON(JSONObject, {
                    onEachFeature: function(feature, layer) {
                        drawnItems.addLayer(layer);   
                    },
                    pmIgnore: false
                }); 
                let JSONCenterObject = JSON.parse(jsonCenter);
                let site_center = L.geoJSON(JSONCenterObject, {
                    pointToLayer: function(feature, latlng) {
                        mymap.panTo(latlng);
                    }
                });
                let zoom=mymap.getZoom();
                mymap.fitBounds(site.getBounds()); 
                mymap.setZoom(zoom+2);

            }            
        } 
        catch(e) 
        {          
            leafDebug(e);
        }
    }
}

function loadJSONSecteur(json,jsonCenter,geo_color, geo_weight, geo_opacity)
{
    if(json!='')
    {
        try {
            let JSONObject = JSON.parse(json);

            let site= null;
            if (JSONObject.type != 'Point')
            {
                site = L.geoJSON(JSONObject, {
                    onEachFeature: function(feature, layer) {
                        drawnItems.addLayer(layer);
                        var myStyle = {
                            "color": String(geo_color),
                            "weight": Number(geo_weight),
                            "opacity": parseFloat(geo_opacity)
                        };      
                        layer.setStyle(myStyle);
                    },
                    pmIgnore: false
                });
  
            }
            else if(JSONObject.type == 'Point')
            {
                site = L.geoJSON(JSONObject, {
                    onEachFeature: function(feature, layer) {
                        drawnItems.addLayer(layer);   
                    },
                    pmIgnore: false
                }); 

            }            
        } catch(e) 
        {
            leafDebug(e);
            console.log(e);
        }
    }
}

function zoomToSector(sectorId) {
    const sector = getSectorData(sectorId);

    if (sector && sector.coordinates) {
        const bounds = L.polygon(sector.coordinates).getBounds();
        map.fitBounds(bounds);
    }
}