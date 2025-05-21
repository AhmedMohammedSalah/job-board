<template>
    <div v-if="replies.length > 0" class="replies ms-4 mt-3">
        <div v-for="reply in replies" :key="reply.id" class="reply mb-3 pb-3 border-bottom">
            <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                    <img :src="reply.user.avatar || '/images/default-avatar.png'" class="rounded-circle" width="40"
                        height="40" alt="User avatar">
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="mb-0 fw-bold">{{ reply.user.name }}</h6>
                        <small class="text-muted">{{ formatDate(reply.created_at) }}</small>
                    </div>
                    <p class="mb-2">{{ reply.content }}</p>

                    <div v-if="reply.user_id === currentUser?.id" class="d-flex align-items-center gap-2">
                        <button @click="$emit('edit-reply', reply)" class="btn btn-sm btn-outline-primary">
                            Edit
                        </button>
                        <button @click="$emit('delete-comment', reply.id)" class="btn btn-sm btn-outline-danger">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RepliesSection',
    props: {
        replies: {
            type: Array,
            default: () => []
        },
        currentUser: {
            type: Object,
            default: null
        }
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }
    }
}
</script>

<style scoped>
.replies {
    border-left: 2px solid #eee;
    padding-left: 1rem;
}
</style>