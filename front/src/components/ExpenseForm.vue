<template>
    <ModalWindow @close="closeModal()">
        <h2>{{ expenseData && expenseData.id ? 'Edit' : 'Add' }} Expense</h2>
        <form @submit.prevent="submit">
            <label for="comment">Comment:</label>
            <input id="comment" v-model="expenseData.comment" required>

            <label for="date">Date:</label>
            <input id="date" v-model="expenseData.date" type="datetime-local" required>

            <label for="sum">Sum:</label>
            <input id="sum" v-model="expenseData.sum" type="number" required>

            <button type="submit">Submit</button>
        </form>
    </ModalWindow>
</template>
  
<script>
import axios from 'axios';
import ModalWindow from './ModalWindow.vue';
import { useToast } from 'vue-toastification';

export default {
    name: 'ExpenseForm',
    components: {
        ModalWindow
    },
    props: ['expense'],
    data() {
        return {
            expenseData: this.expense || {
                comment: '',
                date: '',
                sum: ''
            },
        }
    },
    watch: {
        expense(newExpense) {
            this.expenseData = newExpense || {
                comment: '',
                date: '',
                sum: ''
            };
        }
    },
    methods: {
        closeModal() {
            this.expenseData = {
                comment: '',
                date: '',
                sum: ''
            }
            this.$emit('close');
        },
        async submit() {
            // выбераем метод отправки запроса если есть id то отправляем patch на изменения если нет то отправляем post
            const method = this.expenseData.id ? axios.patch : axios.post;
            // меняем url если есть id 
            const url = this.expenseData.id ? `http://localhost/expense/${this.expenseData.id}` : 'http://localhost/expense';
            // добавляем header если отправляем post запрос
            const headers = this.expenseData.id ? {} : { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
            const toast = useToast();
            if (this.expenseData.comment.trim() === '') {
                toast.error('Comment is required.');
                return
            }
            this.expenseData.date = this.formatDateTime(this.expenseData.date)
            // отправляем запрос на обновление или создание 
            try {
                const response = await method(url, this.expenseData, headers);

                if (response.data.success) {
                    toast.success(response.data.notification.title);
                    this.$emit('submit');
                    this.closeModal()
                } else {
                    toast.error(response.data.notification.title);
                }
            } catch (error) {
                toast.error(error);
            }
            


        },
        // форматирование даты из YYYY-MM-DDTHH:MM в YYYY-MM-DD HH:MM
        formatDateTime(dateStr) {
            let date = new Date(dateStr);

            let year = date.getFullYear();
            let month = ("0" + (date.getMonth() + 1)).slice(-2); 
            let day = ("0" + date.getDate()).slice(-2); 
            let hours = ("0" + date.getHours()).slice(-2);
            let minutes = ("0" + date.getMinutes()).slice(-2);

            let formattedDate = `${year}-${month}-${day} ${hours}:${minutes}`;
            return formattedDate;
        }
    }
}
</script>