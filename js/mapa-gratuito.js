// Mapa Gratuito - OpenStreetMap com Leaflet
// Este arquivo fornece funções auxiliares para o mapa gratuito usado no passageiro.html

let map;
let userMarker;
let routingControl;
let userLocation = null;
let mapaInicializado = false;

// Coordenadas de Fraiburgo (Localização específica) para fallback
const FRAIBURGO_LAT = -27.030235623197072;
const FRAIBURGO_LNG = -50.91776127553039;

// Função para inicializar o mapa (chamada pelo passageiro.html)
window.initMapaGratuito = function() {
    // Evitar inicialização duplicada
    if (mapaInicializado) {
        return;
    }
    
    const container = document.getElementById('mapaContainer');
    if (!container) {
        console.error('Container do mapa não encontrado');
        return;
    }
    
    // Limpar container e criar div para o mapa
    container.innerHTML = '<div id="mapaGratuito" style="width: 100%; height: 100%; border-radius: 8px;"></div>';
    
    mapaInicializado = true;
    
    // Inicia o mapa
    map = L.map('mapaGratuito').setView([FRAIBURGO_LAT, FRAIBURGO_LNG], 14);

    // Adiciona o visual do OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Usa a localização específica fornecida
    userLocation = { lat: FRAIBURGO_LAT, lng: FRAIBURGO_LNG };
    
    console.log("📍 Usando localização específica:", userLocation);
    
    // Centraliza no usuário com zoom adequado
    map.setView([userLocation.lat, userLocation.lng], 15);

    // Remove marcador anterior se existir
    if (userMarker) {
        map.removeLayer(userMarker);
        userMarker = null;
    }
    
    // Cria marcador com ícone personalizado (azul) bem visível na localização específica
    userMarker = L.marker([userLocation.lat, userLocation.lng], {
        icon: L.divIcon({
            className: 'user-location-marker',
            html: `<div style="background-color: #3b82f6; width: 32px; height: 32px; border-radius: 50%; border: 4px solid white; box-shadow: 0 3px 6px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; font-weight: bold;">📍</div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        })
    })
        .addTo(map)
        .bindPopup(`<b>📍 Você está aqui</b><br><small>Localização específica</small><br><small style="font-size: 10px;">Lat: ${userLocation.lat.toFixed(6)}<br>Lng: ${userLocation.lng.toFixed(6)}</small>`)
        .openPopup();
    
    // Retornar o mapa para uso externo
    return map;
};

// Função para traçar rota (usada pelo passageiro.html)
window.traçarRota = function(destLat, destLng) {
    if (!map) {
        console.error('Mapa não inicializado');
        return;
    }

    // Se não tiver localização, tentar obter novamente
    if (!userLocation) {
        console.warn('Localização do usuário não disponível, tentando obter...');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    console.log('📍 Localização obtida:', userLocation);
                    // Recursivamente chamar a função após obter a localização
                    window.traçarRota(destLat, destLng);
                },
                (error) => {
                    console.error('Erro ao obter localização:', error);
                    alert("Não foi possível obter sua localização. Usando localização padrão.");
                    userLocation = { lat: FRAIBURGO_LAT, lng: FRAIBURGO_LNG };
                    // Continuar com a localização padrão
                    window.traçarRota(destLat, destLng);
                },
                { enableHighAccuracy: true, timeout: 5000 }
            );
            return;
        } else {
            alert("Aguardando sua localização...");
            return;
        }
    }

    // Se já existe uma rota desenhada, remove ela antes de criar a nova
    if (routingControl) {
        map.removeControl(routingControl);
        routingControl = null;
    }

    // Verifica se Leaflet Routing Machine está disponível
    if (typeof L.Routing === 'undefined') {
        console.error('Leaflet Routing Machine não está carregado');
        alert('Erro: Plugin de rotas não está disponível. Recarregue a página.');
        return;
    }

    // Cria a rota usando OSRM (Serviço Gratuito de Rotas)
    routingControl = L.Routing.control({
        waypoints: [
            L.latLng(userLocation.lat, userLocation.lng), // Início: Você
            L.latLng(destLat, destLng)                    // Fim: Ponto clicado
        ],
        routeWhileDragging: false,
        language: 'pt', // Instruções em português
        show: false, // Não mostra o painel de instruções (o passageiro.html tem seu próprio painel)
        lineOptions: {
            styles: [{ color: '#3b82f6', opacity: 0.8, weight: 6 }] // Linha azul
        },
        createMarker: function() { return null; } // Não cria novos marcadores
    }).addTo(map);
    
    // Fecha o popup para ver a rota melhor
    if (map && map.closePopup) {
        map.closePopup();
    }
    
    return routingControl;
};

// Função para limpar rota (compatibilidade com o HTML)
window.limparRota = function() {
    if (routingControl && map) {
        map.removeControl(routingControl);
        routingControl = null;
    }
};

// Função para obter a localização do usuário
window.obterLocalizacaoUsuario = function() {
    return userLocation;
};

// Função para obter a instância do mapa
window.obterMapa = function() {
    return map;
};

// Função para adicionar marcador no mapa
window.adicionarMarcador = function(lat, lng, popupContent) {
    if (!map) {
        console.error('Mapa não inicializado');
        return null;
    }
    
    const marker = L.marker([lat, lng]).addTo(map);
    if (popupContent) {
        marker.bindPopup(popupContent);
    }
    return marker;
};

// Função para remover todos os marcadores (exceto o do usuário)
window.limparMarcadores = function() {
    if (!map) return;
    
    map.eachLayer(function(layer) {
        if (layer instanceof L.Marker && layer !== userMarker) {
            map.removeLayer(layer);
        }
    });
};

// Função para centralizar o mapa em uma localização
window.centralizarMapa = function(lat, lng, zoom = 15) {
    if (map) {
        map.setView([lat, lng], zoom);
    }
};
