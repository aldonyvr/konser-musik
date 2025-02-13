import { ref, onMounted } from 'vue';
import axios from '@/libs/axios';

export function useKonser() {
    const data = ref([]);
    const isLoading = ref(true);
    const error = ref(null);

    const fetchKonser = async () => {
        try {
            isLoading.value = true;
            const response = await axios.get('/getKonser');
            data.value = response.data.map(konser => ({
                id: konser.id,
                text: konser.title,
                value: konser.id
            }));
        } catch (err) {
            error.value = err;
            console.error('Error fetching konser:', err);
        } finally {
            isLoading.value = false;
        }
    };

    onMounted(fetchKonser);

    return {
        data,
        isLoading,
        error,
        fetchKonser
    };
}
