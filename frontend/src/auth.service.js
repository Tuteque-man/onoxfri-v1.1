import apiClient from './apiClient'; // Suponiendo que tienes un cliente de API configurado

export const authService = {
  /**
   * Inicia sesión de un usuario.
   * @param {object} credentials - { email, password }
   * @returns {Promise<object>}
   */
  async login(credentials) {
    const response = await apiClient.post('/auth/login', credentials);
    if (response.data.token) {
      localStorage.setItem('onoxfri_token', response.data.token);
      localStorage.setItem('onoxfri_user', JSON.stringify(response.data.user));
    }
    return response.data;
  },

  logout() {
    localStorage.removeItem('onoxfri_token');
    localStorage.removeItem('onoxfri_user');
  }
};