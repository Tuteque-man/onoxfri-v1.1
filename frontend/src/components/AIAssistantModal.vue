<template>
  <transition name="modal-fade">
    <div v-if="visible" class="ai-modal-backdrop" @click.self="close">
      <div class="ai-modal-card" role="dialog" aria-modal="true" aria-label="Asistente IA">
        <header class="ai-modal-header">
          <i class="fas fa-robot"></i>
          <h3>Asistente IA</h3>
          <button class="ai-close" @click="close" aria-label="Cerrar">
            <i class="fas fa-times"></i>
          </button>
        </header>

        <section class="ai-chat" ref="chatBody">
          <div v-if="messages.length === 0" class="ai-empty">¿En qué te ayudo hoy?</div>
          <div v-for="(m, idx) in messages" :key="idx" class="ai-msg" :class="m.role">
            <div class="bubble">
              <p>{{ m.content }}</p>
            </div>
          </div>
          <div v-if="loading" class="ai-typing">
            <span></span><span></span><span></span>
          </div>
        </section>

        <footer class="ai-input">
          <textarea v-model="input" :disabled="loading" placeholder="Escribe tu consulta..." @keydown.enter.exact.prevent="send" />
          <button class="btn-futuristic" :disabled="loading || !input.trim()" @click="send">
            <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
            <span v-if="!loading">Enviar</span>
          </button>
        </footer>
        <p v-if="error" class="ai-error">{{ error }}</p>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue'
import { aiService } from '@/services/ai.service.js'

const props = defineProps({
  model: { type: String, default: 'gpt-3.5-turbo' },
  visible: { type: Boolean, default: false },
})
const emit = defineEmits(['close'])

const input = ref('')
const messages = ref([]) // { role: 'user'|'assistant', content }
const loading = ref(false)
const error = ref('')
const chatBody = ref(null)

watch(() => props.visible, (v) => {
  if (v) nextTick(scrollToEnd)
})

const close = () => emit('close')

const scrollToEnd = () => {
  try { chatBody.value?.scrollTo({ top: chatBody.value.scrollHeight, behavior: 'smooth' }) } catch {}
}

const send = async () => {
  const text = input.value.trim()
  if (!text) return
  error.value = ''
  messages.value.push({ role: 'user', content: text })
  input.value = ''
  loading.value = true
  await nextTick(scrollToEnd)
  try {
    const resp = await aiService.chat([
      ...messages.value.map(m => ({ role: m.role, content: m.content }))
    ], props.model)
    const assistantText = resp?.content || resp?.text || ''
    messages.value.push({ role: 'assistant', content: assistantText || '(sin respuesta)' })
  } catch (e) {
    error.value = e?.message || 'No se pudo obtener respuesta de la IA'
  } finally {
    loading.value = false
    await nextTick(scrollToEnd)
  }
}
</script>

<style scoped>
.ai-modal-backdrop {
  position: fixed; inset: 0; background: rgba(0,0,0,0.55); display: flex; align-items: center; justify-content: center; z-index: 2000;
}
.ai-modal-card {
  width: min(92vw, 780px); height: min(80vh, 720px);
  background: var(--bg-primary, #0a0a0a);
  border: 1px solid rgba(138,43,226,0.35); border-radius: 16px; box-shadow: 0 25px 60px rgba(0,0,0,0.45);
  display: grid; grid-template-rows: auto 1fr auto; overflow: hidden;
}
.ai-modal-header { display:flex; align-items:center; gap:10px; padding: 12px 14px; border-bottom: 1px solid rgba(138,43,226,0.25); background: linear-gradient(135deg, rgba(75,0,130,0.08), rgba(138,43,226,0.08)); }
.ai-modal-header h3 { margin: 0; font-family: 'Space Grotesk', sans-serif; color: var(--text-primary, #fff); }
.ai-close { margin-left: auto; background: transparent; color: var(--text-primary, #fff); border: none; cursor: pointer; padding: 6px; border-radius: 8px; }
.ai-close:hover { background: rgba(255,255,255,0.08); }

.ai-chat { padding: 14px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; }
.ai-empty { color: var(--text-secondary, #ccc); text-align: center; padding: 16px; }
.ai-msg { display: flex; }
.ai-msg.user { justify-content: flex-end; }
.ai-msg.assistant { justify-content: flex-start; }
.ai-msg .bubble { max-width: 75%; padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(138,43,226,0.25); }
.ai-msg.user .bubble { background: linear-gradient(135deg, rgba(75,0,130,0.25), rgba(138,43,226,0.25)); color: #fff; }
.ai-msg.assistant .bubble { background: rgba(255,255,255,0.06); color: #fff; }

.ai-typing { display: flex; gap: 6px; padding: 6px 12px; }
.ai-typing span { width: 8px; height: 8px; background: #bbb; border-radius: 50%; animation: blink 1s infinite ease-in-out; opacity: 0.6; }
.ai-typing span:nth-child(2){ animation-delay: .2s }
.ai-typing span:nth-child(3){ animation-delay: .4s }
@keyframes blink { 0%, 100% { transform: translateY(0); opacity: .6 } 50% { transform: translateY(-3px); opacity: 1 } }

.ai-input { display: grid; grid-template-columns: 1fr auto; gap: 10px; padding: 12px; border-top: 1px solid rgba(138,43,226,0.25); background: rgba(255,255,255,0.02); }
.ai-input textarea { resize: none; height: 46px; border-radius: 10px; border: 1px solid rgba(138,43,226,0.25); background: rgba(255,255,255,0.04); color: #fff; padding: 10px; font-family: inherit; }
.ai-input textarea:disabled { opacity: .6 }
.ai-input .btn-futuristic { display: inline-flex; align-items: center; gap: 8px; }

.ai-error { color: #ff667a; padding: 0 12px 12px; margin: 0; font-size: .9rem; }

/* transition */
.modal-fade-enter-active,.modal-fade-leave-active { transition: opacity .2s ease }
.modal-fade-enter-from,.modal-fade-leave-to { opacity: 0 }
</style>
