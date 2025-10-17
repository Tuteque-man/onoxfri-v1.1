// Simple API client using fetch
const baseURL = (typeof window !== 'undefined' && window.__API_BASE__) || 'http://localhost:8000';

function getToken() {
  try {
    return localStorage.getItem('onoxfri_token') || '';
  } catch {
    return '';
  }
}

async function request(path, { method = 'GET', body, auth = false } = {}) {
  const headers = { 'Content-Type': 'application/json' };
  if (auth) {
    const token = getToken();
    if (token) headers['Authorization'] = `Bearer ${token}`;
  }

  const res = await fetch(`${baseURL}${path}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : undefined,
  });

  let data = null;
  const contentType = res.headers.get('content-type') || '';
  if (contentType.includes('application/json')) {
    data = await res.json();
  } else {
    data = await res.text();
  }

  if (!res.ok) {
    const message = (data && data.errors) ? JSON.stringify(data.errors) : (data && data.message) || 'Request error';
    throw new Error(message);
  }

  return data;
}

export const authService = {
  async login(payload) {
    // payload: { email, password, empresa_id? }
    const data = await request('/login', { method: 'POST', body: payload });
    try {
      if (data && data.token) localStorage.setItem('onoxfri_token', data.token);
      if (data && data.user) localStorage.setItem('onoxfri_user', JSON.stringify(data.user));
    } catch {}
    return { success: true, ...data };
  },
  async register(payload) {
    const data = await request('/register', { method: 'POST', body: payload });
    try {
      if (data && data.token) localStorage.setItem('onoxfri_token', data.token);
      if (data && data.user) localStorage.setItem('onoxfri_user', JSON.stringify(data.user));
    } catch {}
    return { success: true, ...data };
  },
  async me() {
    const data = await request('/auth/user', { method: 'POST', auth: true });
    return data;
  }
};

export const empresaService = {
  async getAllEmpresas() {
    return await request('/empresas', { method: 'GET', auth: true });
  },
  async getMyEmpresas() {
    return await request('/empresas/my', { method: 'GET', auth: true });
  },
  async createEmpresa(body) {
    const data = await request('/empresas/create', { method: 'POST', body, auth: true });
    return { success: true, data };
  },
  async updateEmpresa(id, body) {
    const payload = { id, ...body };
    const data = await request('/empresas/update', { method: 'POST', body: payload, auth: true });
    return { success: true, data };
  },
  async deleteEmpresa(id) {
    const data = await request('/empresas/delete', { method: 'POST', body: { id }, auth: true });
    return { success: true, data };
  }
};
