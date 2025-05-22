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

                    <div v-show="canEditOrDeleteReply(reply) && editingCommentId !== reply.id"
                        class="d-flex align-items-center gap-2">
                        <button @click="handleEdit(reply)" class="btn btn-sm btn-outline-primary">
                            Edit
                        </button>
                        <button @click="handleDelete(reply.id)" class="btn btn-sm btn-outline-danger">
                            Delete
                        </button>
                    </div>

                    <div v-if="editingCommentId === reply.id" class="mb-3 p-3 bg-white rounded shadow-sm">
                        <form @submit.prevent="updateReply(reply.id)">
                            <div class="form-group mb-2">
                                <textarea v-model="editContentLocal" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="d-flex gap-2 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                <button @click="cancelEdit" type="button" class="btn btn-outline-secondary btn-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
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
        localUser: {
            type: Object,
            default: null
        },
        editingCommentId: {
            type: [Number, String],
            default: null
        }
    },
    data() {
        return {
            editContentLocal: ''
        };
    },
    watch: {
        editingCommentId(newVal) {
            if (newVal) {
                const replyToEdit = this.replies.find(r => r.id === newVal);
                if (replyToEdit) {
                    this.editContentLocal = replyToEdit.content;
                }
            } else {
                this.editContentLocal = '';
            }
        }
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const options = { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        },
        canEditOrDeleteReply(reply) {
            const currentUserId = this.localUser ? Number(this.localUser.id) : null;
            const replyUserId = reply.user_id ? Number(reply.user_id) : null;
            return currentUserId !== null && replyUserId === currentUserId;
        },
        handleEdit(reply) {
            this.$emit('edit-reply', reply);
        },
        handleDelete(replyId) {
            this.$emit('delete-reply', replyId);
        },
        updateReply(replyId) {
            this.$emit('update-reply', replyId, this.editContentLocal);
        },
        cancelEdit() {
            this.$emit('edit-reply', null);
        }
    }
}
</script>

<style scoped>
.replies {
    border-left: 2px solid #ccc;
    padding-left: 1rem;
}
</style>