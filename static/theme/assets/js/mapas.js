class Marcador {
    constructor(nombre, direccion, horarios, lat, lon, tag) {
        this.nombre = nombre;
        this.direccion = direccion;
		this.horarios = horarios;
        this.lat = lat;
        this.lon = lon;
		this.tag = tag;
    }
}

var marcadores = [];
var ventanas = [];
var mapaaa;
var central = new Marcador ("Central Capital","Buenos Aires 71","De 07:00 a 15:00",-29.414429,-66.856407, "central");
marcadores.push(central);
var losfiltros = new Marcador ("Los Filtros","Av. San Francisco Km 2,5","De 07:00 a 15:00",-29.42103,-66.874225, "losfiltros");
marcadores.push(losfiltros);
var cmd = new Marcador ("CMD","Av. 2 de Abril y Gonzalo de Berceo","De 07:00 a 15:00",-29.401607,-66.841953, "cmd");
marcadores.push(cmd);
var faldeo = new Marcador ("Faldeo del Velazco","Principal Rojas y Rosendo Velarte","De 07:00 a 15:00",-29.449095,-66.872757, "faldeo");
marcadores.push(faldeo);
var chamical = new Marcador ("Chamical","Majul Ayan 263","De 07:00 a 15:00",-30.35882,-66.313148, "chamical");
marcadores.push(chamical);
var aimo = new Marcador ("Aimogasta","Santa Rosa 358","De 07:00 a 15:00",-28.560446,-66.809853, "aimo");
marcadores.push(aimo);
var anillaco = new Marcador ("Anillaco","Jujuy 9043","De 07:00 a 15:00",-28.801542,-66.938305, "anillaco");
marcadores.push(anillaco);
var chanar = new Marcador ("Chañar","Belgrano 505","De 07:00 a 15:00",-30.542109,-65.957086, "chanar");
marcadores.push(chanar);
var chepes = new Marcador ("Chepes","Belgrano 91","De 07:00 a 15:00",-31.338194,-66.589116, "chepes");
marcadores.push(chepes);
var chilecito = new Marcador ("Chilecito","1er piso Shoping La Vieja Terminal","De 07:00 a 15:00",-29.163070,-67.495643, "chilecito");
marcadores.push(chilecito);
var campanas = new Marcador ("Campanas","Calle Pública sn.- Bº La Plaza","De 07:00 a 15:00",-28.5542904,-67.6258316, "campanas");
marcadores.push(campanas);
var nonogasta = new Marcador ("Nonogasta","Ruta 76 y Ruta 74","De 07:00 a 15:00",-29.310219,-67.504222, "nonogasta");
marcadores.push(nonogasta);
var jague = new Marcador ("Jague","Pública 9007","De 07:00 a 15:00",-28.661427,-68.389568, "jague");
marcadores.push(jague);
var mazan = new Marcador ("Mazán","Cesar Vallejos 9060","De 07:00 a 15:00",-28.656754,-66.546180, "mazan");
marcadores.push(mazan);
var famatina = new Marcador ("Famatina","9 de Julio 9016","De 07:00 a 15:00",-28.921687,-67.522996, "famatina");
marcadores.push(famatina);
var guandacol = new Marcador ("Guandacol","San Martín 9513","De 07:00 a 15:00",-29.524373,-68.553632, "guandacol");
marcadores.push(guandacol);
var milagro = new Marcador ("Milagro","Calle Rioja s/n esquina Juan Facundo Quiroga","De 07:00 a 15:00",-31.005897,-65.993293, "milagro");
marcadores.push(milagro);
var pituil = new Marcador ("Pituil","Santo Domingo 9516","De 07:00 a 15:00",-28.5761359,-67.4553064, "puil");
marcadores.push(pituil);
var portezuelo = new Marcador ("Portezuelo","Ruta Pcial. 28 9025","De 07:00 a 15:00",-30.836626,-66.704834, "portezuelo");
marcadores.push(portezuelo);
var pllanos = new Marcador ("Punta de los Llanos","Calle 12 9006","De 07:00 a 15:00",-30.1538116,-66.5483566, "pllanos");
marcadores.push(pllanos);
var salicas = new Marcador ("Salicas","Ruta Nac. 40 9115","De 07:00 a 15:00",-28.374227,-67.071183, "salicas");
marcadores.push(salicas);
var vinchina = new Marcador ("Vinchina","Barrio El Alto S/N","De 07:00 a 15:00",-28.752647,-68.2097226, "vinchina");
marcadores.push(vinchina);
var sanagasta = new Marcador ("Sanagasta","Sarmiento S/N","De 07:00 a 15:00",-29.282130,-67.020293, "sanagasta");
marcadores.push(sanagasta);
var ulapes = new Marcador ("Ulapes","Entre San Martín y Juan Facundo Quiroga","De 07:00 a 15:00",-31.5724403,-66.2390027, "ulapes");
marcadores.push(ulapes);
var vcastelli = new Marcador ("Villa Castelli","José de San Martín 9529","De 07:00 a 15:00",-28.5761359,-67.4553064, "vcastelli");
marcadores.push(vcastelli);
var olta = new Marcador ("Olta","José Salinas 455","De 07:00 a 15:00",-30.630417,-66.262144, "olta");
marcadores.push(olta);
var patquia = new Marcador ("Patquia","Av. Rioja 237","De 07:00 a 15:00",-30.045356,-66.885584, "patquia");
marcadores.push(patquia);
var vu = new Marcador ("Villa Unión","Irigoyen 90","De 07:00 a 15:00",-29.319458,-68.227153, "vu");
marcadores.push(vu);

