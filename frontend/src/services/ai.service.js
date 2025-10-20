// AI service using either backend proxy or direct OpenAI-compatible API
// .env options (frontend):
// VITE_AI_USE_PROXY=true
// VITE_AI_API_KEY=sk-...
// VITE_AI_API_BASE=https://api.openai.com
// VITE_AI_MODEL=gpt-3.5-turbo

import apiClient from '../apiClient.js'

export const aiService = {
  async chat(messages, model) {
    const useProxy = String(import.meta.env.VITE_AI_USE_PROXY || '').toLowerCase() === 'true'
    const apiKey = import.meta.env.VITE_AI_API_KEY || ''
    const mdl = model || import.meta.env.VITE_AI_MODEL || 'gpt-3.5-turbo'
    const payload = {
      model: mdl,
      messages: messages.map(m => ({ role: m.role, content: m.content })),
      temperature: 0.2,
    }

    // Prefer backend proxy when configured or when no frontend API key
    if (useProxy || !apiKey) {
      const { data } = await apiClient.post('/ai/proxy.php', payload)
      if (!data?.success) throw new Error(data?.message || 'Error en proxy de IA')
      return { content: data.content || '' }
    }

    // Direct call (browser) when an API key is present and proxy disabled
    const base = (import.meta.env.VITE_AI_API_BASE || 'https://api.openai.com').replace(/\/$/, '')
    const resp = await fetch(`${base}/v1/chat/completions`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${apiKey}`,
      },
      body: JSON.stringify(payload),
    })
    if (!resp.ok) {
      const txt = await resp.text().catch(() => '')
      throw new Error(`IA error ${resp.status}: ${txt || resp.statusText}`)
    }
    const data = await resp.json()
    const content = data?.choices?.[0]?.message?.content || ''
    return { content }
  }
}
