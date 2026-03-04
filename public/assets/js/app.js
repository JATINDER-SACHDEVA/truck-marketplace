async function detectLocation() {
  const input = document.querySelector('[data-location-input]');
  if (!input || input.value) return;
  if (!navigator.geolocation) return;

  navigator.geolocation.getCurrentPosition(async (position) => {
    const { latitude, longitude } = position.coords;
    try {
      const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`;
      const response = await fetch(url);
      const data = await response.json();
      const city = data.address.city || data.address.town || data.address.state_district || '';
      const state = data.address.state || '';
      input.value = [city, state].filter(Boolean).join(', ');
    } catch (e) {
      console.log('Location lookup failed', e);
    }
  });
}

document.addEventListener('DOMContentLoaded', detectLocation);