function ir(oficina){
	cerrarVentanas();
	var actual = marcadores.find(marcador => marcador.tag === oficina);
	var ubicacion = new google.maps.LatLng(actual.lat, actual.lon);
	mapaaa.setCenter(ubicacion);
	mapaaa.setZoom(20);
}

function cerrarVentanas() {
	for (var i=0;i<ventanas.length;i++) {
		ventanas[i].close();
	}
}

$(function () {
    function initMap() {
        var location = new google.maps.LatLng(-29.603073, -67.083247);
        var mapCanvas = document.getElementById('map');
        var mapOptions = {
            center: location,
            zoom: 7,
            panControl: false,
            scrollwheel: false,
			mapTypeControl: false,
			zoomControl: true,
			zoomControlOptions: {
              position: google.maps.ControlPosition.RIGHT_CENTER
			},
			scaleControl: true,
			streetViewControl: true,
			streetViewControlOptions: {
              position: google.maps.ControlPosition.LEFT_TOP
			},
			fullscreenControl: true,
			fullscreenControlOptions: {
              position: google.maps.ControlPosition.BOTTOM_LEFT
			},
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
		
        mapaaa = new google.maps.Map(mapCanvas, mapOptions);
		var markerImagen = 'https://www.edelar.com.ar/static/theme/images/marcador_maps.png';
		
		for (var marcador = 0;  marcador < marcadores.length;  marcador++ ) {
			agregarMarcador(marcadores[marcador]);
		}
		
		function agregarMarcador(marcador) {
			var contenidopopup = '<div class="info-window">' +
                '<h3 style="color:#0f1e79;">'+ marcador.nombre + '</h3>' +
                '<div class="info-content"><p>' + marcador.direccion +
                '</p><p>'+ marcador.horarios +'</p></div>' +
                '</div>';
			var infowindow = new google.maps.InfoWindow({
				content: contenidopopup,
				maxWidth: 400
			});
			
			var latlng = new google.maps.LatLng(marcador.lat, marcador.lon);
			var marker = new google.maps.Marker({
				position: latlng,
				title: marcador.nombre,
				icon: markerImagen
			});
			marker.setMap(mapaaa);
			google.maps.event.addListener(marker, 'click', function() {
				cerrarVentanas();
				infowindow.open(mapaaa,marker );
				ventanas.push(infowindow);
			});
		}
        var styles = [
    {
        "featureType": "landscape",
        "elementType": "all",
        "stylers": [
            {
                "saturation": "-100"
            },
            {
                "lightness": "3"
            },
            {
                "visibility": "on"
            },
            {
                "gamma": "0.67"
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "all",
        "stylers": [
            {
                "saturation": -100
            },
            {
                "lightness": 51
            },
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "all",
        "stylers": [
            {
                "saturation": -100
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "all",
        "stylers": [
            {
                "saturation": -100
            },
            {
                "lightness": 30
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "labels.text",
        "stylers": [
            {
                "color": "#322892"
            },
            {
                "weight": "0.97"
            }
        ]
    },
    {
        "featureType": "road.local",
        "elementType": "all",
        "stylers": [
            {
                "saturation": -100
            },
            {
                "lightness": 40
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "road.local",
        "elementType": "labels.text",
        "stylers": [
            {
                "color": "#140e4d"
            },
            {
                "saturation": "62"
            },
            {
                "lightness": "16"
            },
            {
                "weight": "0.52"
            }
        ]
    },
    {
        "featureType": "transit",
        "elementType": "all",
        "stylers": [
            {
                "saturation": -100
            },
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "geometry",
        "stylers": [
            {
                "hue": "#ffff00"
            },
            {
                "lightness": -25
            },
            {
                "saturation": -97
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "lightness": -25
            },
            {
                "saturation": -100
            }
        ]
    }
];

mapaaa.set('styles', styles);
}
google.maps.event.addDomListener(window, 'load', initMap);
});