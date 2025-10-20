import apiClient from './apiClient';

export const empresaService = {
  async getMyEmpresas() {
    const response = await apiClient.get('/empresas/my');
    return response.data;
  },

  async getAllEmpresas() {
    const response = await apiClient.get('/empresas');
    return response.data;
  },

  async createEmpresa(empresaData) {
    // El backend debería devolver un objeto con { success: true, data: newEmpresa }
    const response = await apiClient.post('/empresas', empresaData);
    return response.data;
  },

  async updateEmpresa(id, empresaData) {
    const response = await apiClient.put(`/empresas/${id}`, empresaData);
    return response.data;
  },

  async deleteEmpresa(id) {
    const response = await apiClient.delete(`/empresas/${id}`);
    return response.data;
  }
};