<template>
    <div id="app">
      <ExpensesList />
    </div>
  </template>
  
  <script>
  import ExpensesList from './components/ExpensesList.vue'
  
  export default {
    name: 'App',
    components: {
      ExpensesList
    }
  }
  </script>
  
  <style>
  #app {
    font-family: Arial, sans-serif;
    text-align: center;
  }
  </style>
  