var map = L.map('map').setView([19.4326,-99.1332],13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
maxZoom:19
}).addTo(map);

var marker;

navigator.geolocation.getCurrentPosition(function(position){

var lat = position.coords.latitude;
var lng = position.coords.longitude;

map.setView([lat,lng],15);

marker = L.marker([lat,lng],{draggable:true}).addTo(map);

document.getElementById("latitud").value = lat;
document.getElementById("longitud").value = lng;

marker.on("dragend",function(e){

var pos = marker.getLatLng();

document.getElementById("latitud").value = pos.lat;
document.getElementById("longitud").value = pos.lng;

});

});
