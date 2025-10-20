import apiClient from '../apiClient.js';

export const empresaService = {
  async getMyEmpresas() {
    const response = await apiClient.get('/empresas/my.php');
    return response.data;
  },

  async getAllEmpresas() {
    const response = await apiClient.get('/empresas/index.php');
    return response.data;
  },

  async createEmpresa(empresaData) {
    // El backend debería devolver un objeto con { success: true, data: newEmpresa }
    const response = await apiClient.post('/empresas/create.php', empresaData);
    return response.data;
  },

  async updateEmpresa(id, empresaData) {
    const response = await apiClient.put(`/empresas/update.php?id=${id}`, empresaData);
    return response.data;
  },

  async deleteEmpresa(id) {
    const response = await apiClient.delete(`/empresas/delete.php?id=${id}`);
    return response.data;
  }
};