<template>
    <ModalWindow>
        <h2>удалить позицию {{expense.comment}}</h2>
        <button @click="deleteExepnse(expense.id)">Да</button> <button @click="$emit('close')">нет</button>
    </ModalWindow>
</template>
  
<script>
import axios from 'axios';
import ModalWindow from './ModalWindow.vue';
import { useToast } from 'vue-toastification';

export default {
    name: 'DeleteExpense',
    components: {
        ModalWindow
    },
    props: ['expense'],
    methods: {
        async deleteExepnse(id) {
            const toast = useToast();
            if (id != '') {
                try {
                    const url = `http://localhost/expense/${id}`;
                    const response = await axios.delete(url);
                    // Отправка запроса на удаление 
                    if (response.data.success) {
                        toast.success(response.data.notification.title);
                        this.$emit('submit');
                        this.$emit('close')
                    } else {
                        toast.error(response.data.notification.title);
                    
                    }
                } catch (error) {
                    toast.error(error);
                }
                
            }
        }
    }
}
</script>
<style scoped>
    button{
        width: 49%;
    }
    
</style>