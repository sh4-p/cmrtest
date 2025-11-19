import { ref } from 'vue';

const notifications = ref([]);
let nextId = 1;

export function useNotification() {
    const add = (message, type = 'info', duration = 5000) => {
        const id = nextId++;
        const notification = {
            id,
            message,
            type, // 'success', 'error', 'warning', 'info'
            duration,
        };

        notifications.value.push(notification);

        if (duration > 0) {
            setTimeout(() => {
                remove(id);
            }, duration);
        }

        return id;
    };

    const remove = (id) => {
        const index = notifications.value.findIndex((n) => n.id === id);
        if (index > -1) {
            notifications.value.splice(index, 1);
        }
    };

    const success = (message, duration = 5000) => {
        return add(message, 'success', duration);
    };

    const error = (message, duration = 7000) => {
        return add(message, 'error', duration);
    };

    const warning = (message, duration = 6000) => {
        return add(message, 'warning', duration);
    };

    const info = (message, duration = 5000) => {
        return add(message, 'info', duration);
    };

    const clear = () => {
        notifications.value = [];
    };

    return {
        notifications,
        add,
        remove,
        success,
        error,
        warning,
        info,
        clear,
    };
}
