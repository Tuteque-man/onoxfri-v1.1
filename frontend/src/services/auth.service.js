import apiClient from '../apiClient.js';

export const authService = {

  /**
   * Inicia sesión de un usuario.
   * @param {object} credentials - { email, password }
   * @returns {Promise<object>}
   */
  async login(credentials) {
    const response = await apiClient.post('/auth/login.php', credentials);
    if (response.data.token) {
      localStorage.setItem('onoxfri_token', response.data.token);
      localStorage.setItem('onoxfri_user', JSON.stringify(response.data.user));
      // Expiración local: 20 minutos
      const expiresAt = Date.now() + 20 * 60 * 1000;
      localStorage.setItem('onoxfri_token_exp', String(expiresAt));
    }
    return response.data;
  },

  /**
   * Registra un usuario.
   * @param {object} payload - { email, password, roleId, nombre, apellido, nombre_usuario, nombre_empresa }
   */
  async register(payload) {
    const response = await apiClient.post('/auth/register.php', payload);
    return response.data;
  },

  /**
   * Cambia la contraseña del usuario autenticado.
   * Requiere token (se envía por interceptor Authorization).
   * @param {{ currentPassword: string, newPassword: string }} payload
   */
  async changePassword(payload) {
    const response = await apiClient.put('/auth/change-password.php', payload);
    return response.data;
  },

  /**
   * Solicita un email de recuperación de contraseña.
   * @param {{ email: string }} payload
   */
  async requestPasswordReset(payload) {
    const response = await apiClient.post('/auth/request-password-reset.php', payload);
    return response.data;
  },

  /**
   * Resetea contraseña con un token de recuperación.
   * @param {{ token: string, newPassword: string }} payload
   */
  async resetPassword(payload) {
    const response = await apiClient.post('/auth/reset-password.php', payload);
    return response.data;
  },

  logout() {
    localStorage.removeItem('onoxfri_token');
    localStorage.removeItem('onoxfri_user');
    localStorage.removeItem('onoxfri_token_exp');
  }
};