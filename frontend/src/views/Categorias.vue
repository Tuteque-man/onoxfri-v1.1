<template>
  <div class="categorias-container">
    <div class="main-content">
      <div class="page-header">
        <div class="page-title">
          <i class="fas fa-tags"></i>
          Gestión de Categorías
        </div>
      </div>
      <div class="page-header">
        <div class="page-title">
          <i class="fas fa-tags"></i>
          Gestión de Categorías
        </div>
      </div>

      <div class="coming-soon">
        <i class="fas fa-construction"></i>
        <h2>Módulo de Categorías</h2>
        <p>Próximamente disponible.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.categorias-container {
  width: 100%;
  min-height: 100vh;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1.5rem 0;
  border-bottom: 2px solid rgba(75, 0, 130, 0.1);

  .page-title {
    font-family: 'Orbitron', monospace;
    font-size: 2rem;
    color: #4B0082;
    display: flex;
    align-items: center;
    margin: 0;

    i {
      margin-right: 1rem;
    }
  }
}

.coming-soon {
  text-align: center;
  padding: 4rem 2rem;
  color: #666;

  i {
    font-size: 4rem;
    color: #4B0082;
    margin-bottom: 1rem;
  }

  h2 {
    margin-bottom: 1rem;
    color: #4B0082;
    font-family: 'Space Grotesk', sans-serif;
  }

  p {
    font-size: 1.125rem;
  }
}

@media (max-width: 768px) {
  .page-header {
    text-align: center;
  }

  .page-title {
    font-size: 1.75rem;
    justify-content: center;
  }
}
</style>