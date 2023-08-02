<template>
    <div>
        <h1>Expenses</h1>
        <button @click="showForm = true">Add New Expense</button>
        <ExpenseForm :show="showForm" :expense="selectedExpense" @close="showForm = false" @submit="fetchExpenses" />
        <DeleteExpense :show="showDeleteModal" :expense="selectedExpense" @close="showDeleteModal = false" @submit="fetchExpenses"/>
        <div class="grid">
            <div class="card" v-for="expense in expenses" :key="expense.id">
                <h3>{{ expense.comment }}</h3>
                <p>{{ expense.date }} - {{ expense.sum }}</p>
                <button @click="editExpense(expense)" class="edit">Edit</button>
                <button @click="deleteExpense(expense)" class="delete">delete</button>
            </div>
        </div>
    </div>
</template>
  
<script>
import axios from 'axios';
import ExpenseForm from './ExpenseForm.vue';
import DeleteExpense from './DeleteExpense.vue'
import { useToast } from 'vue-toastification';

export default {
    name: 'ExpensesList',
    components: {
        ExpenseForm,
        DeleteExpense
    },
    data() {
        return {
            showForm: false,
            showDeleteModal:false,
            selectedExpense: null,
            expenses: []
        }
    },
    methods: {
        editExpense(expense) {
            this.selectedExpense = { ...expense };
            this.showForm = true;
        },
        deleteExpense(expense) {
            this.selectedExpense = { ...expense };
            this.showDeleteModal = true;
        },
        async fetchExpenses() {
            const toast = useToast();
            try {
                const response = await axios.get('http://localhost/expense');
                this.expenses = response.data;
            } catch (error) {
                toast.error(error);
            }
        }
    },
    created() {
        this.fetchExpenses();
    }
}
</script>
<style scoped>
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    grid-gap: 20px;
    padding: 20px;
}

.card {
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 20px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}
.edit, .delete{
    width: 50%;
}
.edit{
    float: left;
}
.delete{
    float: right;
    
}
</style>
  