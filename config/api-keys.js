// Configuração das API Keys
// IMPORTANTE: Substitua as chaves abaixo pelas suas chaves reais do Google Cloud Platform

const API_KEYS = {
    // Google Maps API Key
    // Obtenha sua chave em: https://console.cloud.google.com/
    GOOGLE_MAPS: 'SUA_API_KEY_AQUI'
};

// Verifica se a API key foi configurada
function verificarAPIKey() {
    if (API_KEYS.GOOGLE_MAPS === 'SUA_API_KEY_AQUI' || !API_KEYS.GOOGLE_MAPS) {
        // Usar console.log em vez de console.error, pois é um comportamento esperado
        // O sistema usará automaticamente o mapa gratuito (OpenStreetMap) como fallback
        console.log('ℹ️ Google Maps API Key não configurada - usando mapa gratuito (OpenStreetMap)');
        console.log('📋 Para usar Google Maps, configure a API key:');
        console.log('1. Acesse: https://console.cloud.google.com/');
        console.log('2. Crie um projeto e ative a Maps JavaScript API');
        console.log('3. Gere uma chave de API');
        console.log('4. Edite config/api-keys.js e substitua "SUA_API_KEY_AQUI" pela sua chave real');
        return false;
    }
    return true;
}

// Exporta as configurações
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { API_KEYS, verificarAPIKey };
}
