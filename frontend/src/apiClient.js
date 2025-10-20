import axios from 'axios';

const apiClient = axios.create({
  // Asegúrate de que esta URL apunte a tu servidor backend.
  // La obtendremos de las variables de entorno de Vite.
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000',
  headers: {
    'Content-Type': 'application/json',
  },
});

// Interceptor: añade token si no expiró y extiende expiración (sliding refresh)
apiClient.interceptors.request.use((config) => {
  try {
    const token = localStorage.getItem('onoxfri_token');
    const expStr = localStorage.getItem('onoxfri_token_exp');
    const exp = Number(expStr || 0);
    const now = Date.now();
    const isExpired = !exp || now > exp;

    if (!token || isExpired) {
      // limpiar si expiró
      localStorage.removeItem('onoxfri_token');
      localStorage.removeItem('onoxfri_user');
      localStorage.removeItem('onoxfri_token_exp');
      // no adjuntar token
      return config;
    }

    // token válido: adjuntar y extender 20 minutos
    config.headers.Authorization = `Bearer ${token}`;
    const newExp = now + 20 * 60 * 1000;
    localStorage.setItem('onoxfri_token_exp', String(newExp));
  } catch {}
  return config;
});

export default apiClient;