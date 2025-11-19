import { ref } from 'vue';
import axios from 'axios';

export function useApi() {
    const loading = ref(false);
    const error = ref(null);

    const handleRequest = async (requestFn) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await requestFn();
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || err.message || 'An error occurred';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const get = async (url, config = {}) => {
        return handleRequest(() => axios.get(url, config));
    };

    const post = async (url, data = {}, config = {}) => {
        return handleRequest(() => axios.post(url, data, config));
    };

    const put = async (url, data = {}, config = {}) => {
        return handleRequest(() => axios.put(url, data, config));
    };

    const patch = async (url, data = {}, config = {}) => {
        return handleRequest(() => axios.patch(url, data, config));
    };

    const destroy = async (url, config = {}) => {
        return handleRequest(() => axios.delete(url, config));
    };

    return {
        loading,
        error,
        get,
        post,
        put,
        patch,
        destroy,
    };
}
