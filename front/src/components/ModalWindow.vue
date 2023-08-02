<template>
    <div v-if="show" class="modal-overlay" @click.self="$emit('close')">
      <div class="modal">
        <slot></slot>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'ModalWindow',
    props: ['show']
  }
  </script>
  
  <style scoped>
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .modal {
    min-width: 400px;
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
  }
  </style>